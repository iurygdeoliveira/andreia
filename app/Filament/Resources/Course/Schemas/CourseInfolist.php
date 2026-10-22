<?php

declare(strict_types=1);

namespace App\Filament\Resources\Course\Schemas;

use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\SpatieTagsEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Detalhes do Curso')
                    ->columnSpanFull()
                    ->tabs([
                        self::getGeneralInfoTab(),
                        self::getMediaTab(),
                        self::getTechnicalDetailsTab(),
                    ])
                    ->persistTabInQueryString(),
            ]);
    }

    private static function getGeneralInfoTab(): Tab
    {
        return Tab::make('Informações Gerais')
            ->icon(Heroicon::AcademicCap)
            ->schema([
                TextEntry::make('title')
                    ->label('Título')
                    ->columnSpan(2),

                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->columnSpan(1),

                TextEntry::make('price_in_cents')
                    ->label('Preço')
                    ->money('BRL')
                    ->columnSpan(1),

                TextEntry::make('organization.name')
                    ->label('Organização')
                    ->icon(Heroicon::BuildingOffice)
                    ->columnSpan(2),

                SpatieTagsEntry::make('tags')
                    ->label('Tags')
                    ->columnSpan(3),
            ])
            ->columns(3);
    }

    private static function getMediaTab(): Tab
    {
        return Tab::make('Mídia e Conteúdo')
            ->icon(Heroicon::Photo)
            ->schema([
                SpatieMediaLibraryImageEntry::make('cover')
                    ->label('Imagem de Capa')
                    ->collection('cover')
                    ->columnSpan(2),

                TextEntry::make('description')
                    ->label('Descrição')
                    ->placeholder('Sem descrição cadastrada')
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    private static function getTechnicalDetailsTab(): Tab
    {
        return Tab::make('Detalhes Técnicos')
            ->icon(Heroicon::Cog6Tooth)
            ->schema([
                TextEntry::make('checkout_code')
                    ->label('Código Checkout')
                    ->copyable()
                    ->copyMessage('Código copiado!')
                    ->icon(Heroicon::ClipboardDocumentCheck),

                TextEntry::make('slug')
                    ->label('Slug'),

                TextEntry::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i'),

                TextEntry::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->columns(2);
    }
}
