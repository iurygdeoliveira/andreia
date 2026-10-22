<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EnrollmentStatus: string implements HasColor, HasLabel
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Active => 'Ativa',
            self::Inactive => 'Inativa',
            self::Expired => 'Expirada',
            self::Cancelled => 'Cancelada',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'gray',
            self::Expired => 'warning',
            self::Cancelled => 'danger',
        };
    }
}
