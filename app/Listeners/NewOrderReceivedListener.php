<?php

namespace App\Listeners;

use App\Events\SendNewOrderReceived;
use App\Http\Controllers\DashboardController;
use App\Models\User;
use App\Notifications\NewOrderReceived;
use App\Scopes\BranchScope;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NewOrderReceivedListener
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
    public function handle(SendNewOrderReceived $event): void
    {
        $users = User::role('Admin_' . $event->order->branch->restaurant_id)->where('restaurant_id', $event->order->branch->restaurant->id)->withoutGlobalScope(BranchScope::class)->get();

        try {
            Notification::send($users, new NewOrderReceived($event->order));
        } catch (\Exception $e) {
            \Log::error('Error sending new order received notification: ' . $e->getMessage());
        }

        $pushNotification = new DashboardController();
        $pushUsersIds = [$users->pluck('id')->toArray()];
        $pushNotification->sendPushNotifications($pushUsersIds, __('email.newOrder.subject'), $event->order->show_formatted_order_number, route('orders.index'));
    }
}
