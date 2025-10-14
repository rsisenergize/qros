<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtp extends Notification implements ShouldQueue
{
    use Queueable;

    protected $otp;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(config('app.name') . ' - ' . __('auth.loginVerificationCode'))
            ->greeting(__('app.hello') . ' ' . $notifiable->name . '!')
            ->line(__('auth.youHaveRequestedToLogin'))
            ->line($this->otp)
            ->line(__('auth.thisCodeWillExpireIn') . ' **10 ' . __('auth.minutes') . '**.')
            ->line(__('auth.ifYouDidNotRequestThisLoginCode'))
            ->salutation(__('auth.bestRegards'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'otp' => $this->otp,
            'type' => 'login',
        ];
    }
} 