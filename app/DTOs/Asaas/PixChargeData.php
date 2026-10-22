<?php

declare(strict_types=1);

namespace App\DTOs\Asaas;

/**
 * Payload de criacao de cobranca PIX.
 *
 * ATENCAO (DEC-05): $value esta em REAIS, nao em centavos. O Asaas espera
 * reais como decimal, e Course::$price_in_cents ja devolve reais por causa
 * do MoneyCast, apesar do nome da coluna.
 */
final readonly class PixChargeData
{
    public function __construct(
        public string $customer,
        public float $value,
        public string $dueDate,
        public string $description,
        public string $externalReference,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'customer' => $this->customer,
            'billingType' => 'PIX',
            'value' => $this->value,
            'dueDate' => $this->dueDate,
            'description' => $this->description,
            // Amarra a cobranca ao pedido local: e por aqui que o webhook
            // reencontra o Order mesmo se algo se perder no caminho.
            'externalReference' => $this->externalReference,
        ];
    }
}
