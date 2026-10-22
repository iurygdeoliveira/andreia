<?php

declare(strict_types=1);

namespace App\DTOs\Asaas;

/**
 * Payload de criacao de cliente no Asaas.
 *
 * As chaves de toArray() usam camelCase porque sao o contrato da API do
 * Asaas, nao a convencao do projeto.
 */
final readonly class CustomerData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $cpfCnpj,
        public ?string $mobilePhone = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'cpfCnpj' => $this->cpfCnpj,
            'mobilePhone' => $this->mobilePhone,
        ], fn (?string $value): bool => $value !== null && $value !== '');
    }
}
