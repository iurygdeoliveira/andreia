<?php

declare(strict_types=1);

namespace App\Filament\Resources\Order\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes da Compra')
                    ->icon(Heroicon::CurrencyDollar)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('order_number')
                                    ->label('Número do Pedido')
                                    ->weight(FontWeight::Bold)
                                    ->copyable(),

                                TextEntry::make('purchase_type')
                                    ->label('Tipo de Compra / Plano')
                                    ->badge(),

                                TextEntry::make('total_amount')
                                    ->label('Valor Total')
                                    ->money('BRL')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('payment_status')
                                    ->label('Status do Pagamento')
                                    ->badge(),

                                TextEntry::make('payment_method')
                                    ->label('Método')
                                    ->badge(),

                                TextEntry::make('paid_at')
                                    ->label('Confirmado em')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('Aguardando pagamento'),
                            ]),
                    ]),

                Section::make('Dados da Aluna / Compradora')
                    ->icon(Heroicon::User)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('buyer_name')
                                    ->label('Nome Completo')
                                    ->weight(FontWeight::SemiBold),

                                TextEntry::make('buyer_email')
                                    ->label('E-mail')
                                    ->icon(Heroicon::Envelope)
                                    ->copyable(),

                                TextEntry::make('buyer_phone')
                                    ->label('WhatsApp / Telefone')
                                    ->icon(Heroicon::Phone)
                                    ->copyable(),

                                TextEntry::make('buyer_document')
                                    ->label('CPF')
                                    ->placeholder('—'),
                            ]),
                    ]),

                Section::make('Acesso Liberado à Plataforma')
                    ->icon(Heroicon::AcademicCap)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('items.course.title')
                                    ->label('Curso Contratado')
                                    ->bulleted(),

                                TextEntry::make('user.email')
                                    ->label('Conta de Acesso vinculada')
                                    ->placeholder('Conta será criada/vinculada na confirmação'),
                            ]),
                    ]),
            ]);
    }
}
