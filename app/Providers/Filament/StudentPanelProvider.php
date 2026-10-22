<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\ViewLesson;
use App\Filament\Student\Resources\Course\StudentCourseResource;
use App\Http\Middleware\TeamSyncMiddleware;
use App\Models\Organization;
use App\Support\NotificationCenter\NotificationCategory;
use App\Support\NotificationCenter\NotificationCenterManager;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

class StudentPanelProvider extends BasePanelProvider
{
    #[\Override]
    public function panel(Panel $panel): Panel
    {
        $panel = parent::panel($panel);

        NotificationCenterManager::registerCategories('student', [
            NotificationCategory::make('system')
                ->label('Sistema')
                ->icon(Heroicon::Cog6Tooth)
                ->color(Color::Gray)
                ->order(1),
        ]);

        $panel = $panel
            ->tenant(Organization::class, slugAttribute: 'slug')
            ->tenantMenu(false)
            ->pages([
                Dashboard::class,
                ViewLesson::class,
            ])
            ->resources([
                StudentCourseResource::class,
            ])
            ->widgets([
            ])
            ->tenantMiddleware([
                TeamSyncMiddleware::class,
            ], isPersistent: true);

        return $panel;
    }

    protected function getPanelId(): string
    {
        return 'student';
    }

    protected function getPanelPath(): string
    {
        return 'student';
    }
}
