<?php

declare(strict_types=1);

use App\Actions\GrantCourseAccessAction;
use App\Enums\CourseStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\OrderPaymentMethod;
use App\Enums\OrderStatus;
use App\Enums\PurchaseType;
use App\Filament\Checkout\Pages\Checkout;
use App\Filament\Widgets\PurchaseTypesDistributionChart;
use App\Filament\Widgets\PurchaseTypesStatsWidget;
use App\Livewire\Public\CoursePurchase;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Organization;
use App\Models\User;
use App\Services\CartService;
use Database\Seeders\Andreia360CourseSeeder;
use Database\Seeders\UserSeeder;
use Filament\Facades\Filament;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(Andreia360CourseSeeder::class);
});

it('has exactly one master course for the organization', function (): void {
    $org = Organization::where('slug', 'andreia360')->firstOrFail();
    $courses = Course::where('organization_id', $org->id)->get();

    expect($courses)->toHaveCount(1)
        ->and($courses->first()->slug)->toBe('metodo-andreia-360')
        ->and($courses->first()->title)->toBe('Método Andreia 360')
        ->and($courses->first()->status)->toBe(CourseStatus::Published);
});

it('correctly maps purchase types and prices', function (): void {
    expect(PurchaseType::Essencial->price())->toBe(149.90)
        ->and(PurchaseType::Essencial->getShortLabel())->toBe('Essencial')
        ->and(PurchaseType::Premium->price())->toBe(299.90)
        ->and(PurchaseType::Premium->getShortLabel())->toBe('Premium')
        ->and(PurchaseType::Vip->price())->toBe(499.90)
        ->and(PurchaseType::Vip->getShortLabel())->toBe('VIP 360');

    expect(PurchaseType::tryFromSlug('plano-essencial'))->toBe(PurchaseType::Essencial)
        ->and(PurchaseType::tryFromSlug('premium'))->toBe(PurchaseType::Premium)
        ->and(PurchaseType::tryFromSlug('vip-360'))->toBe(PurchaseType::Vip);
});

it('displays the chosen plan and price on the course landing page', function (): void {
    $org = Organization::where('slug', 'andreia360')->firstOrFail();
    $course = Course::where('organization_id', $org->id)->firstOrFail();

    $this->get(route('curso.show', ['cursoPublicado' => $course, 'plano' => 'essencial']))
        ->assertSuccessful()
        ->assertSee('R$ 149,90')
        ->assertSee('Essencial');

    $this->get(route('curso.show', ['cursoPublicado' => $course, 'plano' => 'vip-360']))
        ->assertSuccessful()
        ->assertSee('R$ 499,90')
        ->assertSee('VIP 360');
});

it('allows the owner to view different purchase types in the orders list', function (): void {
    $owner = User::where('email', 'andreia@metodoandreia360.com.br')->firstOrFail();
    $org = Organization::where('slug', 'andreia360')->firstOrFail();

    $this->actingAs($owner)
        ->get("/user/{$org->slug}/order/orders")
        ->assertSuccessful()
        ->assertSee('Tipo de Compra')
        ->assertSee('Plano Premium')
        ->assertSee('Plano VIP 360')
        ->assertSee('Plano Essencial');
});

it('renders the purchase types stats widget with sales breakdown', function (): void {
    $owner = User::where('email', 'andreia@metodoandreia360.com.br')->firstOrFail();
    $org = Organization::where('slug', 'andreia360')->firstOrFail();

    $this->actingAs($owner);
    Filament::setCurrentPanel(Filament::getPanel('user'));
    Filament::setTenant($org);

    Livewire::test(PurchaseTypesStatsWidget::class)
        ->assertSuccessful()
        ->assertSee('Faturamento Total')
        ->assertSee('Plano Premium (Recomendado)')
        ->assertSee('Plano VIP 360')
        ->assertSee('Plano Essencial');
});

it('renders the purchase types distribution chart widget', function (): void {
    $owner = User::where('email', 'andreia@metodoandreia360.com.br')->firstOrFail();
    $org = Organization::where('slug', 'andreia360')->firstOrFail();

    $this->actingAs($owner);
    Filament::setCurrentPanel(Filament::getPanel('user'));
    Filament::setTenant($org);

    Livewire::test(PurchaseTypesDistributionChart::class)
        ->assertSuccessful()
        ->assertSee('Distribuição das Vendas por Tipo de Compra');
});

it('propagates purchase_type from order to enrollment when access is granted', function (): void {
    $org = Organization::where('slug', 'andreia360')->firstOrFail();
    $course = Course::where('organization_id', $org->id)->firstOrFail();

    $order = Order::create([
        'organization_id' => $org->id,
        'order_number' => 'PED-TEST-VIP-01',
        'buyer_name' => 'Aluna VIP Teste',
        'buyer_email' => 'aluna.vip@exemplo.com',
        'buyer_phone' => '(11) 91111-2222',
        'buyer_document' => '11122233344',
        'total_amount' => PurchaseType::Vip->price(),
        'purchase_type' => PurchaseType::Vip,
        'payment_method' => OrderPaymentMethod::Pix,
        'payment_status' => OrderStatus::Confirmed,
        'paid_at' => now(),
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'course_id' => $course->id,
        'unit_price' => $order->total_amount,
    ]);

    $action = app(GrantCourseAccessAction::class);
    $enrollments = $action->execute($order);

    expect($enrollments)->toHaveCount(1)
        ->and($enrollments->first()->purchase_type)->toBe(PurchaseType::Vip)
        ->and($enrollments->first()->status)->toBe(EnrollmentStatus::Active);
});

it('charges the exact price of the selected plan across CoursePurchase, CartService, Checkout, and Asaas', function (PurchaseType $plan): void {
    Http::fake([
        '*/customers*' => Http::response(['id' => 'cus_plan_test', 'totalCount' => 1, 'data' => [['id' => 'cus_plan_test']]]),
        '*/payments/*/pixQrCode' => Http::response([
            'encodedImage' => 'qr_base64_img',
            'payload' => '000201pixpayload',
            'expirationDate' => now()->addMinutes(30)->toIso8601String(),
        ]),
        '*/payments' => Http::response(['id' => "pay_{$plan->value}"]),
    ]);

    $org = Organization::where('slug', 'andreia360')->firstOrFail();
    $course = Course::where('organization_id', $org->id)->firstOrFail();

    $cart = app(CartService::class);
    $cart->clear();

    Livewire::test(CoursePurchase::class, ['course' => $course, 'plan' => $plan])
        ->callAction('buyAction')
        ->assertRedirect(Checkout::getUrl(panel: 'checkout'));

    expect($cart->total())->toBe($plan->price())
        ->and($cart->getPurchaseTypeForCourse($course))->toBe($plan);

    Filament::setCurrentPanel(Filament::getPanel('checkout'));

    Livewire::test(Checkout::class)
        ->set('data.name', 'Maria Silva')
        ->set('data.email', "maria.{$plan->value}@exemplo.com")
        ->set('data.document', '52998224725')
        ->set('data.phone', '11999998888')
        ->callAction('gerarCobranca');

    $order = Order::query()
        ->where('buyer_email', "maria.{$plan->value}@exemplo.com")
        ->latest('id')
        ->first();

    expect($order)->not->toBeNull()
        ->and($order->purchase_type)->toBe($plan)
        ->and((float) $order->total_amount)->toBe($plan->price())
        ->and($order->payment_id)->toBe("pay_{$plan->value}")
        ->and((float) $order->items()->firstOrFail()->unit_price)->toBe($plan->price());

    Http::assertSent(function (Request $request) use ($plan): bool {
        if ($request->method() !== 'POST' || ! str_ends_with($request->url(), '/payments')) {
            return false;
        }

        $body = $request->data();

        return ($body['billingType'] ?? null) === 'PIX'
            && abs(((float) ($body['value'] ?? 0)) - $plan->price()) < 0.01;
    });
})->with([
    'essencial' => [PurchaseType::Essencial],
    'premium' => [PurchaseType::Premium],
    'vip' => [PurchaseType::Vip],
]);

it('updates the cart item price and plan when the visitor switches plan for the same course', function (): void {
    $org = Organization::where('slug', 'andreia360')->firstOrFail();
    $course = Course::where('organization_id', $org->id)->firstOrFail();

    $cart = app(CartService::class);
    $cart->clear();

    $cart->add($course, 1, PurchaseType::Essencial);
    expect($cart->total())->toBe(PurchaseType::Essencial->price())
        ->and($cart->getPurchaseTypeForCourse($course))->toBe(PurchaseType::Essencial);

    $cart->add($course, 1, PurchaseType::Vip);
    expect($cart->count())->toBe(1)
        ->and($cart->total())->toBe(PurchaseType::Vip->price())
        ->and($cart->getPurchaseTypeForCourse($course))->toBe(PurchaseType::Vip);
});

it('redirects to thank-you page and renders confirmation after PIX payment is approved', function (): void {
    $org = Organization::where('slug', 'andreia360')->firstOrFail();
    $course = Course::where('organization_id', $org->id)->firstOrFail();

    $order = Order::create([
        'organization_id' => $org->id,
        'order_number' => 'PED-PIX-OK-01',
        'buyer_name' => 'Fernanda Costa',
        'buyer_email' => 'fernanda.pix@exemplo.com',
        'buyer_phone' => '11988887777',
        'buyer_document' => '52998224725',
        'total_amount' => PurchaseType::Essencial->price(),
        'purchase_type' => PurchaseType::Essencial,
        'payment_method' => OrderPaymentMethod::Pix,
        'payment_status' => OrderStatus::Received,
        'payment_id' => 'pay_pix_ok_01',
        'paid_at' => now(),
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'course_id' => $course->id,
        'unit_price' => PurchaseType::Essencial->price(),
    ]);

    $this->get(route('pedido.pagamento', ['pedido' => $order->order_number]))
        ->assertSuccessful()
        ->assertSee('Pagamento confirmado')
        ->assertSee('Acessar Área da Aluna');

    $this->getJson(route('pedido.status', ['pedido' => $order->order_number]))
        ->assertOk()
        ->assertJson([
            'status' => 'received',
            'pago' => true,
            'redirect' => route('curso.obrigado', $course),
        ]);

    $this->get(route('curso.obrigado', $course))
        ->assertSuccessful()
        ->assertViewIs('public.pages.obrigado')
        ->assertSee('Sua Vaga Está')
        ->assertSee('Fernanda');

    // Mesmo sem lead_name em flash (ex.: refresh), sessao com checkout.order_ids pago mantem pagina de obrigado
    $this->withSession(['checkout.order_ids' => [$order->id]])
        ->get(route('curso.obrigado', $course))
        ->assertSuccessful()
        ->assertViewIs('public.pages.obrigado')
        ->assertSee('Fernanda');
});
