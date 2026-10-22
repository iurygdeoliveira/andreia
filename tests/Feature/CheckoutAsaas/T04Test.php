<?php

declare(strict_types=1);

use App\Enums\CourseStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

describe('T04 — Estornos parciais e vínculo do webhook', function () {
    beforeEach(function () {
        Queue::fake();

        $this->org = Organization::factory()->create();
        $this->course = Course::factory()->create([
            'organization_id' => $this->org->id,
            'status' => CourseStatus::Published,
            'price_in_cents' => 100.0,
        ]);
        $this->order = Order::factory()->withCourses($this->course)->create([
            'organization_id' => $this->org->id,
            'buyer_email' => 'aluno_webhook@teste.com',
            'buyer_name' => 'Aluno Webhook',
            'payment_status' => OrderStatus::Pending,
            'payment_id' => 'pay_webhook_test_1',
            'paid_at' => null,
        ]);

        config(['services.asaas.webhook_token' => 'token_valido_123']);
    });

    it('rejeita webhook com token inválido com 401', function () {
        $response = $this->postJson(route('webhooks.asaas'), [
            'payment' => ['id' => $this->order->payment_id],
        ], [
            'asaas-access-token' => 'token_errado',
        ]);

        $response->assertUnauthorized();
    });

    it('ignora refunds PENDING e CANCELLED mantendo o status em received', function () {
        Http::fake([
            '*/payments/pay_webhook_test_1' => Http::response([
                'id' => 'pay_webhook_test_1',
                'externalReference' => $this->order->order_number,
                'billingType' => 'PIX',
                'status' => 'RECEIVED',
                'value' => 100.00,
                'deleted' => false,
                'refunds' => [
                    ['status' => 'PENDING', 'value' => 20.00],
                    ['status' => 'CANCELLED', 'value' => 30.00],
                ],
            ]),
        ]);

        $response = $this->postJson(route('webhooks.asaas'), [
            'payment' => ['id' => $this->order->payment_id],
        ], [
            'asaas-access-token' => 'token_valido_123',
        ]);

        $response->assertOk();

        $this->order->refresh();
        expect($this->order->payment_status)->toBe(OrderStatus::Received)
            ->and($this->order->paid_at)->not->toBeNull();

        $enrollment = Enrollment::where('course_id', $this->course->id)->first();
        expect($enrollment)->not->toBeNull()
            ->and($enrollment->status)->toBe(EnrollmentStatus::Active);
    });

    it('reconhece estorno parcial com refund DONE transformando em partially_refunded e concedendo/mantendo acesso', function () {
        Http::fake([
            '*/payments/pay_webhook_test_1' => Http::response([
                'id' => 'pay_webhook_test_1',
                'externalReference' => $this->order->order_number,
                'billingType' => 'PIX',
                'status' => 'CONFIRMED',
                'value' => 100.00,
                'deleted' => false,
                'refunds' => [
                    ['status' => 'DONE', 'value' => 40.00],
                    ['status' => 'PENDING', 'value' => 10.00],
                ],
            ]),
        ]);

        $response = $this->postJson(route('webhooks.asaas'), [
            'payment' => ['id' => $this->order->payment_id],
        ], [
            'asaas-access-token' => 'token_valido_123',
        ]);

        $response->assertOk();

        $this->order->refresh();
        expect($this->order->payment_status)->toBe(OrderStatus::PartiallyRefunded)
            ->and($this->order->paid_at)->not->toBeNull();

        $enrollment = Enrollment::where('course_id', $this->course->id)->first();
        expect($enrollment)->not->toBeNull()
            ->and($enrollment->status)->toBe(EnrollmentStatus::Active);
    });

    it('reconhece soma de refunds DONE igual ou superior ao valor total como refunded e revoga acesso', function () {
        // Aluno já tem a matrícula ativa
        $user = User::factory()->create(['email' => $this->order->buyer_email]);
        $this->order->update(['user_id' => $user->id, 'payment_status' => OrderStatus::Received, 'paid_at' => now()]);
        $enrollment = Enrollment::factory()->create([
            'organization_id' => $this->org->id,
            'user_id' => $user->id,
            'course_id' => $this->course->id,
            'status' => EnrollmentStatus::Active,
        ]);

        Http::fake([
            '*/payments/pay_webhook_test_1' => Http::response([
                'id' => 'pay_webhook_test_1',
                'externalReference' => $this->order->order_number,
                'billingType' => 'PIX',
                'status' => 'RECEIVED',
                'value' => 100.00,
                'deleted' => false,
                'refunds' => [
                    ['status' => 'DONE', 'value' => 50.00],
                    ['status' => 'DONE', 'value' => 50.00],
                ],
            ]),
        ]);

        $response = $this->postJson(route('webhooks.asaas'), [
            'payment' => ['id' => $this->order->payment_id],
        ], [
            'asaas-access-token' => 'token_valido_123',
        ]);

        $response->assertOk();

        $this->order->refresh();
        expect($this->order->payment_status)->toBe(OrderStatus::Refunded);

        $enrollment->refresh();
        expect($enrollment->status)->toBe(EnrollmentStatus::Cancelled);
    });

    it('divergência de externalReference responde 200 sem alterar pedido nem conceder matrícula', function () {
        Http::fake([
            '*/payments/pay_webhook_test_1' => Http::response([
                'id' => 'pay_webhook_test_1',
                'externalReference' => 'OUTRO_PEDIDO_123',
                'billingType' => 'PIX',
                'status' => 'CONFIRMED',
                'value' => 100.00,
                'deleted' => false,
            ]),
        ]);

        $response = $this->postJson(route('webhooks.asaas'), [
            'payment' => ['id' => $this->order->payment_id],
        ], [
            'asaas-access-token' => 'token_valido_123',
        ]);

        $response->assertOk();

        $this->order->refresh();
        expect($this->order->payment_status)->toBe(OrderStatus::Pending);

        expect(Enrollment::where('course_id', $this->course->id)->doesntExist())->toBeTrue();
    });

    it('divergência de billingType (BOLETO) responde 200 sem alterar pedido nem conceder matrícula', function () {
        Http::fake([
            '*/payments/pay_webhook_test_1' => Http::response([
                'id' => 'pay_webhook_test_1',
                'externalReference' => $this->order->order_number,
                'billingType' => 'BOLETO',
                'status' => 'CONFIRMED',
                'value' => 100.00,
                'deleted' => false,
            ]),
        ]);

        $response = $this->postJson(route('webhooks.asaas'), [
            'payment' => ['id' => $this->order->payment_id],
        ], [
            'asaas-access-token' => 'token_valido_123',
        ]);

        $response->assertOk();

        $this->order->refresh();
        expect($this->order->payment_status)->toBe(OrderStatus::Pending);

        expect(Enrollment::where('course_id', $this->course->id)->doesntExist())->toBeTrue();
    });

    it('divergência de deleted (true) responde 200 sem alterar pedido nem conceder matrícula', function () {
        Http::fake([
            '*/payments/pay_webhook_test_1' => Http::response([
                'id' => 'pay_webhook_test_1',
                'externalReference' => $this->order->order_number,
                'billingType' => 'PIX',
                'status' => 'CONFIRMED',
                'value' => 100.00,
                'deleted' => true,
            ]),
        ]);

        $response = $this->postJson(route('webhooks.asaas'), [
            'payment' => ['id' => $this->order->payment_id],
        ], [
            'asaas-access-token' => 'token_valido_123',
        ]);

        $response->assertOk();

        $this->order->refresh();
        expect($this->order->payment_status)->toBe(OrderStatus::Pending);

        expect(Enrollment::where('course_id', $this->course->id)->doesntExist())->toBeTrue();
    });

    it('divergência de valor responde 200 sem alterar pedido nem conceder matrícula', function () {
        Http::fake([
            '*/payments/pay_webhook_test_1' => Http::response([
                'id' => 'pay_webhook_test_1',
                'externalReference' => $this->order->order_number,
                'billingType' => 'PIX',
                'status' => 'CONFIRMED',
                'value' => 150.00, // Diverge dos 100.00 do pedido
                'deleted' => false,
            ]),
        ]);

        $response = $this->postJson(route('webhooks.asaas'), [
            'payment' => ['id' => $this->order->payment_id],
        ], [
            'asaas-access-token' => 'token_valido_123',
        ]);

        $response->assertOk();

        $this->order->refresh();
        expect($this->order->payment_status)->toBe(OrderStatus::Pending);

        expect(Enrollment::where('course_id', $this->course->id)->doesntExist())->toBeTrue();
    });
});
