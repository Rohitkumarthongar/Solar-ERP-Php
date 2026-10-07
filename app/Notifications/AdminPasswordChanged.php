<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminPasswordChanged extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your admin password was changed')
            ->line('Your admin password has been reset. Previous login sessions have been revoked.')
            ->line('If you did not make this change, contact your administrator immediately.');
    }
}
