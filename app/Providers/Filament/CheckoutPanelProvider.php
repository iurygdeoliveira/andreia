<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Checkout\Pages\Checkout;
use App\Http\Middleware\EnsureSecurityHeaders;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CheckoutPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('checkout')
            ->path('checkout')
            ->spa()
            ->darkMode(true, true)
            ->brandLogo(fn (): Factory|View => view('panels.auth.logo_auth'))
            ->brandLogoHeight('2.5rem')
            ->homeUrl('/')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->authGuard('web')
            ->navigation(false)
            ->topbar(true)
            ->maxContentWidth(Width::SevenExtraLarge)
            ->pages([
                Checkout::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                EnsureSecurityHeaders::class,
            ]);
    }
}
