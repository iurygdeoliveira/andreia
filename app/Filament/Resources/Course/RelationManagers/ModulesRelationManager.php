<?php

declare(strict_types=1);

namespace App\Filament\Resources\Course\RelationManagers;

use App\Models\Course;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ModulesRelationManager extends RelationManager
{
    protected static string $relationship = 'modules';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $title = 'Módulos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->reorderable('sort')
            ->columns([
                Tables\Columns\TextInputColumn::make('title')
                    ->label('Título')
                    ->searchable(),

                Tables\Columns\TextColumn::make('lessons_count')
                    ->counts('lessons')
                    ->label('Qtd. Aulas'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data, RelationManager $livewire): array {
                        $owner = $livewire->getOwnerRecord();
                        $data['organization_id'] = $owner instanceof Course
                            ? $owner->organization_id
                            : $owner->getAttribute('organization_id');

                        return $data;
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make('manage_lessons')
                        ->tooltip('Gerenciar Aulas')
                        ->color('primary')
                        ->icon(Heroicon::BookOpen)
                        ->hiddenLabel()
                        ->modalHeading(fn ($record): string => 'Gerenciar Aulas - '.$record->title)
                        ->schema([
                            $this->getLessonsRepeater(),
                        ]),

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

    protected function getLessonsRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('lessons')
            ->relationship('lessons')
            ->hiddenLabel()
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Título da Aula')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set): mixed => $operation === 'create' ? $set('view_path', 'panels.lessons.'.Str::slug((string) $state)) : null),

                Forms\Components\TextInput::make('view_path')
                    ->label('Caminho da View Blade')
                    ->readOnly()
                    ->dehydrated()
                    ->helperText('Caminho da view Blade (ex.: panels.lessons.fisiologia-exercicio.aula-1).'),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, RelationManager $livewire): array {
                $owner = $livewire->getOwnerRecord();
                $data['organization_id'] = $owner instanceof Course
                    ? $owner->organization_id
                    : $owner->getAttribute('organization_id');

                return $data;
            })
            ->orderColumn('sort');
    }
}
