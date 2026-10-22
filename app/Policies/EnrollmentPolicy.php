<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\OrganizationRole;
use App\Models\Enrollment;
use App\Models\Organization;
use App\Models\User;
use Filament\Facades\Filament;

class EnrollmentPolicy
{
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
        return false;
    }

    public function view(User $user, Enrollment $enrollment): bool
    {
        return $user->id === $enrollment->user_id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Enrollment $enrollment): bool
    {
        return false;
    }

    public function delete(User $user, Enrollment $enrollment): bool
    {
        return false;
    }
}
