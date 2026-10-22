<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Permissions\Pages;

use Filament\Support\Icons\Heroicon;

class CoursePermissions extends BasePermissionPage
{
    protected string $view = 'panels.clusters.permissions.pages.course-permissions';

    protected static ?string $navigationLabel = 'Cursos';

    protected static ?string $title = 'Permissões de Cursos';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static string $resourceSlug = 'course';
}
