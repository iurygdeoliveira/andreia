<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Enums\OrganizationRole;
use App\Filament\Pages\Auth\VerificationPending;
use App\Models\Organization;
use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse as FilamentRegistrationResponse;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class RegistrationResponse implements FilamentRegistrationResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        if ($token = session()->pull('pending_invite_token')) {
            session()->forget('url.intended');

            return to_route('filament.auth.pages.accept-invite', ['token' => $token]);
        }

        // Se usuário não está aprovado (exceto administradores), redirecionar para verificação pendente
        if (! $user->hasRole(OrganizationRole::Admin->value) && ! $user->isApproved()) {
            return redirect()->to(VerificationPending::getUrl());
        }

        // Se usuário está aprovado, redirecionar para painel apropriado
        if ($user->canAccessPanel(Filament::getPanel('admin'))) {
            return redirect()->to('/admin');
        }

        if ($user->canAccessPanel(Filament::getPanel('user'))) {
            /** @var Organization|null $firstOrganization */
            $firstOrganization = $user->getTenants(Filament::getPanel('user'))->first();
            if ($firstOrganization) {
                return redirect()->to('/user/'.$firstOrganization->slug);
            }

            return redirect()->to('/user');
        }

        if ($user->canAccessPanel(Filament::getPanel('student'))) {
            /** @var Organization|null $firstOrganization */
            $firstOrganization = $user->getTenants(Filament::getPanel('student'))->first();
            if ($firstOrganization) {
                return redirect()->to('/student/'.$firstOrganization->slug);
            }

            return redirect()->to('/student');
        }

        // Fallback para a rota home se nenhum role for encontrado
        return to_route('home');
    }
}
