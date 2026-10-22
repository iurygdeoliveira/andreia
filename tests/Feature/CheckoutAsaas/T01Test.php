<?php

declare(strict_types=1);

use App\Actions\GrantCourseAccessAction;
use App\Enums\CourseStatus;
use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Order;
use App\Models\Organization;
use Illuminate\Support\Facades\Queue;

describe('T01 — Fundação dos status de acesso', function () {
    it('OrderStatus diferencia isPaid de grantsAccess e lista accessGranting', function () {
        expect(OrderStatus::Received->isPaid())->toBeTrue()
            ->and(OrderStatus::Confirmed->isPaid())->toBeTrue()
            ->and(OrderStatus::PartiallyRefunded->isPaid())->toBeFalse()
            ->and(OrderStatus::Pending->isPaid())->toBeFalse()
            ->and(OrderStatus::Refunded->isPaid())->toBeFalse();

        expect(OrderStatus::Received->grantsAccess())->toBeTrue()
            ->and(OrderStatus::Confirmed->grantsAccess())->toBeTrue()
            ->and(OrderStatus::PartiallyRefunded->grantsAccess())->toBeTrue()
            ->and(OrderStatus::Pending->grantsAccess())->toBeFalse()
            ->and(OrderStatus::Refunded->grantsAccess())->toBeFalse()
            ->and(OrderStatus::Cancelled->grantsAccess())->toBeFalse()
            ->and(OrderStatus::Overdue->grantsAccess())->toBeFalse();

        expect(OrderStatus::accessGranting())->toBe([
            OrderStatus::Received,
            OrderStatus::Confirmed,
            OrderStatus::PartiallyRefunded,
        ]);
    });

    it('Order delega grantsAccess para payment_status', function () {
        $org = Organization::factory()->create();
        $order = Order::factory()->create([
            'organization_id' => $org->id,
            'payment_status' => OrderStatus::PartiallyRefunded,
        ]);

        expect($order->grantsAccess())->toBeTrue()
            ->and($order->isPaid())->toBeFalse();

        $order->payment_status = OrderStatus::Pending;
        expect($order->grantsAccess())->toBeFalse();
    });

    it('OrderPaymentController devolve pago true e redirect para pedido PartiallyRefunded', function () {
        $org = Organization::factory()->create();
        $course = Course::factory()->create([
            'organization_id' => $org->id,
            'status' => CourseStatus::Published,
        ]);
        $order = Order::factory()->withCourses($course)->create([
            'organization_id' => $org->id,
            'payment_status' => OrderStatus::PartiallyRefunded,
        ]);

        $response = $this->getJson(route('pedido.status', ['pedido' => $order->order_number]));

        $response->assertOk()
            ->assertJson([
                'status' => 'partially_refunded',
                'pago' => true,
                'redirect' => route('curso.obrigado', $course),
            ]);
    });

    it('GrantCourseAccessAction autoriza pedido com status PartiallyRefunded', function () {
        Queue::fake();

        $org = Organization::factory()->create();
        $course = Course::factory()->create([
            'organization_id' => $org->id,
            'status' => CourseStatus::Published,
        ]);
        $order = Order::factory()->withCourses($course)->create([
            'organization_id' => $org->id,
            'buyer_email' => 'aluno@example.com',
            'buyer_name' => 'Aluno Teste',
            'payment_status' => OrderStatus::PartiallyRefunded,
        ]);

        $action = app(GrantCourseAccessAction::class);
        $enrollments = $action->execute($order);

        expect($enrollments)->toHaveCount(1)
            ->and($enrollments->first()->course_id)->toBe($course->id);
    });
});
