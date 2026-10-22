<?php

declare(strict_types=1);

use App\Actions\GrantCourseAccessAction;
use App\Enums\CourseStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\OrderPaymentMethod;
use App\Enums\OrderStatus;
use App\Enums\PurchaseType;
use App\Filament\Resources\Order\Pages\ListOrders;
use App\Filament\Widgets\PurchaseTypesDistributionChart;
use App\Filament\Widgets\PurchaseTypesStatsWidget;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\Andreia360CourseSeeder;
use Database\Seeders\UserSeeder;
use Filament\Facades\Filament;
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
