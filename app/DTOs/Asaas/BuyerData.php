<?php

declare(strict_types=1);

namespace App\DTOs\Asaas;

/**
 * Dados que o comprador anonimo informa no checkout da landing.
 *
 * Note que NAO ha valor aqui: o preco vem sempre do Course (DEC-05). Aceitar
 * valor vindo do request deixaria o comprador escolher quanto pagar.
 */
final readonly class BuyerData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
        public string $document,
        public ?string $profile = null,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated): self
    {
        return new self(
            name: (string) $validated['name'],
            email: (string) $validated['email'],
            phone: (string) $validated['phone'],
            document: preg_replace('/\D/', '', (string) $validated['document']) ?? '',
            profile: isset($validated['profile']) ? (string) $validated['profile'] : null,
        );
    }

    public function toCustomerData(): CustomerData
    {
        return new CustomerData(
            name: $this->name,
            email: $this->email,
            cpfCnpj: $this->document,
            mobilePhone: $this->phone,
        );
    }
}
