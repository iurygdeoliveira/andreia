<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\OrganizationRole;
use App\Enums\Permission;
use App\Models\Module;
use App\Models\Organization;
use App\Models\User;
use Filament\Facades\Filament;

class ModulePolicy
{
    /**
     * Executado antes de qualquer verificação de autorização.
     * - Admin: acesso total global
     * - Owner: acesso total dentro do tenant atual
     */
    public function before(User $user): ?bool
    {
        if ($user->hasRole(OrganizationRole::Admin->value)) {
            return true;
        }

        $currentTenant = Filament::getTenant();
        if ($currentTenant instanceof Organization && $user->isOwnerOfOrganization($currentTenant)) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can(Permission::VIEW->for('module'));
    }

    public function view(User $user, Module $module): bool
    {
        return $user->can(Permission::VIEW->for('module'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::CREATE->for('module'));
    }

    public function update(User $user, Module $module): bool
    {
        return $user->can(Permission::UPDATE->for('module'));
    }

    public function delete(User $user, Module $module): bool
    {
        return $user->can(Permission::DELETE->for('module'));
    }

    public function restore(User $user, Module $module): bool
    {
        return $user->can(Permission::UPDATE->for('module'));
    }

    public function forceDelete(User $user, Module $module): bool
    {
        return $user->can(Permission::DELETE->for('module'));
    }
}
