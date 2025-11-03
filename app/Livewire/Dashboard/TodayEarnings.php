<?php

namespace App\Livewire\Dashboard;

use App\Models\Order;
use Livewire\Component;

class TodayEarnings extends Component
{

    public $orderCount;
    public $percentChange;
    
    public function mount()
    {
        $tz = timezone();
        
        $this->orderCount = Order::whereDate('orders.date_time', '>=', now()->startOfDay()->setTimezone($tz)->toDateTimeString())->whereDate('orders.date_time', '<=', now()->endOfDay()->setTimezone($tz)->toDateTimeString())
            ->where('status', 'paid')
            ->sum('total');
        
        $yesterdayCount = Order::whereDate('orders.date_time', '>=', now()->subDay()->startOfDay()->setTimezone($tz)->toDateTimeString())->whereDate('orders.date_time', '<=', now()->subDay()->endOfDay()->setTimezone($tz)->toDateTimeString())
            ->where('status', 'paid')
            ->sum('total');

        $orderDifference = ($this->orderCount - $yesterdayCount);

        $this->percentChange  = (($orderDifference / ($yesterdayCount == 0 ? 1 : $yesterdayCount)) * 100);

    }

    public function render()
    {
        return view('livewire.dashboard.today-earnings');
    }

}
