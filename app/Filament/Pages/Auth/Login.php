<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Traits\Filament\NotificationsTrait;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as AuthLogin;
use Filament\Facades\Filament;

class Login extends AuthLogin
{
    use NotificationsTrait;

    #[\Override]
    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        if ($response === null) {
            return null;
        }

        $user = Filament::auth()->user();
        if ($user instanceof User && $user->isSuspended()) {
            $this->redirect(AccountSuspended::getUrl());

            return null;
        }

        if ($user instanceof User && ! $user->isApproved() && ! session()->has('pending_invite_token')) {
            $this->redirect(VerificationPending::getUrl());

            return null;
        }

        return $response;
    }
}
