<?php

namespace App\Livewire\Dashboard;

use App\Models\Order;
use Carbon\Carbon;
use Livewire\Component;

class TodayOrderList extends Component
{

    protected $listeners = ['refreshOrders' => '$refresh'];

    public function render()
    {
        $tz = timezone();
        
        $start = Carbon::now($tz)->startOfDay()->setTimezone($tz)->toDateTimeString();
        $end = Carbon::now($tz)->endOfDay()->setTimezone($tz)->toDateTimeString();

        $orders = Order::withCount('items')->with('table', 'waiter', 'orderType')
            ->where('status', '<>', 'canceled')
            ->where('status', '<>', 'draft')
            ->orderBy('id', 'desc')
            ->whereDate('orders.date_time', '>=', $start)->whereDate('orders.date_time', '<=', $end);

        $orders = $orders->get();

        return view('livewire.dashboard.today-order-list', [
            'orders' => $orders
        ]);
    }

}
