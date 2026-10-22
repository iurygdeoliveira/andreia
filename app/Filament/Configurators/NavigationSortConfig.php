<?php

declare(strict_types=1);

namespace App\Filament\Configurators;

use App\Filament\Clusters\Permissions\PermissionsCluster;
use App\Filament\Pages\Mail\Templates;
use App\Filament\Pages\Organization\GeneralSettings;
use App\Filament\Resources\Authentication\AuthenticationLogResource;
use App\Filament\Resources\Course\CourseResource;
use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Organization\OrganizationResource;
use App\Filament\Resources\Users\UserResource;
use WallaceMartinss\FilamentSecurity\Filament\Resources\SecurityEventResource;

class NavigationSortConfig
{
    public static function getSortOrder(string $class): ?int
    {
        $order = [
            // Admin Panel / User Panel sorting config
            GeneralSettings::class => 1,
            MediaResource::class => 1,
            CourseResource::class => 2,
            UserResource::class => 3,
            AuthenticationLogResource::class => 4,
            SecurityEventResource::class => 5,
            OrganizationResource::class => 6,
            Templates::class => 7,
            PermissionsCluster::class => 8,
        ];

        // Busca exata pela classe
        if (isset($order[$class])) {
            return $order[$class];
        }

        if (! class_exists($class)) {
            return null;
        }

        // Fallback: busca pela classe pai (herança)
        foreach ($order as $registeredClass => $sort) {
            if (is_subclass_of($class, $registeredClass)) {
                return $sort;
            }
        }

        return null;
    }
}
