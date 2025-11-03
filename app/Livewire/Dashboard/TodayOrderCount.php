<?php

namespace App\Livewire\Dashboard;

use App\Models\Order;
use Carbon\Carbon;
use Livewire\Component;

class TodayOrderCount extends Component
{
    public $orderCount;
    public $percentChange;
    
    public function mount()
    {
        $tz = timezone();
        
        $start = Carbon::now($tz)->startOfDay()->setTimezone($tz)->toDateTimeString();
        $end = Carbon::now($tz)->endOfDay()->setTimezone($tz)->toDateTimeString();
        
        $this->orderCount = Order::whereDate('orders.date_time', '>=', $start)
            ->whereDate('orders.date_time', '<=', $end)
            ->where('status', '<>', 'canceled')
            ->where('status', '<>', 'draft')
            ->count();
        
        $yesterdayStart = Carbon::now($tz)->subDay()->startOfDay()->setTimezone($tz)->toDateTimeString();
        $yesterdayEnd = Carbon::now($tz)->subDay()->endOfDay()->setTimezone($tz)->toDateTimeString();
        
        $yesterdayCount = Order::whereDate('orders.date_time', '>=', $yesterdayStart)
            ->whereDate('orders.date_time', '<=', $yesterdayEnd)
            ->where('status', '<>', 'canceled')
            ->where('status', '<>', 'draft')
            ->count();

        $orderDifference = ($this->orderCount - $yesterdayCount);

        $this->percentChange  = (($orderDifference / ($yesterdayCount == 0 ? 1 : $yesterdayCount)) * 100);

    }

    public function render()
    {
        return view('livewire.dashboard.today-order-count');
    }

}
