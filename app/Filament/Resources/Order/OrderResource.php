<?php

declare(strict_types=1);

namespace App\Filament\Resources\Order;

use App\Filament\Resources\Order\Pages\ListOrders;
use App\Filament\Resources\Order\Schemas\OrderInfolist;
use App\Filament\Resources\Order\Tables\OrderTable;
use App\Models\Order;
use App\Traits\Filament\HasConfigurableNavigationSort;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Override;

/**
 * Consulta dos pedidos de compra (UC-04).
 *
 * Somente leitura: pedido e reflexo do que aconteceu no Asaas. Editar um
 * status aqui criaria divergencia silenciosa com o gateway.
 */
class OrderResource extends Resource
{
    use HasConfigurableNavigationSort;

    protected static ?string $model = Order::class;

    /** @see Order::organization() */
    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    protected static bool $isScopedToTenant = true;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;

    protected static ?string $navigationLabel = 'Pedidos';

    protected static ?string $title = 'Pedidos';

    protected static ?string $recordTitleAttribute = 'order_number';

    #[Override]
    public static function getModelLabel(): string
    {
        return 'Pedido';
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return 'Pedidos';
    }

    #[Override]
    public static function getRecordRouteKeyName(): string
    {
        return 'order_number';
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        $tenant = Filament::getTenant();

        return parent::getEloquentQuery()
            ->with(['items.course', 'user'])
            ->when($tenant, fn (Builder $query) => $query->where('organization_id', $tenant->getKey()));
    }

    #[Override]
    public static function infolist(Schema $schema): Schema
    {
        return OrderInfolist::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return OrderTable::configure($table);
    }

    #[Override]
    public static function canCreate(): bool
    {
        return false;
    }

    #[Override]
    public static function canEdit(mixed $record): bool
    {
        return false;
    }

    #[Override]
    public static function canDelete(mixed $record): bool
    {
        return false;
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
        ];
    }
}
