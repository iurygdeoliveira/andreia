<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\OrganizationRole;
use App\Enums\Permission;
use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    /**
     * Executado antes de qualquer verificação de autorização.
     * - Admin: acesso total global
     */
    public function before(User $user): ?bool
    {
        if ($user->hasRole(OrganizationRole::Admin->value)) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can(Permission::VIEW->for('organization'));
    }

    public function view(User $user, Organization $organization): bool
    {
        return $user->can(Permission::VIEW->for('organization'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::CREATE->for('organization'));
    }

    public function update(User $user, Organization $organization): bool
    {
        return $user->can(Permission::UPDATE->for('organization'));
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $user->can(Permission::DELETE->for('organization'));
    }
}
