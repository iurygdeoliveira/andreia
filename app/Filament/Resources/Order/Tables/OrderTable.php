<?php

declare(strict_types=1);

namespace App\Filament\Resources\Order\Tables;

use App\Enums\OrderStatus;
use App\Enums\PurchaseType;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Pedido')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('buyer_name')
                    ->label('Comprador')
                    ->description(fn ($record): string => $record->buyer_email)
                    ->searchable(['buyer_name', 'buyer_email']),

                TextColumn::make('items.course.title')
                    ->label('Curso')
                    ->bulleted()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->placeholder('—'),

                TextColumn::make('purchase_type')
                    ->label('Tipo de Compra')
                    ->badge()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('paid_at')
                    ->label('Pago em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('purchase_type')
                    ->label('Tipo de Compra')
                    ->options(PurchaseType::class),

                SelectFilter::make('payment_status')
                    ->label('Status')
                    ->options(OrderStatus::class),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modal()
                    ->icon(Heroicon::Eye)
                    ->color('primary')
                    ->tooltip('Visualizar Detalhes'),
            ]);
    }
}
