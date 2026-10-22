<?php

declare(strict_types=1);

use App\DTOs\Asaas\BuyerData;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Order;
use App\Models\Organization;
use App\Services\CheckoutService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

describe('T03 — Reconciliação após timeout', function () {
    beforeEach(function () {
        $this->org = Organization::factory()->create();
        $this->course = Course::factory()->create([
            'organization_id' => $this->org->id,
            'status' => CourseStatus::Published,
            'price_in_cents' => 20000,
        ]);
        $this->buyer = new BuyerData(
            name: 'Aluno Timeout',
            email: 'aluno_timeout@teste.com',
            document: '52998224725',
            phone: '11999999999',
        );
    });

    it('(a) 1º POST sofre ConnectionException; 2ª submissão faz GET, encontra 1 cobrança compatível, vincula e faz 0 novos POSTs', function () {
        $courses = new EloquentCollection([$this->course]);
        $service = app(CheckoutService::class);

        $savedOrderId = null;
        $orderNumber = null;
        $postCount = 0;

        Http::fake([
            '*/payments/*/pixQrCode' => Http::response([
                'encodedImage' => 'img_qr',
                'payload' => 'payload_qr',
                'expirationDate' => now()->addMinutes(30)->toIso8601String(),
            ]),
            '*/customers*' => Http::response(['id' => 'cus_timeout_1']),
            '*/payments*' => function (Request $request) use (&$postCount) {
                if ($request->method() === 'GET') {
                    return Http::response([
                        'totalCount' => 1,
                        'hasMore' => false,
                        'data' => [
                            [
                                'id' => 'pay_reconciled_123',
                                'customer' => 'cus_timeout_1',
                                'value' => 20000.00,
                                'billingType' => 'PIX',
                                'status' => 'PENDING',
                                'deleted' => false,
                            ],
                        ],
                    ]);
                }

                $postCount++;
                throw new ConnectionException('Connection timed out');
            },
        ]);

        $attempt1 = $service->processPixCheckout(
            $courses,
            $this->buyer,
            ownedOrderIds: [],
            onOrderCreated: function (Order $order) use (&$savedOrderId, &$orderNumber) {
                $savedOrderId = $order->id;
                $orderNumber = $order->order_number;
            }
        );

        expect($attempt1)->toBeNull()
            ->and($savedOrderId)->not->toBeNull();

        $orphanOrder = Order::find($savedOrderId);
        expect($orphanOrder->payment_id)->toBeNull();

        $attempt2 = $service->processPixCheckout(
            $courses,
            $this->buyer,
            ownedOrderIds: [$savedOrderId],
        );

        expect($attempt2)->not->toBeNull()
            ->and($attempt2->id)->toBe($savedOrderId)
            ->and($attempt2->payment_id)->toBe('pay_reconciled_123')
            ->and($attempt2->asaas_customer_id)->toBe('cus_timeout_1');

        // Zero POSTs na 2ª tentativa (apenas o 1 POST que falhou na 1ª tentativa)
        expect($postCount)->toBe(1);
    });

    it('(b) 2ª submissão com GET vazio faz 1 POST com a mesma externalReference do pedido órfão', function () {
        $courses = new EloquentCollection([$this->course]);
        $service = app(CheckoutService::class);

        $savedOrderId = null;
        $orderNumber = null;
        $postCount = 0;

        Http::fake([
            '*/payments/*/pixQrCode' => Http::response([
                'encodedImage' => 'img_qr',
                'payload' => 'payload_qr',
                'expirationDate' => now()->addMinutes(30)->toIso8601String(),
            ]),
            '*/customers*' => Http::response(['id' => 'cus_timeout_2']),
            '*/payments*' => function (Request $request) use (&$postCount) {
                if ($request->method() === 'GET') {
                    return Http::response([
                        'totalCount' => 0,
                        'hasMore' => false,
                        'data' => [],
                    ]);
                }

                $postCount++;
                if ($postCount === 1) {
                    throw new ConnectionException('Connection timed out');
                }

                return Http::response(['id' => 'pay_created_second_attempt']);
            },
        ]);

        $service->processPixCheckout(
            $courses,
            $this->buyer,
            ownedOrderIds: [],
            onOrderCreated: function (Order $order) use (&$savedOrderId, &$orderNumber) {
                $savedOrderId = $order->id;
                $orderNumber = $order->order_number;
            }
        );

        $attempt2 = $service->processPixCheckout(
            $courses,
            $this->buyer,
            ownedOrderIds: [$savedOrderId],
        );

        expect($attempt2)->not->toBeNull()
            ->and($attempt2->id)->toBe($savedOrderId)
            ->and($attempt2->payment_id)->toBe('pay_created_second_attempt');

        // Verificamos que o POST usou a mesma externalReference ($orderNumber)
        $paymentPosts = Http::recorded(function (Request $request) {
            return $request->method() === 'POST' && str_ends_with($request->url(), '/payments');
        });

        expect($paymentPosts)->toHaveCount(1)
            ->and($paymentPosts->first()[0]->data()['externalReference'])->toBe($orderNumber);
    });

    it('(c) GET com 2 cobranças retorna null e não faz nenhum POST', function () {
        $courses = new EloquentCollection([$this->course]);
        $service = app(CheckoutService::class);

        $savedOrderId = null;

        Http::fake([
            '*/customers*' => Http::response(['id' => 'cus_timeout_3']),
            '*/payments*' => function () {
                throw new ConnectionException('Connection timed out');
            },
        ]);

        $service->processPixCheckout(
            $courses,
            $this->buyer,
            ownedOrderIds: [],
            onOrderCreated: function (Order $order) use (&$savedOrderId) {
                $savedOrderId = $order->id;
            }
        );

        // 2ª tentativa: GET retorna 2 cobranças
        Http::fake([
            '*/payments?*' => Http::response([
                'totalCount' => 2,
                'hasMore' => false,
                'data' => [
                    ['id' => 'pay_dup_1', 'value' => 200.0, 'billingType' => 'PIX', 'deleted' => false],
                    ['id' => 'pay_dup_2', 'value' => 200.0, 'billingType' => 'PIX', 'deleted' => false],
                ],
            ]),
        ]);

        $attempt2 = $service->processPixCheckout(
            $courses,
            $this->buyer,
            ownedOrderIds: [$savedOrderId],
        );

        expect($attempt2)->toBeNull();

        expect(Http::recorded(function (Request $request) {
            return $request->method() === 'POST';
        }))->toHaveCount(0);
    });

    it('(d) GET com hasMore=true recusa lista incompleta, retorna null e não faz POST', function () {
        $courses = new EloquentCollection([$this->course]);
        $service = app(CheckoutService::class);

        $savedOrderId = null;

        Http::fake([
            '*/customers*' => Http::response(['id' => 'cus_timeout_4']),
            '*/payments*' => function () {
                throw new ConnectionException('Connection timed out');
            },
        ]);

        $service->processPixCheckout(
            $courses,
            $this->buyer,
            ownedOrderIds: [],
            onOrderCreated: function (Order $order) use (&$savedOrderId) {
                $savedOrderId = $order->id;
            }
        );

        // 2ª tentativa: GET com hasMore true
        Http::fake([
            '*/payments?*' => Http::response([
                'totalCount' => 105,
                'hasMore' => true,
                'data' => [
                    ['id' => 'pay_1', 'value' => 200.0, 'billingType' => 'PIX', 'deleted' => false],
                ],
            ]),
        ]);

        $attempt2 = $service->processPixCheckout(
            $courses,
            $this->buyer,
            ownedOrderIds: [$savedOrderId],
        );

        expect($attempt2)->toBeNull();

        expect(Http::recorded(function (Request $request) {
            return $request->method() === 'POST';
        }))->toHaveCount(0);
    });

    it('(e) GET com falha HTTP retorna null e não faz POST', function () {
        $courses = new EloquentCollection([$this->course]);
        $service = app(CheckoutService::class);

        $savedOrderId = null;

        Http::fake([
            '*/customers*' => Http::response(['id' => 'cus_timeout_5']),
            '*/payments*' => function () {
                throw new ConnectionException('Connection timed out');
            },
        ]);

        $service->processPixCheckout(
            $courses,
            $this->buyer,
            ownedOrderIds: [],
            onOrderCreated: function (Order $order) use (&$savedOrderId) {
                $savedOrderId = $order->id;
            }
        );

        // 2ª tentativa: GET com falha 500
        Http::fake([
            '*/payments?*' => Http::response(['error' => 'Internal server error'], 500),
        ]);

        $attempt2 = $service->processPixCheckout(
            $courses,
            $this->buyer,
            ownedOrderIds: [$savedOrderId],
        );

        expect($attempt2)->toBeNull();

        expect(Http::recorded(function (Request $request) {
            return $request->method() === 'POST';
        }))->toHaveCount(0);
    });
});
