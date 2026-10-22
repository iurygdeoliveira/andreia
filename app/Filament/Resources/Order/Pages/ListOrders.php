<?php

declare(strict_types=1);

namespace App\Filament\Resources\Order\Pages;

use App\Enums\PurchaseType;
use App\Filament\Resources\Order\OrderResource;
use App\Filament\Widgets\PurchaseTypesStatsWidget;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Override;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            PurchaseTypesStatsWidget::class,
        ];
    }

    #[Override]
    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos os Pedidos'),

            'premium' => Tab::make('⭐ Plano Premium')
                ->modifyQueryUsing(fn ($query) => $query->where('purchase_type', PurchaseType::Premium)),

            'vip' => Tab::make('💎 Plano VIP 360')
                ->modifyQueryUsing(fn ($query) => $query->where('purchase_type', PurchaseType::Vip)),

            'essencial' => Tab::make('⚡ Plano Essencial')
                ->modifyQueryUsing(fn ($query) => $query->where('purchase_type', PurchaseType::Essencial)),
        ];
    }
}

