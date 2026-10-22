<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Pages;

use App\Enums\OrganizationRole;
use App\Filament\Resources\Users\UserResource;
use App\Models\Organization;
use App\Models\User;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\HasFeedbackAction;
use App\Traits\Filament\NotificationsTrait;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

/**
 * @property-read User|null $record
 */
class EditUser extends EditRecord
{
    use HasBackButtonAction;
    use HasFeedbackAction;
    use NotificationsTrait;

    protected static string $resource = UserResource::class;

    #[Computed]
    public function canDelete(): bool
    {
        return $this->record?->getKey() !== Auth::id();
    }

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            $this->getSaveFormAction()->formId('form')->icon(Heroicon::Check),
            Action::make('delete')
                ->label(__('filament-actions::delete.single.label'))
                ->icon(Heroicon::Trash)
                ->color('danger')
                ->url(fn (): string => UserResource::getUrl('delete', ['record' => $this->getRecord()])),
        ];
    }

    #[\Override]
    protected function getFormActions(): array
    {
        return [];
    }

    #[\Override]
    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        if (! $record instanceof User) {
            return;
        }

        // Sincroniza suspended_at com is_suspended
        if ($record->is_suspended && $record->suspended_at === null) {
            $record->suspended_at = now();
            $record->save();
        }

        if (! $record->is_suspended && $record->suspended_at !== null) {
            $record->suspended_at = null;
            $record->save();
        }

        if ($record->getKey() === Auth::id() && $record->is_suspended) {
            // Evita auto-suspensão via edição direta
            $record->forceFill([
                'is_suspended' => false,
                'suspended_at' => null,
            ])->save();

            $this->notifyDanger('Você não pode suspender a si mesmo. Alteração revertida.');

            $this->redirect($this->getResource()::getUrl('index'));

            return;
        }

        $currentUser = Filament::auth()->user();
        if ($currentUser instanceof User && $currentUser->hasRole(OrganizationRole::Admin->value)) {
            $newTenantId = (int) ($this->data['tenant_id'] ?? 0);
            $newRole = (string) ($this->data['organization_role'] ?? OrganizationRole::User->value);
            if ($newTenantId > 0) {
                $organization = Organization::find($newTenantId);
                if ($organization instanceof Organization) {
                    $currentOrg = $record->organizations()->first();
                    $currentOrgId = $currentOrg?->id;
                    $currentRole = $currentOrg?->pivot?->role;

                    if ($currentOrgId !== $newTenantId || $currentRole !== $newRole) {
                        if ($currentOrg instanceof Organization && $currentOrgId !== $newTenantId) {
                            $record->removeAllUserRolesFromOrganization($currentOrg);
                            $record->removeAllOwnerRolesFromOrganization($currentOrg);
                        }

                        $record->organizations()->sync([
                            $organization->id => ['role' => $newRole],
                        ]);

                        if ($newRole === OrganizationRole::Owner->value || $newRole === OrganizationRole::Admin->value) {
                            $record->removeAllUserRolesFromOrganization($organization);
                        } else {
                            $record->removeAllOwnerRolesFromOrganization($organization);
                        }

                        $spatieRole = match ($newRole) {
                            OrganizationRole::Owner->value, OrganizationRole::Admin->value => OrganizationRole::ensureOwnerRoleForTeam($organization->id, config('auth.defaults.guard', 'web')),
                            default => OrganizationRole::ensureUserRoleForTeam($organization->id, config('auth.defaults.guard', 'web')),
                        };
                        $record->assignRoleInOrganization($spatieRole, $organization);
                    }
                }
            }
        }

        $this->notifySuccess('Usuário atualizado com sucesso.');
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
