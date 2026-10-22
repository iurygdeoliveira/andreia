<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\OrganizationRole;
use App\Enums\Permission;
use App\Models\Lesson;
use App\Models\Organization;
use App\Models\User;
use Filament\Facades\Filament;

class LessonPolicy
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
            return true;
        }

        return $user->can(Permission::VIEW->for('lesson'));
    }

    public function view(User $user, Lesson $lesson): bool
    {
        if (Filament::getCurrentPanel()?->getId() === 'student') {
            $lesson->loadMissing('module');
            $courseId = $lesson->module?->course_id;

            return $courseId !== null && $user->enrollments()
                ->where('course_id', $courseId)
                ->where('organization_id', $lesson->organization_id)
                ->active()
                ->exists();
        }

        return $user->can(Permission::VIEW->for('lesson'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::CREATE->for('lesson'));
    }

    public function update(User $user, Lesson $lesson): bool
    {
        return $user->can(Permission::UPDATE->for('lesson'));
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->can(Permission::DELETE->for('lesson'));
    }

    public function restore(User $user, Lesson $lesson): bool
    {
        return $user->can(Permission::UPDATE->for('lesson'));
    }

    public function forceDelete(User $user, Lesson $lesson): bool
    {
        return $user->can(Permission::DELETE->for('lesson'));
    }
}
