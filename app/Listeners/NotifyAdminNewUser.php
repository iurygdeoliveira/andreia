<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\NewUserNotificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotifyAdminNewUser
{
    public function handle(UserRegistered $event): void
    {
        // Buscar apenas o admin específico
        $adminEmail = (string) config('app.admin_email', 'iurygdeoliveira@gmail.com');
        $admin = User::where('email', $adminEmail)->first();

        if ($admin) {
            Mail::to($admin->email)->send(new NewUserNotificationMail($admin, $event->user));
        }
    }
}
