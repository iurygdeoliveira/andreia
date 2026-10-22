<?php

declare(strict_types=1);

use App\DTOs\Asaas\BuyerData;
use App\Enums\CourseStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\OrderStatus;
use App\Exceptions\CourseAlreadyActiveException;
use App\Filament\Checkout\Pages\Checkout;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Organization;
use App\Models\User;
use App\Services\CartService;
use App\Services\CheckoutService;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

describe('T06 — Bloqueio de recompra ativa e mensagem privada', function () {
    beforeEach(function () {
        RateLimiter::clear('checkout-pix-rate-limit:127.0.0.1');

        $this->org = Organization::factory()->create();
        $this->courseA = Course::factory()->create([
            'organization_id' => $this->org->id,
            'title' => 'Curso de Biomecânica',
            'status' => CourseStatus::Published,
            'price_in_cents' => 100.0,
        ]);
        $this->courseB = Course::factory()->create([
            'organization_id' => $this->org->id,
            'title' => 'Curso de Fisiologia',
            'status' => CourseStatus::Published,
            'price_in_cents' => 120.0,
        ]);
        $this->user = User::factory()->create([
            'email' => 'aluno_ativo@teste.com',
        ]);
        $this->buyer = new BuyerData(
            name: $this->user->name,
            email: $this->user->email,
            document: '52998224725',
            phone: '11999999999',
        );
    });

    it('bloqueia compra de curso com matrícula ativa e vigente lançando CourseAlreadyActiveException sem HTTP nem pedidos', function () {
        Enrollment::factory()->create([
            'organization_id' => $this->org->id,
            'user_id' => $this->user->id,
            'course_id' => $this->courseA->id,
            'status' => EnrollmentStatus::Active,
            'expires_at' => now()->addMonths(6),
        ]);

        $service = app(CheckoutService::class);
        $courses = new EloquentCollection([$this->courseA]);

        expect(fn () => $service->processPixCheckout($courses, $this->buyer))
            ->toThrow(CourseAlreadyActiveException::class);

        expect(Order::count())->toBe(0);
        Http::assertNothingSent();
    });

    it('bloqueia carrinho misto inteiro se qualquer curso já estiver ativo', function () {
        Enrollment::factory()->create([
            'organization_id' => $this->org->id,
            'user_id' => $this->user->id,
            'course_id' => $this->courseA->id,
            'status' => EnrollmentStatus::Active,
            'expires_at' => now()->addMonths(6),
        ]);

        $service = app(CheckoutService::class);
        $courses = new EloquentCollection([$this->courseA, $this->courseB]);

        try {
            $service->processPixCheckout($courses, $this->buyer);
            $this->fail('Deveria ter lançado CourseAlreadyActiveException');
        } catch (CourseAlreadyActiveException $e) {
            expect($e->getCourses()->pluck('id')->all())->toBe([$this->courseA->id]);
        }

        expect(Order::count())->toBe(0);
        Http::assertNothingSent();
    });

    it('permite compra quando matrícula estiver Cancelled ou expirada no passado', function () {
        Enrollment::factory()->create([
            'organization_id' => $this->org->id,
            'user_id' => $this->user->id,
            'course_id' => $this->courseA->id,
            'status' => EnrollmentStatus::Cancelled,
            'expires_at' => now()->subDay(),
        ]);

        Http::fake([
            '*/payments/*/pixQrCode' => Http::response([
                'encodedImage' => 'img_qr',
                'payload' => 'payload_qr',
                'expirationDate' => now()->addMinutes(30)->toIso8601String(),
            ]),
            '*/customers*' => Http::response(['id' => 'cus_t06_1', 'totalCount' => 1, 'data' => [['id' => 'cus_t06_1']]]),
            '*/payments*' => Http::response(['id' => 'pay_t06_1']),
        ]);

        $service = app(CheckoutService::class);
        $courses = new EloquentCollection([$this->courseA]);

        $order = $service->processPixCheckout($courses, $this->buyer);

        expect($order)->not->toBeNull()
            ->and(Order::count())->toBe(1);
    });

    it('bloqueia quando matrícula ativa tiver expires_at nulo', function () {
        Enrollment::factory()->create([
            'organization_id' => $this->org->id,
            'user_id' => $this->user->id,
            'course_id' => $this->courseA->id,
            'status' => EnrollmentStatus::Active,
            'expires_at' => null,
        ]);

        $service = app(CheckoutService::class);
        $courses = new EloquentCollection([$this->courseA]);

        expect(fn () => $service->processPixCheckout($courses, $this->buyer))
            ->toThrow(CourseAlreadyActiveException::class);
    });

    it('aluno autenticado comprando com o próprio e-mail recebe notificação com os títulos dos cursos e carrinho preservado', function () {
        Enrollment::factory()->create([
            'organization_id' => $this->org->id,
            'user_id' => $this->user->id,
            'course_id' => $this->courseA->id,
            'status' => EnrollmentStatus::Active,
            'expires_at' => now()->addMonths(6),
        ]);

        $this->actingAs($this->user);

        $cart = app(CartService::class);
        $cart->clear();
        $cart->add($this->courseA);

        Filament::setCurrentPanel(Filament::getPanel('checkout'));

        Livewire::test(Checkout::class)
            ->set('data.name', $this->user->name)
            ->set('data.email', $this->user->email)
            ->set('data.document', '52998224725')
            ->set('data.phone', '11999999999')
            ->callAction('gerarCobranca')
            ->assertNotified(
                Notification::make()
                    ->title('Cursos já ativos')
                    ->body("Você já tem acesso ativo a: {$this->courseA->title}. Remova-os do carrinho para continuar.")
                    ->warning()
            );

        expect($cart->count())->toBe(1);
    });

    it('visitante deslogado usando e-mail de aluno recebe mensagem neutra sem títulos dos cursos', function () {
        Enrollment::factory()->create([
            'organization_id' => $this->org->id,
            'user_id' => $this->user->id,
            'course_id' => $this->courseA->id,
            'status' => EnrollmentStatus::Active,
            'expires_at' => now()->addMonths(6),
        ]);

        $cart = app(CartService::class);
        $cart->clear();
        $cart->add($this->courseA);

        Filament::setCurrentPanel(Filament::getPanel('checkout'));

        Livewire::test(Checkout::class)
            ->set('data.name', 'Visitante Qualquer')
            ->set('data.email', $this->user->email)
            ->set('data.document', '52998224725')
            ->set('data.phone', '11999999999')
            ->callAction('gerarCobranca')
            ->assertNotified(
                Notification::make()
                    ->title('Não foi possível concluir a compra')
                    ->body('Não foi possível concluir a compra com este e-mail. Se você já é aluno, entre na sua conta para ver seus cursos.')
                    ->warning()
            );

        expect($cart->count())->toBe(1);
    });

    it('pedido pendente antigo não é retomado se a matrícula do curso ficou ativa posteriormente', function () {
        $courses = new EloquentCollection([$this->courseA]);

        // Cria pedido pendente no passado
        $oldPendingOrder = Order::factory()->withCourses($this->courseA)->create([
            'organization_id' => $this->org->id,
            'buyer_email' => $this->buyer->email,
            'buyer_document' => $this->buyer->document,
            'payment_status' => OrderStatus::Pending,
            'payment_id' => 'pay_old_pending',
            'created_at' => now()->subMinutes(10),
        ]);

        // Posteriormente a matrícula do curso ficou ativa
        Enrollment::factory()->create([
            'organization_id' => $this->org->id,
            'user_id' => $this->user->id,
            'course_id' => $this->courseA->id,
            'status' => EnrollmentStatus::Active,
            'expires_at' => now()->addMonths(6),
        ]);

        $service = app(CheckoutService::class);

        expect(fn () => $service->processPixCheckout($courses, $this->buyer, ownedOrderIds: [$oldPendingOrder->id]))
            ->toThrow(CourseAlreadyActiveException::class);
    });
});
