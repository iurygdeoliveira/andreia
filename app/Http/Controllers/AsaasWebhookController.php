<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\GrantCourseAccessAction;
use App\Actions\RevokeCourseAccessAction;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\AsaasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Recebe as notificacoes de cobranca do Asaas (UC-04).
 *
 * Este endpoint concede acesso a curso pago, entao as regras abaixo nao sao
 * defensividade decorativa:
 *
 * C1 - autentica pelo header asaas-access-token E reconsulta o pagamento na
 *      API. O corpo da requisicao nunca decide sozinho quem foi pago.
 * C2 - falha interna responde nao-2xx, para o Asaas reenviar.
 * C2b- pagamento desconhecido responde 200. A fila do Asaas e serial e pausa
 *      apos 15 falhas consecutivas; devolver 404 para cobrancas alheias
 *      travaria as vendas legitimas seguintes.
 * C3 - paid_at e gravado uma unica vez e nunca sobrescrito com null.
 */
final class AsaasWebhookController
{
    public function __invoke(
        Request $request,
        AsaasService $asaas,
        GrantCourseAccessAction $grantAccess,
        RevokeCourseAccessAction $revokeAccess,
    ): JsonResponse {
        if (! $this->tokenIsValid($request)) {
            Log::warning('Webhook do Asaas rejeitado por token invalido.', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
        }

        $paymentId = $request->input('payment.id');

        if (! is_string($paymentId) || $paymentId === '') {
            return $this->acknowledge('Evento sem identificador de pagamento.');
        }

        $order = Order::query()->where('payment_id', $paymentId)->first();

        if (! $order instanceof Order) {
            // C2b: cobranca que nao nasceu aqui (avulsa no painel do Asaas,
            // outro sistema na mesma conta). Nada a fazer, mas 200.
            return $this->acknowledge("Pagamento {$paymentId} nao pertence a esta plataforma.");
        }

        // C1: a fonte da verdade e a API, nao o corpo recebido. Sem isso,
        // qualquer POST com um payment.id valido liberaria o curso de graca.
        $payment = $asaas->getPayment($paymentId);

        // Validações de integridade entre a cobrança na API e o pedido local (AC-5)
        $orderValueCents = (int) round(((float) $order->total_amount) * 100);
        $paymentValueCents = (int) round(((float) ($payment['value'] ?? 0)) * 100);

        if (($payment['externalReference'] ?? null) !== $order->order_number) {
            Log::error('Divergência de externalReference no webhook do Asaas.', [
                'order_number' => $order->order_number,
                'payment_id' => $paymentId,
                'divergent_field' => 'externalReference',
            ]);

            return $this->acknowledge("Divergência de externalReference para o pagamento {$paymentId}. Pedido inalterado.");
        }

        if (($payment['billingType'] ?? null) !== 'PIX') {
            Log::error('Divergência de billingType no webhook do Asaas.', [
                'order_number' => $order->order_number,
                'payment_id' => $paymentId,
                'divergent_field' => 'billingType',
            ]);

            return $this->acknowledge("Divergência de billingType para o pagamento {$paymentId}. Pedido inalterado.");
        }

        if (($payment['deleted'] ?? false) === true) {
            Log::error('Cobrança marcada como deleted no Asaas recebida no webhook.', [
                'order_number' => $order->order_number,
                'payment_id' => $paymentId,
                'divergent_field' => 'deleted',
            ]);

            return $this->acknowledge("Cobrança deletada para o pagamento {$paymentId}. Pedido inalterado.");
        }

        if ($paymentValueCents !== $orderValueCents) {
            Log::error('Divergência de valor no webhook do Asaas.', [
                'order_number' => $order->order_number,
                'payment_id' => $paymentId,
                'divergent_field' => 'value',
            ]);

            return $this->acknowledge("Divergência de valor para o pagamento {$paymentId}. Pedido inalterado.");
        }

        $status = OrderStatus::tryFromAsaas($payment['status']);

        if (! $status instanceof OrderStatus) {
            return $this->acknowledge("Status '{$payment['status']}' nao mapeado. Pedido inalterado.");
        }

        // Se status financeiro indicar pago, verificar se há estornos (AC-4)
        if ($status->isPaid()) {
            $refunds = $payment['refunds'] ?? [];
            $refundedCents = 0;
            if (is_array($refunds)) {
                foreach ($refunds as $refund) {
                    if (($refund['status'] ?? '') === 'DONE') {
                        $refundedCents += (int) round(((float) ($refund['value'] ?? 0)) * 100);
                    }
                }
            }

            if ($refundedCents > 0 && $refundedCents < $paymentValueCents) {
                $status = OrderStatus::PartiallyRefunded;
            } elseif ($refundedCents >= $paymentValueCents && $paymentValueCents > 0) {
                $status = OrderStatus::Refunded;
            }
        }

        $this->applyStatus($order, $status);

        if ($status->grantsAccess()) {
            $grantAccess->execute($order->fresh());

            if ($status === OrderStatus::PartiallyRefunded) {
                Log::info("Estorno parcial registrado para o pedido {$order->order_number}. Matrícula mantida ativa.", [
                    'order_number' => $order->order_number,
                    'payment_id' => $paymentId,
                ]);
            }
        } elseif ($status === OrderStatus::Refunded) {
            $revokeAccess->execute($order->fresh());
        }

        return $this->acknowledge("Pedido {$order->order_number} atualizado para {$status->value}.");
    }

    private function tokenIsValid(Request $request): bool
    {
        $expected = config('services.asaas.webhook_token');

        // Token nao configurado nao pode virar "aceita qualquer coisa".
        if (! is_string($expected) || $expected === '') {
            Log::error('ASAAS_WEBHOOK_TOKEN nao configurado: webhook recusado.');

            return false;
        }

        $received = (string) $request->header('asaas-access-token', '');

        return hash_equals($expected, $received);
    }

    private function applyStatus(Order $order, OrderStatus $status): void
    {
        $attributes = ['payment_status' => $status];

        // C3: so carimba a primeira confirmacao. O filacon zera o paid_at
        // quando CONFIRMED chega depois de RECEIVED.
        if ($status->grantsAccess() && $order->paid_at === null) {
            $attributes['paid_at'] = now();
        }

        $order->update($attributes);
    }

    private function acknowledge(string $message): JsonResponse
    {
        Log::info('Webhook do Asaas processado.', ['resultado' => $message]);

        return response()->json(['message' => $message]);
    }
}
