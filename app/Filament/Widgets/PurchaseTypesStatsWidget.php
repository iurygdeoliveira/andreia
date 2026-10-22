<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PurchaseType;
use App\Models\Order;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class PurchaseTypesStatsWidget extends BaseWidget
{
    protected ?string $pollingInterval = null;

    #[\Override]
    protected function getStats(): array
    {
        $tenant = Filament::getTenant();

        $baseQuery = Order::query()
            ->when($tenant, fn (Builder $q) => $q->where('organization_id', $tenant->getKey()));

        $paidOrders = (clone $baseQuery)->whereIn('payment_status', [OrderStatus::Received, OrderStatus::Confirmed]);

        $totalRevenue = (float) (clone $paidOrders)->sum('total_amount');
        $totalOrdersCount = (clone $baseQuery)->count();
        $paidCount = (clone $paidOrders)->count();

        $stats = [
            Stat::make('Faturamento Total', 'R$ ' . number_format($totalRevenue, 2, ',', '.'))
                ->description("{$paidCount} compras confirmadas ({$totalOrdersCount} pedidos gerados)")
                ->descriptionIcon(Heroicon::CurrencyDollar)
                ->color('success'),
        ];

        foreach (PurchaseType::cases() as $type) {
            $typeOrders = (clone $paidOrders)->where('purchase_type', $type);
            $count = $typeOrders->count();
            $sum = (float) $typeOrders->sum('total_amount');
            $percentage = $totalRevenue > 0 ? round(($sum / $totalRevenue) * 100, 1) : 0;

            $stats[] = Stat::make($type->getLabel(), 'R$ ' . number_format($sum, 2, ',', '.'))
                ->description("{$count} aluna(s) • {$percentage}% da receita")
                ->descriptionIcon($type->getIcon())
                ->color($type->getColor());
        }

        return $stats;
    }
}
