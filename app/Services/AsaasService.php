<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Asaas\CustomerData;
use App\DTOs\Asaas\PixChargeData;
use App\DTOs\Asaas\PixChargeResult;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Cliente da API do Asaas (UC-04).
 *
 * Conta unica da plataforma (DEC-02/DEC-07): as credenciais vem de
 * config/services.asaas, nao da organizacao.
 */
class AsaasService
{
    private function client(): PendingRequest
    {
        return Http::baseUrl((string) config('services.asaas.base_url'))
            ->withHeaders(['access_token' => (string) config('services.asaas.api_key')])
            ->connectTimeout(5)
            ->timeout(10)
            ->throw();
    }

    /**
     * @return array{items: array<int, array{id: string, status: string, value: float, billingType: string, customer?: string, deleted?: bool}>, hasMore: bool}
     */
    public function findPaymentsByExternalReference(string $ref): array
    {
        /** @var array{totalCount?: int, hasMore?: bool, data?: array<int, array{id: string, status: string, value: float, billingType: string, customer?: string, deleted?: bool}>} $response */
        $response = $this->client()
            ->get('/payments', [
                'externalReference' => $ref,
                'limit' => 100,
            ])
            ->json();

        $items = array_values(array_filter(
            $response['data'] ?? [],
            fn (array $item): bool => ($item['deleted'] ?? false) !== true,
        ));

        return [
            'items' => $items,
            'hasMore' => (bool) ($response['hasMore'] ?? false),
        ];
    }

    public function findCustomerByEmail(string $email): ?string
    {
        /** @var array{totalCount?: int, data?: array<int, array{id: string}>} $response */
        $response = $this->client()
            ->get('/customers', ['email' => $email])
            ->json();

        if (($response['totalCount'] ?? 0) > 0 && isset($response['data'][0]['id'])) {
            return $response['data'][0]['id'];
        }

        return null;
    }

    public function createCustomer(CustomerData $data): string
    {
        /** @var array{id: string} $response */
        $response = $this->client()
            ->post('/customers', $data->toArray())
            ->json();

        return $response['id'];
    }

    public function findOrCreateCustomer(CustomerData $data): string
    {
        return $this->findCustomerByEmail($data->email)
            ?? $this->createCustomer($data);
    }

    public function createPixCharge(PixChargeData $data): string
    {
        /** @var array{id: string} $payment */
        $payment = $this->client()
            ->post('/payments', $data->toArray())
            ->json();

        return $payment['id'];
    }

    public function getPixQrCode(string $paymentId): PixChargeResult
    {
        /** @var array{encodedImage: string, payload: string, expirationDate?: string} $qrCode */
        $qrCode = $this->client()
            ->get("/payments/{$paymentId}/pixQrCode")
            ->json();

        return new PixChargeResult(
            paymentId: $paymentId,
            qrCodeImage: $qrCode['encodedImage'],
            qrCodePayload: $qrCode['payload'],
            expirationDate: $qrCode['expirationDate'] ?? null,
        );
    }

    /**
     * Reconsulta o pagamento na origem.
     *
     * O webhook nunca confia no corpo recebido (correcao C1): o status que
     * libera o acesso ao curso e sempre o que a API do Asaas devolve aqui.
     *
     * @return array{id: string, status: string, externalReference?: string|null, value?: float, billingType?: string|null, deleted?: bool|null, refunds?: array<int, array{status: string, value: float}>}
     */
    public function getPayment(string $paymentId): array
    {
        /** @var array{id: string, status: string, externalReference?: string|null, value?: float, billingType?: string|null, deleted?: bool|null, refunds?: array<int, array{status: string, value: float}>} $response */
        $response = $this->client()
            ->get("/payments/{$paymentId}")
            ->json();

        return $response;
    }
}
