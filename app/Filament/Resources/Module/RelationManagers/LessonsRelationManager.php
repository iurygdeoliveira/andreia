<?php

declare(strict_types=1);

namespace App\Filament\Resources\Module\RelationManagers;

use App\Models\Module;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessons';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $title = 'Aulas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Set $set): mixed => $operation === 'create' ? $set('view_path', 'panels.lessons.'.Str::slug($state)) : null
                    ),

                TextInput::make('view_path')
                    ->label('Caminho da View (Automático)')
                    ->readOnly()
                    ->dehydrated()
                    ->unique()
                    ->helperText('Copie este caminho para criar a view Blade na pasta resources/views.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->reorderable('sort')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data, RelationManager $livewire): array {
                        $owner = $livewire->getOwnerRecord();
                        $data['organization_id'] = $owner instanceof Module
                            ? $owner->organization_id
                            : $owner->getAttribute('organization_id');

                        return $data;
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->tooltip('Editar')
                        ->color('success')
                        ->icon(Heroicon::PencilSquare)
                        ->hiddenLabel(),

                    DeleteAction::make()
                        ->tooltip('Excluir')
                        ->color('danger')
                        ->icon(Heroicon::Trash)
                        ->hiddenLabel(),
                ])->buttonGroup(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
