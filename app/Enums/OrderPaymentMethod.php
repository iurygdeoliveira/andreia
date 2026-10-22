<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrderPaymentMethod: string implements HasLabel
{
    case Pix = 'pix';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pix => 'PIX',
        };
    }
}
