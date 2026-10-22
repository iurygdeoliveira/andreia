<?php

namespace App\Http\Controllers;

use App\Models\OrganizationInvite;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AcceptInviteController
{
    public function __invoke(Request $request, string $token): RedirectResponse
    {
        $invite = OrganizationInvite::query()
            ->where('token', $token)
            ->pending()
            ->first();

        if (! $invite) {
            abort(404, __('organization.validation.invalid_invitation'));
        }

        $panel = Filament::getPanel('auth');
        $acceptUrl = route('filament.auth.pages.accept-invite', ['token' => $token]);

        if (auth()->check()) {
            return redirect()->to($acceptUrl);
        }

        session()->put('pending_invite_token', $token);

        $userModel = config('filament-tenant-members.models.user', User::class);

        if ($userModel::where('email', $invite->email)->exists()) {
            return redirect()->to($panel->getLoginUrl());
        }

        session()->put('pending_invite_email', $invite->email);

        return redirect()->to($panel->getLoginUrl());
    }
}
