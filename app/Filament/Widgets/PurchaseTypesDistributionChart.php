<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PurchaseType;
use App\Models\Order;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class PurchaseTypesDistributionChart extends ChartWidget
{
    protected ?string $heading = 'Distribuição das Vendas por Tipo de Compra';

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '280px';

    #[\Override]
    protected function getData(): array
    {
        $tenant = Filament::getTenant();

        $baseQuery = Order::query()
            ->when($tenant, fn (Builder $q) => $q->where('organization_id', $tenant->getKey()))
            ->whereIn('payment_status', [OrderStatus::Received, OrderStatus::Confirmed]);

        $labels = [];
        $data = [];
        $colors = [
            '#0ea5e9', // Sky/Info for Essencial
            '#f59e0b', // Amber/Gold for Premium
            '#10b981', // Emerald/Green for VIP
        ];

        foreach (PurchaseType::cases() as $type) {
            $labels[] = $type->getLabel();
            $data[] = (clone $baseQuery)->where('purchase_type', $type)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Alunas por Tipo de Compra',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 2,
                    'borderColor' => '#010d26',
                ],
            ],
            'labels' => $labels,
        ];
    }

    #[\Override]
    protected function getType(): string
    {
        return 'doughnut';
    }
}
