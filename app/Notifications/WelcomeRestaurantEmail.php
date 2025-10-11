<?php

namespace App\Notifications;

use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeRestaurantEmail extends BaseNotification
{

    public $restaurantDetails;
    public $password;

    /**
     * Create a new notification instance.
     */
    public function __construct(Restaurant $restaurantDetails, $password)
    {
        $this->restaurantDetails = $restaurantDetails;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        $build = parent::build($notifiable);

        if (module_enabled('Subdomain')) {
            $loginUrl = 'https://' . $this->restaurantDetails->sub_domain;
        } else {
            $loginUrl = url('/');
        }

        $siteName = global_setting()->name;
        return $build
            ->subject(__('email.welcomeRestaurant.subject', ['site_name' => $siteName]))
            ->greeting(__('email.welcomeRestaurant.greeting', ['name' => $notifiable->name]))
            ->line(__('email.welcomeRestaurant.line1', ['site_name' => $siteName]))
            ->line(__('email.welcomeRestaurant.line8', ['email' => $notifiable->email]))
            ->line(__('email.welcomeRestaurant.line9', ['password' => $this->password]))
            ->line(__('email.welcomeRestaurant.line10', ['login_url' => $loginUrl]))
            ->line(__('email.welcomeRestaurant.line2'))
            ->line(__('email.welcomeRestaurant.line3'))
            ->line(__('email.welcomeRestaurant.line4'))
            ->line(__('email.welcomeRestaurant.line5'))
            ->line(__('email.welcomeRestaurant.line6',  ['site_name' => $siteName]))
            ->line(__('email.welcomeRestaurant.line7', ['site_name' => $siteName]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
