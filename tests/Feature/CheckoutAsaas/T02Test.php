<?php

declare(strict_types=1);

use App\DTOs\Asaas\BuyerData;
use App\Enums\CourseStatus;
use App\Enums\OrderStatus;
use App\Filament\Checkout\Pages\Checkout;
use App\Models\Course;
use App\Models\Order;
use App\Models\Organization;
use App\Services\CartService;
use App\Services\CheckoutService;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

describe('T02 — Retomada autorizada pela sessão', function () {
    beforeEach(function () {
        $this->org = Organization::factory()->create();
        $this->course = Course::factory()->create([
            'organization_id' => $this->org->id,
            'status' => CourseStatus::Published,
            'price_in_cents' => 15000,
        ]);
        $this->buyer = new BuyerData(
            name: 'Comprador Teste',
            email: 'comprador@teste.com',
            document: '52998224725',
            phone: '11999999999',
        );
    });

    it('sessão B não retoma pedido da sessão A mesmo com mesmo e-mail, CPF e cursos', function () {
        Http::fake([
            '*/payments/*/pixQrCode' => Http::response([
                'encodedImage' => 'img_qr',
                'payload' => 'payload_qr',
                'expirationDate' => now()->addMinutes(30)->toIso8601String(),
            ]),
            '*/customers*' => Http::response(['id' => 'cus_123', 'totalCount' => 1, 'data' => [['id' => 'cus_123']]]),
            '*/payments*' => function () {
                return Http::response(['id' => 'pay_'.uniqid()]);
            },
        ]);

        $service = app(CheckoutService::class);
        $courses = new EloquentCollection([$this->course]);

        // Sessão A cria o pedido
        $orderA = $service->processPixCheckout($courses, $this->buyer, ownedOrderIds: []);
        expect($orderA)->not->toBeNull();

        // Sessão B tenta checkout com os mesmos cursos e comprador, mas sem posse do pedido A
        $orderB = $service->processPixCheckout($courses, $this->buyer, ownedOrderIds: []);
        expect($orderB)->not->toBeNull()
            ->and($orderB->id)->not->toBe($orderA->id)
            ->and($orderB->order_number)->not->toBe($orderA->order_number);
    });

    it('mesma sessão submetendo duas vezes retoma o mesmo pedido com 1 único POST de pagamento', function () {
        Http::fake([
            '*/payments/*/pixQrCode' => Http::response([
                'encodedImage' => 'img_qr',
                'payload' => 'payload_qr',
                'expirationDate' => now()->addMinutes(30)->toIso8601String(),
            ]),
            '*/customers*' => Http::response(['id' => 'cus_123', 'totalCount' => 1, 'data' => [['id' => 'cus_123']]]),
            '*/payments*' => Http::response(['id' => 'pay_123']),
        ]);

        $service = app(CheckoutService::class);
        $courses = new EloquentCollection([$this->course]);

        // Primeira submissão
        $order1 = $service->processPixCheckout($courses, $this->buyer, ownedOrderIds: []);
        expect($order1)->not->toBeNull();

        // Segunda submissão com o ID na sessão
        $order2 = $service->processPixCheckout($courses, $this->buyer, ownedOrderIds: [$order1->id]);
        expect($order2)->not->toBeNull()
            ->and($order2->id)->toBe($order1->id);

        expect(Http::recorded(function (Request $request) {
            return str_contains($request->url(), '/payments') && $request->method() === 'POST';
        }))->toHaveCount(1);
    });

    it('pedido pago nunca é retomado', function () {
        Http::fake([
            '*/payments/*/pixQrCode' => Http::response([
                'encodedImage' => 'img_qr',
                'payload' => 'payload_qr',
                'expirationDate' => now()->addMinutes(30)->toIso8601String(),
            ]),
            '*/customers*' => Http::response(['id' => 'cus_123', 'totalCount' => 1, 'data' => [['id' => 'cus_123']]]),
            '*/payments*' => Http::response(['id' => 'pay_456']),
        ]);

        $service = app(CheckoutService::class);
        $courses = new EloquentCollection([$this->course]);

        $orderPago = Order::factory()->withCourses($this->course)->create([
            'organization_id' => $this->org->id,
            'buyer_email' => $this->buyer->email,
            'buyer_document' => $this->buyer->document,
            'payment_status' => OrderStatus::Received,
            'paid_at' => now(),
        ]);

        $orderNovo = $service->processPixCheckout($courses, $this->buyer, ownedOrderIds: [$orderPago->id]);

        expect($orderNovo)->not->toBeNull()
            ->and($orderNovo->id)->not->toBe($orderPago->id);
    });

    it('callback onOrderCreated é invocado após criação no banco e antes de HTTP', function () {
        Http::fake([
            '*/customers*' => Http::response(['error' => 'Server error'], 500),
        ]);

        $service = app(CheckoutService::class);
        $courses = new EloquentCollection([$this->course]);

        $createdOrderId = null;
        $order = $service->processPixCheckout(
            $courses,
            $this->buyer,
            ownedOrderIds: [],
            onOrderCreated: function (Order $created) use (&$createdOrderId) {
                $createdOrderId = $created->id;
            }
        );

        // O checkout falhou devido ao Asaas 500
        expect($order)->toBeNull();
        // Mas o callback onOrderCreated foi chamado e gravou o id criado
        expect($createdOrderId)->not->toBeNull();
        expect(Order::find($createdOrderId))->not->toBeNull();
    });

    it('Checkout Livewire page passa ownedOrderIds da sessão e limita a 10 IDs', function () {
        Http::fake([
            '*/payments/*/pixQrCode' => Http::response([
                'encodedImage' => 'img_qr',
                'payload' => 'payload_qr',
                'expirationDate' => now()->addMinutes(30)->toIso8601String(),
            ]),
            '*/customers*' => Http::response(['id' => 'cus_123', 'totalCount' => 1, 'data' => [['id' => 'cus_123']]]),
            '*/payments*' => Http::response(['id' => 'pay_123']),
        ]);

        $cart = app(CartService::class);
        $cart->add($this->course);

        Filament::setCurrentPanel(Filament::getPanel('checkout'));

        // Pré-popular sessão com 10 IDs não colidentes
        session(['checkout.order_ids' => [101, 102, 103, 104, 105, 106, 107, 108, 109, 110]]);

        Livewire::test(Checkout::class)
            ->set('data.name', 'Nome Aluno')
            ->set('data.email', 'aluno@teste.com')
            ->set('data.document', '52998224725')
            ->set('data.phone', '11999999999')
            ->callAction('gerarCobranca');

        $sessionOrderIds = session('checkout.order_ids');
        expect($sessionOrderIds)->toBeArray()
            ->and(count($sessionOrderIds))->toBe(10)
            ->and($sessionOrderIds)->not->toContain(110); // O mais antigo saiu
    });
});
