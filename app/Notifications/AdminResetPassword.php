<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminResetPassword extends Notification
{
    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reset your admin password')
            ->line('A password reset was requested for your admin account.')
            ->action('Reset password', rtrim(config('app.url'), '/').route('admin.password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false))
            ->line('This link expires in '.config('auth.passwords.admin_users.expire').' minutes.')
            ->line('If you did not request this, you can ignore this email.');
    }
}
