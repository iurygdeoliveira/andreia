<?php

declare(strict_types=1);

namespace App\DTOs\Asaas;

final readonly class PixChargeResult
{
    public function __construct(
        public string $paymentId,
        public string $qrCodeImage,
        public string $qrCodePayload,
        public ?string $expirationDate = null,
    ) {}
}
