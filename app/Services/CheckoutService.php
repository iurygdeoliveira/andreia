<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Asaas\BuyerData;
use App\DTOs\Asaas\PixChargeData;
use App\Enums\CourseStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\OrderPaymentMethod;
use App\Enums\OrderStatus;
use App\Enums\PurchaseType;
use App\Exceptions\CourseAlreadyActiveException;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lead;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;

/**
 * Orquestra o checkout PIX de um conjunto de cursos (UC-04).
 */
class CheckoutService
{
    public function __construct(
        private readonly AsaasService $asaas,
    ) {}

    /**
     * Devolve null quando a comunicação com o Asaas falha (DEC-09) ou limites são excedidos.
     *
     * O pedido pendente permanece no banco de propósito: preserva os dados
     * digitados e os Leads para retomada comercial.
     *
     * @param  Collection<int, Course>  $courses
     * @param  array<int, int|string>  $ownedOrderIds
     */
    public function processPixCheckout(
        Collection $courses,
        BuyerData $buyer,
        ?string $idempotencyKey = null,
        array $ownedOrderIds = [],
        ?callable $onOrderCreated = null,
    ): ?Order {
        if ($courses->isEmpty()) {
            return null;
        }

        // SEC-01: Rejeitar rigorosamente cursos indisponíveis ou não publicados na fronteira (RN-02, CA-07)
        $publishedCourses = Course::query()
            ->where('status', CourseStatus::Published)
            ->whereKey($courses->modelKeys())
            ->get();

        if ($publishedCourses->count() !== $courses->count()) {
            Log::warning('Tentativa de checkout com curso indisponível ou não publicado.', [
                'course_ids' => $courses->modelKeys(),
            ]);

            return null;
        }

        $courses = $publishedCourses;

        // SEC-02: Compartilhar limite de operação (RateLimiter) entre HTTP e Livewire
        $ip = request()?->ip() ?? '127.0.0.1';
        $rateLimitKey = 'checkout-pix-rate-limit:'.$ip;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            Log::warning('Limite compartilhado de geração de cobranças PIX excedido.', [
                'ip' => $ip,
                'course_ids' => $courses->modelKeys(),
                'buyer_email' => $buyer->email,
            ]);

            return null;
        }

        // RN-03: Bloquear recompra se o comprador já tiver matrícula ativa e vigente (AC-7)
        $buyerUser = User::query()->where('email', $buyer->email)->first();
        if ($buyerUser instanceof User) {
            $activeEnrollments = Enrollment::query()
                ->where('user_id', $buyerUser->getKey())
                ->whereIn('course_id', $courses->modelKeys())
                ->where('status', EnrollmentStatus::Active)
                ->where(function ($q): void {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->with('course')
                ->get();

            if ($activeEnrollments->isNotEmpty()) {
                RateLimiter::hit($rateLimitKey, 60);

                /** @var Collection<int, Course> $activeCourses */
                $activeCourses = $activeEnrollments->pluck('course')->filter();

                throw new CourseAlreadyActiveException($activeCourses);
            }
        }

        $normalizedOwnedIds = array_values(array_filter(
            array_map(fn ($id) => is_numeric($id) ? (int) $id : null, $ownedOrderIds),
            fn ($id) => $id !== null
        ));

        // SEC-02: Idempotência por conjunto canônico ordenado de cursos e comprador (ADR-0005, RF-08)
        $fingerprint = $idempotencyKey ?: $this->buildFingerprint($courses, $buyer);
        $cacheKey = "checkout:idempotency:{$fingerprint}";

        if (! empty($normalizedOwnedIds)) {
            $cachedOrderId = Cache::get($cacheKey);
            if ($cachedOrderId && in_array((int) $cachedOrderId, $normalizedOwnedIds, true)) {
                $cachedOrder = Order::query()->with(['items.course'])->find($cachedOrderId);
                if ($cachedOrder instanceof Order && $cachedOrder->payment_status === OrderStatus::Pending && $cachedOrder->payment_id !== null) {
                    return $cachedOrder;
                }
            }

            $sortedCourseIds = $courses->modelKeys();
            sort($sortedCourseIds, SORT_NUMERIC);

            $candidateOrders = Order::query()
                ->whereKey($normalizedOwnedIds)
                ->where('buyer_email', $buyer->email)
                ->where('buyer_document', $buyer->document)
                ->where('payment_status', OrderStatus::Pending)
                ->where(function ($q): void {
                    $q->where(function ($q2): void {
                        $q2->whereNotNull('payment_id')
                            ->where(function ($q3): void {
                                $q3->whereNull('payment_qr_code_expiration_date')
                                    ->orWhere('payment_qr_code_expiration_date', '>', now());
                            });
                    })->orWhere(function ($q2): void {
                        $q2->whereNull('payment_id')
                            ->where('created_at', '>=', now()->subMinutes(30));
                    });
                })
                ->with(['items.course'])
                ->latest()
                ->get();

            $recentPendingOrder = $candidateOrders->first(function (Order $order) use ($sortedCourseIds): bool {
                $orderCourseIds = $order->items->pluck('course_id')->all();
                sort($orderCourseIds, SORT_NUMERIC);

                return $orderCourseIds === $sortedCourseIds;
            });

            if ($recentPendingOrder instanceof Order && $recentPendingOrder->payment_id !== null) {
                Cache::put($cacheKey, $recentPendingOrder->getKey(), now()->addMinutes(15));

                return $recentPendingOrder;
            }
        }

        RateLimiter::hit($rateLimitKey, 60);

        // Lock atômico contra concorrência simultânea (double-click - CA-08)
        $lockKey = "checkout:lock:{$fingerprint}";

        try {
            $order = Cache::lock($lockKey, 75)->block(5, function () use ($courses, $buyer, $normalizedOwnedIds, $onOrderCreated): ?Order {
                $candidateOrder = null;
                if (! empty($normalizedOwnedIds)) {
                    $sortedCourseIds = $courses->modelKeys();
                    sort($sortedCourseIds, SORT_NUMERIC);

                    $candidates = Order::query()
                        ->whereKey($normalizedOwnedIds)
                        ->where('buyer_email', $buyer->email)
                        ->where('buyer_document', $buyer->document)
                        ->where('payment_status', OrderStatus::Pending)
                        ->where(function ($q): void {
                            $q->where(function ($q2): void {
                                $q2->whereNotNull('payment_id')
                                    ->where(function ($q3): void {
                                        $q3->whereNull('payment_qr_code_expiration_date')
                                            ->orWhere('payment_qr_code_expiration_date', '>', now());
                                    });
                            })->orWhere(function ($q2): void {
                                $q2->whereNull('payment_id')
                                    ->where('created_at', '>=', now()->subMinutes(30));
                            });
                        })
                        ->with(['items.course'])
                        ->latest()
                        ->get();

                    $candidateOrder = $candidates->first(function (Order $order) use ($sortedCourseIds): bool {
                        $orderCourseIds = $order->items->pluck('course_id')->all();
                        sort($orderCourseIds, SORT_NUMERIC);

                        return $orderCourseIds === $sortedCourseIds;
                    });
                }

                if ($candidateOrder instanceof Order && $candidateOrder->payment_id !== null) {
                    return $candidateOrder;
                }

                if ($candidateOrder instanceof Order) {
                    // Reconciliação do pedido órfão antes de qualquer POST
                    try {
                        $paymentResult = $this->asaas->findPaymentsByExternalReference($candidateOrder->order_number);
                    } catch (RequestException|ConnectionException $e) {
                        Log::error('Falha ao consultar cobranças por externalReference no Asaas para reconciliação.', [
                            'order_number' => $candidateOrder->order_number,
                            'status' => $e instanceof RequestException ? $e->response->status() : null,
                        ]);

                        return null;
                    }

                    if ($paymentResult['hasMore']) {
                        Log::error('Listagem incompleta no Asaas ao consultar externalReference; recusa conservadora.', [
                            'order_number' => $candidateOrder->order_number,
                        ]);

                        return null;
                    }

                    $items = $paymentResult['items'];

                    if (count($items) === 1) {
                        $charge = $items[0];
                        $chargeValueCents = (int) round(((float) $charge['value']) * 100);
                        $orderValueCents = (int) round(((float) $candidateOrder->total_amount) * 100);

                        if (($charge['billingType'] ?? '') === 'PIX' && $chargeValueCents === $orderValueCents) {
                            $candidateOrder->update([
                                'payment_id' => $charge['id'],
                                'asaas_customer_id' => $charge['customer'] ?? $candidateOrder->asaas_customer_id,
                            ]);

                            return $candidateOrder;
                        }

                        Log::error('Cobrança encontrada no Asaas diverge do pedido órfão.', [
                            'order_number' => $candidateOrder->order_number,
                            'charge_id' => $charge['id'],
                            'billing_type' => $charge['billingType'] ?? null,
                            'charge_value' => $charge['value'],
                            'order_value' => $candidateOrder->total_amount,
                        ]);

                        return null;
                    }

                    if (count($items) > 1) {
                        Log::error('Múltiplas cobranças encontradas no Asaas para a mesma externalReference.', [
                            'order_number' => $candidateOrder->order_number,
                            'charge_ids' => array_column($items, 'id'),
                        ]);

                        return null;
                    }

                    // Zero cobranças no Asaas: aproveita o mesmo pedido órfão para emitir a cobrança
                    $currentTotal = (float) $courses->sum(fn (Course $course): float => (float) $course->price_in_cents);
                    if ($candidateOrder->total_amount != $currentTotal) {
                        $candidateOrder->update(['total_amount' => $currentTotal]);
                        foreach ($courses as $course) {
                            $candidateOrder->items()->where('course_id', $course->getKey())->update([
                                'unit_price' => $course->price_in_cents,
                            ]);
                        }
                    }
                    $order = $candidateOrder;
                } else {
                    $order = $this->createPendingOrder($courses, $buyer);

                    if ($onOrderCreated !== null) {
                        $onOrderCreated($order);
                    }
                }

                try {
                    $customerId = $this->asaas->findOrCreateCustomer($buyer->toCustomerData());

                    $paymentId = $this->asaas->createPixCharge(new PixChargeData(
                        customer: $customerId,
                        // Preço total dos cursos no banco, nunca do request (DEC-05, CA-03).
                        value: $order->total_amount,
                        dueDate: now()->addDay()->format('Y-m-d'),
                        description: $this->buildChargeDescription($courses),
                        externalReference: $order->order_number,
                    ));
                } catch (RequestException|ConnectionException $e) {
                    Log::error('Falha ao gerar cobrança PIX no Asaas.', [
                        'order_number' => $order->order_number,
                        'course_ids' => $courses->modelKeys(),
                        'status' => $e instanceof RequestException ? $e->response->status() : null,
                        'response' => $e instanceof RequestException ? $e->response->json() : null,
                    ]);

                    return null;
                }

                $order->update([
                    'asaas_customer_id' => $customerId,
                    'payment_id' => $paymentId,
                ]);

                return $order;
            });

            if ($order instanceof Order) {
                $this->recoverPixQrCode($order);

                $freshOrder = $order->fresh(['items.course']);

                if ($freshOrder instanceof Order) {
                    Cache::put($cacheKey, $freshOrder->getKey(), now()->addMinutes(30));

                    return $freshOrder;
                }

                return $order;
            }

            return null;
        } catch (LockTimeoutException) {
            Log::warning('Timeout ao adquirir lock de idempotência para o checkout.', [
                'lock_key' => $lockKey,
                'course_ids' => $courses->modelKeys(),
            ]);

            return null;
        }
    }

    public function recoverPixQrCode(Order $order): void
    {
        if ($order->grantsAccess() || ! $order->payment_id || $order->payment_qr_code_payload) {
            return;
        }

        try {
            $charge = $this->asaas->getPixQrCode($order->payment_id);
        } catch (RequestException|ConnectionException $e) {
            Log::warning('QR Code indisponível; cobrança preservada para nova consulta.', [
                'order_number' => $order->order_number,
                'status' => $e instanceof RequestException ? $e->response->status() : null,
            ]);

            return;
        }

        $order->update([
            'payment_qr_code_image' => $charge->qrCodeImage,
            'payment_qr_code_payload' => $charge->qrCodePayload,
            // Expiração informada pelo Asaas; 30 min só como fallback, para
            // não anunciar ao comprador um prazo que o gateway não pratica.
            'payment_qr_code_expiration_date' => $charge->expirationDate !== null
                ? Date::parse($charge->expirationDate)
                : now()->addMinutes(30),
        ]);
    }

    /**
     * Ordenação canônica para geração do fingerprint de idempotência (ADR-0005).
     *
     * @param  Collection<int, Course>  $courses
     */
    private function buildFingerprint(Collection $courses, BuyerData $buyer): string
    {
        $courseIds = $courses->modelKeys();
        sort($courseIds, SORT_NUMERIC);

        $cleanDoc = (string) preg_replace('/\D/', '', (string) $buyer->document);

        return hash('sha256', 'courses:'.implode(',', $courseIds)."|buyer:{$buyer->email}|doc:{$cleanDoc}");
    }

    /**
     * Descrição única da cobrança para N cursos agregados (RF-03).
     *
     * @param  Collection<int, Course>  $courses
     */
    private function buildChargeDescription(Collection $courses): string
    {
        $description = $courses->count() === 1
            ? (string) $courses->first()?->title
            : $courses->count().' cursos: '.$courses->pluck('title')->implode(', ');

        return mb_substr($description, 0, 255);
    }

    /**
     * Cria o pedido com itens e leads em uma transação única (RNF-01, DEC-08, AD-004).
     *
     * @param  Collection<int, Course>  $courses
     */
    private function createPendingOrder(Collection $courses, BuyerData $buyer): Order
    {
        return DB::transaction(function () use ($courses, $buyer): Order {
            // AD-004: um Lead por curso preserva o funil de quem se inscreveu em cada curso.
            $leads = $courses->map(fn (Course $course) => Lead::updateOrCreate(
                ['course_id' => $course->getKey(), 'email' => $buyer->email],
                [
                    'name' => $buyer->name,
                    'phone' => $buyer->phone,
                    'profile' => $buyer->profile,
                ],
            ));

            /** @var Course $firstCourse */
            $firstCourse = $courses->first();

            // DEC-05: total_amount é a soma dos preços no banco (MoneyCast expõe reais)
            $totalAmount = (float) $courses->sum(fn (Course $course): float => (float) $course->price_in_cents);

            $orderData = [
                'organization_id' => $firstCourse->organization_id,
                'lead_id' => $leads->first()?->getKey(),
                'buyer_name' => $buyer->name,
                'buyer_email' => $buyer->email,
                'buyer_phone' => $buyer->phone,
                'buyer_document' => $buyer->document,
                'total_amount' => $totalAmount,
                'purchase_type' => match (true) {
                    abs($totalAmount - 149.90) < 0.01 => PurchaseType::Essencial,
                    abs($totalAmount - 499.90) < 0.01 => PurchaseType::Vip,
                    default => PurchaseType::Premium,
                },
                'payment_method' => OrderPaymentMethod::Pix,
                'payment_status' => OrderStatus::Pending,
            ];

            if (Schema::hasColumn('orders', 'course_id')) {
                $orderData['course_id'] = $firstCourse->getKey();
            }

            $order = Order::create($orderData);

            foreach ($courses as $course) {
                $order->items()->create([
                    'course_id' => $course->getKey(),
                    'unit_price' => $course->price_in_cents,
                ]);
            }

            return $order;
        });
    }
}
