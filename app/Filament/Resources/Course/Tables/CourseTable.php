<?php

declare(strict_types=1);

namespace App\Filament\Resources\Course\Tables;

use App\Filament\Resources\Course\Actions\DeleteCourseAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CourseTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')
                    ->label('Capa')
                    ->collection('cover'),

                TextColumn::make('title')
                    ->label('Título')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),

                SpatieTagsColumn::make('tags')
                    ->label('Tags'),

                TextColumn::make('price_in_cents')
                    ->label('Preço')
                    ->money('BRL')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('checkout_code')
                    ->label('Código Checkout')
                    ->copyable()
                    ->copyMessage('Código copiado!'),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->tooltip('Visualizar')
                        ->color('gray')
                        ->icon(Heroicon::Eye)
                        ->hiddenLabel(),
                    EditAction::make()
                        ->tooltip('Editar')
                        ->color('success')
                        ->icon(Heroicon::PencilSquare)
                        ->hiddenLabel(),
                    DeleteCourseAction::make()
                        ->tooltip('Excluir')
                        ->hiddenLabel(),
                ])
                    ->buttonGroup(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
