<?php

namespace App\Listeners;

use App\Models\OrganizationInvite;
use Illuminate\Auth\Events\Login;

class AcceptPendingInvite
{
    public function handle(Login $event): void
    {
        $token = session()->get('pending_invite_token');

        if (! $token) {
            return;
        }

        if (! OrganizationInvite::byToken($token)->pending()->exists()) {
            return;
        }

        session()->put('url.intended', route('filament.auth.pages.accept-invite', ['token' => $token]));
    }
}
