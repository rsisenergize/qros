<?php

namespace App\Listeners;

use App\Events\SendOrderBillEvent;
use App\Notifications\SendOrderBill;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOrderBillListener implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(SendOrderBillEvent $event): void
    {
        try {
            if ($event->order->customer_id) {
                $event->order->customer->notify(new SendOrderBill($event->order));
            }
        } catch (\Exception $e) {
            Log::error('Error sending order bill notification: ' . $e->getMessage());
        }
    }
}
