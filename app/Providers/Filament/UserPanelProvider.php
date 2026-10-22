<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\Organization\RegisterOrganization;
use App\Filament\Pages\ViewLesson;
use App\Filament\Resources\Authentication\AuthenticationLogResource;
use App\Filament\Resources\Course\CourseResource;
use App\Filament\Resources\Order\OrderResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Widgets\PurchaseTypesDistributionChart;
use App\Filament\Widgets\PurchaseTypesStatsWidget;
use App\Http\Middleware\TeamSyncMiddleware;
use App\Livewire\Organization\ListInvitations;
use App\Livewire\Organization\ListMembers;
use App\Models\Organization;
use App\Support\Changelog\ChangelogPlugin;
use App\Support\NotificationCenter\NotificationCategory;
use App\Support\NotificationCenter\NotificationCenterManager;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

class UserPanelProvider extends BasePanelProvider
{
    #[\Override]
    public function panel(Panel $panel): Panel
    {
        // Configurações compartilhadas (Base define id/path via getPanelId/getPanelPath)
        $panel = parent::panel($panel);

        NotificationCenterManager::registerCategories('user', [
            NotificationCategory::make('system')
                ->label('Sistema')
                ->icon(Heroicon::Cog6Tooth)
                ->color(Color::Gray)
                ->order(1),
            NotificationCategory::make('security')
                ->label('Segurança')
                ->icon(Heroicon::ShieldCheck)
                ->color(Color::Red)
                ->order(2),
        ]);

        // Particularidades do painel user
        $panel = $panel
            ->tenant(Organization::class, slugAttribute: 'slug')
            ->tenantRegistration(RegisterOrganization::class)
            ->tenantMenu(true)
            ->pages([
                Dashboard::class,
            ])
            ->livewireComponents([
                ListMembers::class,
                ListInvitations::class,
            ])
            ->resources([
                AuthenticationLogResource::class,
                CourseResource::class,
                OrderResource::class,
                UserResource::class,
            ])
            ->pages([
                Dashboard::class,
                ViewLesson::class,
            ])
            ->plugins([
                ChangelogPlugin::make(),
            ])
            ->widgets([
                PurchaseTypesStatsWidget::class,
                PurchaseTypesDistributionChart::class,
            ])
            ->tenantMiddleware([
                TeamSyncMiddleware::class,
            ], isPersistent: true);

        return $panel;
    }

    protected function getPanelId(): string
    {
        return 'user';
    }

    protected function getPanelPath(): string
    {
        return 'user';
    }
}
