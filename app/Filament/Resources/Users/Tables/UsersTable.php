<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\OrganizationRole;
use App\Events\UserApproved;
use App\Filament\Resources\Users\Actions\DeleteUserAction;
use App\Models\Organization;
use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        $currentUser = Filament::auth()->user();
        $isAdmin = false;

        if ($currentUser instanceof User) {
            $isAdmin = $currentUser->hasRole(OrganizationRole::Admin->value);
        }

        $query = User::query()->withoutRole(OrganizationRole::Admin->value);
        $currentTeam = Filament::getTenant();

        if (! $isAdmin) {
            if ($currentTeam instanceof Organization) {
                $query->whereHas('organizations', function ($q) use ($currentTeam): void {
                    $q->where('organizations.id', $currentTeam->getKey());
                })->withRolesForTeam($currentTeam);
            }
        } else {
            $query->with(['organizations', 'rolesWithTeams']);
        }

        return $table
            ->query($query)
            ->columns([
                self::getNameColumn(),
                self::getEmailColumn(),
                ...self::getTenantsColumn($isAdmin),
                self::getRolesColumn($isAdmin),
                self::getStatusColumn(),
                self::getApprovalColumn(),
            ])
            ->filters([
                //
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
                        ->hiddenLabel()
                        ->visible(fn (User $record): bool => Filament::auth()->user()->can('update', $record) && $record->isApproved()),

                    DeleteUserAction::make()
                        ->tooltip('Excluir')
                        ->hiddenLabel(),
                ])
                    ->buttonGroup(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    private static function getNameColumn(): TextColumn
    {
        return TextColumn::make('name')
            ->searchable()
            ->sortable()
            ->label('Nome Completo');
    }

    private static function getEmailColumn(): TextColumn
    {
        return TextColumn::make('email')
            ->searchable()
            ->sortable()
            ->label('E-mail');
    }

    private static function getTenantsColumn(bool $isAdmin): array
    {
        if (! $isAdmin) {
            return [];
        }

        return [
            TextColumn::make('organizations.name')
                ->label('Organizações')
                ->badge()
                ->color('info')
                ->separator(',')
                ->placeholder('—'),
        ];
    }

    private static function getRolesColumn(bool $isAdmin): TextColumn
    {
        return TextColumn::make('roles')
            ->label('Função')
            ->state(fn (User $record): array|string => self::getTenantRolesForUser($record, $isAdmin))
            ->badge()
            ->color('gray');
    }

    private static function getStatusColumn(): TextColumn
    {
        return TextColumn::make('is_suspended')
            ->label('Status')
            ->badge()
            ->color(fn (bool $state): string => $state ? 'danger' : 'success')
            ->formatStateUsing(fn (bool $state): string => $state ? 'Suspenso' : 'Ativo');
    }

    private static function getApprovalColumn(): ToggleColumn
    {
        return ToggleColumn::make('is_approved')
            ->disabled(fn (User $record): bool => ! Filament::auth()->user()->can('update', $record))
            ->onColor('primary')
            ->offColor('danger')
            ->onIcon(Heroicon::Check)
            ->offIcon(Heroicon::XMark)
            ->label('Aprovar')
            ->afterStateUpdated(function (User $record, $state): void {
                if ($state) {
                    $record->is_suspended = false;
                    if (! $record->hasVerifiedEmail()) {
                        $record->markEmailAsVerified();
                    }
                    $record->save();
                    event(new UserApproved($record));
                }
            });
    }

    private static function getTenantRolesForUser(User $record, bool $isAdmin): array|string
    {
        if ($record->hasRole(OrganizationRole::Admin->value)) {
            return '—';
        }

        if ($isAdmin) {
            return self::getAdminViewRoles($record);
        }

        return self::getTenantViewRoles($record);
    }

    private static function getAdminViewRoles(User $record): array|string
    {
        $teams = $record->organizations;

        if ($teams->isEmpty()) {
            return '—';
        }

        $lines = [];

        foreach ($teams as $team) {
            $roles = $record->rolesWithTeams
                ->where('team_id', $team->id);

            if ($roles->isEmpty()) {
                $lines[] = '—';

                continue;
            }

            $labels = $roles->map(fn ($role): string => self::getRoleLabel($role->name))->all();

            $lines[] = implode(', ', $labels);
        }

        return $lines;
    }

    private static function getTenantViewRoles(User $record): string
    {
        $roles = $record->rolesWithTeams ?? collect();

        if ($roles->isEmpty()) {
            return '—';
        }

        $firstRole = $roles->first();

        return $firstRole ? self::getRoleLabel($firstRole->name) : '—';
    }

    private static function getRoleLabel(string $name): string
    {
        return OrganizationRole::tryFrom($name)?->getLabel() ?? $name;
    }
}
