<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

/**
 * Espelha os status de cobranca do Asaas.
 *
 * Received e Confirmed sao ambos "pago": o Asaas envia PAYMENT_RECEIVED e
 * PAYMENT_CONFIRMED para a mesma cobranca, entao isPaid() cobre os dois e o
 * acesso ao curso e liberado uma unica vez (ver correcao C3 do plano).
 */
enum OrderStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Received = 'received';
    case Confirmed = 'confirmed';
    case Overdue = 'overdue';
    case Refunded = 'refunded';
    case PartiallyRefunded = 'partially_refunded';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Aguardando pagamento',
            self::Received => 'Pago',
            self::Confirmed => 'Pagamento confirmado',
            self::Overdue => 'Vencido',
            self::Refunded => 'Estornado',
            self::PartiallyRefunded => 'Estorno parcial',
            self::Cancelled => 'Cancelado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Received, self::Confirmed => 'success',
            self::Overdue, self::Cancelled => 'danger',
            self::Refunded, self::PartiallyRefunded => 'gray',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Pending => Heroicon::OutlinedClock,
            self::Received => Heroicon::OutlinedCheckCircle,
            self::Confirmed => Heroicon::OutlinedCheckBadge,
            self::Overdue => Heroicon::OutlinedExclamationCircle,
            self::Refunded, self::PartiallyRefunded => Heroicon::OutlinedArrowUturnLeft,
            self::Cancelled => Heroicon::OutlinedXCircle,
        };
    }

    public function isPaid(): bool
    {
        return in_array($this, [self::Received, self::Confirmed], strict: true);
    }

    public function grantsAccess(): bool
    {
        return in_array($this, [self::Received, self::Confirmed, self::PartiallyRefunded], strict: true);
    }

    /**
     * @return array<int, self>
     */
    public static function accessGranting(): array
    {
        return [self::Received, self::Confirmed, self::PartiallyRefunded];
    }

    /**
     * Traduz o status textual vindo da API do Asaas.
     *
     * Status desconhecido devolve null em vez de estourar: o Asaas pode
     * introduzir novos status, e derrubar o webhook por causa disso travaria
     * a fila serial de entregas (ver C2b).
     */
    public static function tryFromAsaas(string $status): ?self
    {
        $normalized = mb_strtolower($status);

        if ($normalized === 'received_in_cash') {
            return self::Received;
        }

        return self::tryFrom($normalized);
    }
}
