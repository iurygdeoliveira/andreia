<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\OrganizationRole;
use App\Enums\Permission as PermissionEnum;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Tenancy\SpatieTeamResolver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');
        $resources = ['media', 'users', 'authentication-log', 'course'];

        foreach ($resources as $resource) {
            foreach (PermissionEnum::cases() as $permission) {
                PermissionModel::firstOrCreate([
                    'name' => $permission->for($resource),
                    'guard_name' => $guard,
                ]);
            }
        }

        OrganizationRole::ensureGlobalRoles($guard);

        $adminEmail = (string) env('ADMIN_USER_EMAIL', 'admin@metodoandreia360.com.br');
        $adminPassword = (string) env('ADMIN_USER_PASSWORD', 'mudar123');
        $adminName = (string) env('ADMIN_USER_NAME', 'Administrador Geral');

        $admin = User::query()->firstOrNew(['email' => $adminEmail]);
        $admin->fill([
            'name' => $adminName,
            'email_verified_at' => $admin->email_verified_at ?? now(),
            'password' => Hash::make($adminPassword),
            'is_approved' => true,
            'approved_by' => null,
        ]);
        $admin->save();

        $globalResolver = resolve(SpatieTeamResolver::class);
        $globalResolver->setPermissionsTeamId(0);
        $admin->syncRoles([OrganizationRole::Admin->value]);
        $globalResolver->setPermissionsTeamId(null);

        $adminRole = Role::where('name', OrganizationRole::Admin->value)->where('guard_name', $guard)->first();
        if ($adminRole) {
            $adminRole->syncPermissions(PermissionModel::all());
        }

        $org = Organization::firstOrCreate(
            ['slug' => 'andreia360'],
            [
                'name' => 'Método Andreia 360',
                'is_active' => true,
            ],
        );

        $this->ensurePermissionsForOrganization($org->id, $guard);

        $ownerEmail = (string) env('OWNER_USER_EMAIL', 'andreia@metodoandreia360.com.br');
        $ownerPassword = (string) env('OWNER_USER_PASSWORD', 'mudar123');
        $ownerName = (string) env('OWNER_USER_NAME', 'Andreia Castro');

        $owner = User::query()->firstOrNew(['email' => $ownerEmail]);
        $owner->fill([
            'name' => $ownerName,
            'email_verified_at' => $owner->email_verified_at ?? now(),
            'password' => Hash::make($ownerPassword),
            'is_approved' => true,
            'approved_by' => $admin->id,
        ]);
        $owner->save();

        $this->ensureMembership($owner->id, $org->id, 'owner');

        $studentEmail = (string) env('STUDENT_USER_EMAIL', 'aluna@metodoandreia360.com.br');
        $studentPassword = (string) env('STUDENT_USER_PASSWORD', 'mudar123');
        $studentName = (string) env('STUDENT_USER_NAME', 'Aluna Teste Andreia 360');

        $student = User::query()->firstOrNew(['email' => $studentEmail]);
        $student->fill([
            'name' => $studentName,
            'email_verified_at' => $student->email_verified_at ?? now(),
            'password' => Hash::make($studentPassword),
            'is_approved' => true,
            'approved_by' => $admin->id,
        ]);
        $student->save();

        $this->ensureMembership($student->id, $org->id, 'student');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function ensureMembership(int $userId, int $organizationId, string $role): void
    {
        DB::table('organization_user')->insertOrIgnore([
            'organization_id' => $organizationId,
            'user_id' => $userId,
            'role' => $role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::find($userId);
        $organization = Organization::find($organizationId);
        if ($user && $organization) {
            $spatieRole = match ($role) {
                'owner', 'admin' => OrganizationRole::ensureOwnerRoleForTeam($organization->id, $guard = config('auth.defaults.guard', 'web')),
                default => OrganizationRole::ensureUserRoleForTeam($organization->id, $guard = config('auth.defaults.guard', 'web')),
            };
            $user->assignRoleInOrganization($spatieRole, $organization);
        }
    }

    private function ensurePermissionsForOrganization(int $organizationId, string $guard): void
    {
        $resources = ['media', 'users', 'authentication-log', 'course'];

        $teamResolver = resolve(SpatieTeamResolver::class);
        $teamResolver->setPermissionsTeamId($organizationId);

        $ownerRole = OrganizationRole::ensureOwnerRoleForTeam($organizationId, $guard);
        OrganizationRole::ensureUserRoleForTeam($organizationId, $guard);

        foreach ($resources as $resource) {
            foreach (PermissionEnum::cases() as $permission) {
                $permissionName = $permission->for($resource);
                PermissionModel::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => $guard,
                ]);

                if (! $ownerRole->hasPermissionTo($permissionName, $guard)) {
                    $ownerRole->givePermissionTo($permissionName);
                }
            }
        }

        $teamResolver->setPermissionsTeamId(null);
    }
}
