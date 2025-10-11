<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestNotification extends BaseNotification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $build = parent::build($notifiable);
        return $build
            ->subject(__('email.testNotification.subject'))
            ->greeting(__('email.testNotification.greeting'))
            ->line(__('email.testNotification.line1'))
            ->line(__('email.testNotification.line2'))
            ->line(__('email.testNotification.line3'));
    }
}
