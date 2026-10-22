<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

/**
 * Modalidades / Tipos de compra do Método Andreia 360.
 * O método é o mesmo para todas as alunas, diferenciando-se pelo plano/tipo de compra adquirido.
 */
enum PurchaseType: string implements HasColor, HasIcon, HasLabel
{
    case Essencial = 'essencial';
    case Premium = 'premium';
    case Vip = 'vip_360';

    public function getLabel(): string
    {
        return match ($this) {
            self::Essencial => 'Plano Essencial',
            self::Premium => 'Plano Premium (Recomendado)',
            self::Vip => 'Plano VIP 360',
        };
    }

    public function getShortLabel(): string
    {
        return match ($this) {
            self::Essencial => 'Essencial',
            self::Premium => 'Premium',
            self::Vip => 'VIP 360',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Essencial => 'info',
            self::Premium => 'warning',
            self::Vip => 'success',
        };
    }

    public function getIcon(): ?Heroicon
    {
        return match ($this) {
            self::Essencial => Heroicon::Sparkles,
            self::Premium => Heroicon::Star,
            self::Vip => Heroicon::Trophy,
        };
    }

    public function price(): float
    {
        return match ($this) {
            self::Essencial => 149.90,
            self::Premium => 299.90,
            self::Vip => 499.90,
        };
    }

    public function priceInCents(): int
    {
        return (int) round($this->price() * 100);
    }

    public function formattedPrice(): string
    {
        return 'R$ ' . number_format($this->price(), 2, ',', '.');
    }

    public function description(): string
    {
        return match ($this) {
            self::Essencial => 'Treinos Método 360 + Cardápio Base 40+ e Grupo WhatsApp.',
            self::Premium => 'Método Completo + Treinos Dinâmicos + E-books de Receitas + Mentoria Quinzenal.',
            self::Vip => 'Método Completo + Suporte Individual 1 a 1 + Reuniões Mensais por Zoom (Vagas Limitadas).',
        };
    }

    public static function tryFromSlug(?string $slug): ?self
    {
        if ($slug === null) {
            return null;
        }

        return match (mb_strtolower(trim($slug))) {
            'essencial', 'plano-essencial' => self::Essencial,
            'premium', 'plano-premium' => self::Premium,
            'vip', 'vip-360', 'vip_360', 'plano-vip-360', 'plano-vip' => self::Vip,
            default => self::tryFrom($slug),
        };
    }
}
