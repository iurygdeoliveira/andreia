<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\OrganizationRole;
use App\Enums\Permission;
use App\Models\Course;
use App\Models\Organization;
use App\Models\User;
use Filament\Facades\Filament;

class CoursePolicy
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
        if (Filament::getCurrentPanel()?->getId() === 'student') {
            return $user->enrollments()
                ->active()
                ->exists();
        }

        return $user->can(Permission::VIEW->for('course'));
    }

    public function view(User $user, Course $course): bool
    {
        if (Filament::getCurrentPanel()?->getId() === 'student') {
            return $user->enrollments()
                ->where('course_id', $course->id)
                ->where('organization_id', $course->organization_id)
                ->active()
                ->exists();
        }

        return $user->can(Permission::VIEW->for('course'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::CREATE->for('course'));
    }

    public function update(User $user, Course $course): bool
    {
        return $user->can(Permission::UPDATE->for('course'));
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->can(Permission::DELETE->for('course'));
    }

    public function restore(User $user, Course $course): bool
    {
        return $user->can(Permission::UPDATE->for('course'));
    }

    public function forceDelete(User $user, Course $course): bool
    {
        return $user->can(Permission::DELETE->for('course'));
    }
}
