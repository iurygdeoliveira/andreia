<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados Pessoais')
                    ->description('Informações básicas e controle de acesso do usuário')
                    ->columns(2)
                    ->components(self::getPersonalDataFields()),

                Section::make('Autenticação')
                    ->description('Configure a senha de acesso do usuário')
                    ->columns(2)
                    ->components(self::getAuthenticationFields()),
            ]);
    }

    private static function getPersonalDataFields(): array
    {
        return [
            Select::make('tenant_id')
                ->label('Organização')
                ->options(fn (): array => Organization::query()
                    ->where('is_active', true)
                    ->orderBy('name', 'asc')
                    ->pluck('name', 'id')
                    ->all())
                ->searchable()
                ->preload()
                ->native(false)
                ->dehydrated(false)
                ->required()
                ->visible(fn (string $operation): bool => self::shouldShowTenantField())
                ->columnSpan(1)
                ->formatStateUsing(fn (?User $record): ?int => $record?->organizations()->first()?->id),

            Select::make('organization_role')
                ->label('Função na Organização')
                ->options(OrganizationRole::tenantOptions())
                ->enum(OrganizationRole::class)
                ->default(OrganizationRole::User->value)
                ->native(false)
                ->dehydrated(false)
                ->required()
                ->visible(fn (string $operation): bool => self::shouldShowTenantField())
                ->columnSpan(1)
                ->formatStateUsing(fn (?User $record): ?OrganizationRole => ($role = $record?->organizations()->first()?->pivot?->role) ? OrganizationRole::tryFromValue($role) : OrganizationRole::User),

            TextInput::make('name')
                ->label('Nome Completo')
                ->required()
                ->columnSpan(1),

            TextInput::make('email')
                ->label('E-mail')
                ->email()
                ->required()
                ->columnSpan(1),

            Toggle::make('is_suspended')
                ->label(fn (Get $get): string => $get('is_suspended') ? 'Usuário suspenso' : 'Usuário com acesso liberado')
                ->onColor('danger')
                ->offColor('primary')
                ->onIcon(Heroicon::NoSymbol)
                ->offIcon(Heroicon::Check)
                ->default(fn ($record): bool => (bool) ($record?->is_suspended))
                ->disabled(fn (?User $record): bool => $record?->getKey() === Auth::id())
                ->hint(fn (?User $record): ?string => $record?->getKey() === Auth::id() ? __('Você não pode suspender a si mesmo.') : null)
                ->hintColor('danger')
                ->hidden(fn (string $operation): bool => $operation === 'create')
                ->columnSpan(2),

            TextInput::make('suspension_reason')
                ->label('Motivo da Suspensão')
                ->disabled(fn (?User $record): bool => $record?->getKey() === Auth::id())
                ->placeholder('Descreva o motivo da suspensão...')
                ->hidden(fn (string $operation): bool => $operation === 'create')
                ->columnSpan(2),
        ];
    }

    private static function getAuthenticationFields(): array
    {
        return [
            TextInput::make('password')
                ->label('Senha')
                ->password()
                ->revealable()
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create')
                ->confirmed()
                ->columnSpan(1),

            TextInput::make('password_confirmation')
                ->label('Confirmar Senha')
                ->password()
                ->revealable()
                ->requiredWith('password')
                ->dehydrated(false)
                ->columnSpan(1),
        ];
    }

    private static function shouldShowTenantField(): bool
    {
        $user = Filament::auth()->user();
        if (! ($user instanceof User)) {
            return false;
        }

        return $user->hasRole(OrganizationRole::Admin->value);
    }
}
