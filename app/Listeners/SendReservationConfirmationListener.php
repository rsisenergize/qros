<?php

namespace App\Listeners;

use App\Events\ReservationConfirmationSent;
use App\Notifications\ReservationConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendReservationConfirmationListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReservationConfirmationSent $event): void
    {
        try {
            $customer = $event->reservation->customer;
            if ($customer && $customer->email) {
                $customer->notify(new ReservationConfirmation($event->reservation));
            }
        } catch (\Exception $e) {
            Log::error('Error sending reservation confirmation email: ' . $e->getMessage());
        }
    }
} 