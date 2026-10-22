<?php

namespace App\Listeners;

use App\Models\AuthenticationLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class LogAuthenticationActivity
{
    public function __construct(public Request $request) {}

    public function handle(object $event): void
    {
        if ($event instanceof Login) {
            $this->logLogin($event);
        } elseif ($event instanceof Logout) {
            $this->logLogout($event);
        } elseif ($event instanceof Failed) {
            $this->logFailed($event);
        }
    }

    protected function logLogin(Login $event): void
    {
        $user = $event->user;

        if (! $user) {
            return;
        }

        AuthenticationLog::create([
            'authenticatable_type' => $user::class,
            'authenticatable_id' => $user->getAuthIdentifier(),
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'login_at' => Date::now(),
            'login_successful' => true,
            'location' => [], // Implement IP geolocation if needed later
        ]);
    }

    protected function logLogout(Logout $event): void
    {
        $user = $event->user;

        if (! $user) {
            return;
        }

        $log = AuthenticationLog::where('authenticatable_id', $user->getAuthIdentifier())
            ->where('ip_address', $this->request->ip())
            ->where('user_agent', $this->request->userAgent())
            ->latest('login_at')
            ->first();

        if ($log) {
            $log->update(['logout_at' => Date::now()]);
        }
    }

    protected function logFailed(Failed $event): void
    {
        $user = $event->user;

        if (! $user) {
            return;
        }

        AuthenticationLog::create([
            'authenticatable_type' => $user::class,
            'authenticatable_id' => $user->getAuthIdentifier(),
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'login_at' => Date::now(),
            'login_successful' => false,
        ]);
    }
}
