<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;
use App\Models\GlobalSetting;

class ResetPasswordNotification extends BaseResetPasswordNotification
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Set the locale for the email
        $this->setLocale($notifiable);

        return $this->buildMailMessage($this->resetUrl($notifiable));
    }

    /**
     * Set the locale for the notification.
     *
     * @param  mixed  $notifiable
     * @return void
     */
    protected function setLocale($notifiable)
    {
        $globalSetting = GlobalSetting::first();

        // Get locale from user, global settings, or session
        $locale = $notifiable->locale ?? $globalSetting->locale ?? session('locale');

        if ($locale) {
            App::setLocale($locale);
        }
    }

    /**
     * Build the reset password mail message.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject(__('passwords.reset_subject', ['app_name' => config('app.name')]))
            ->greeting(__('app.hello'))
            ->line(__('passwords.reset_line1'))
            ->line(__('passwords.reset_line2'))
            ->action(__('passwords.reset_action'), $url)
            ->line(__('passwords.reset_line3', ['count' => config('auth.passwords.users.expire')]))
            ->line(__('passwords.reset_line4'))
            ->salutation(__('app.regards') . ',' . PHP_EOL . config('app.name'));
    }
}
