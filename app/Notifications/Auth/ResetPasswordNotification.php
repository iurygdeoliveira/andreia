<?php

declare(strict_types=1);

namespace App\Notifications\Auth;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * The password reset URL.
     *
     * @var string
     */
    public $url;

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    #[\Override]
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Notificação de Redefinição de Senha')
            ->view('panels.vendor.mail.html.password-reset', [
                'user' => $notifiable,
                'token' => $this->token,
                'url' => $this->url,
            ]);
    }
}
