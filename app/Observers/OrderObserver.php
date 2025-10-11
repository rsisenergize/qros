<?php

namespace App\Observers;

use App\Models\Order;
use App\Events\OrderCancelled;
use App\Events\TodayOrdersUpdated;
use App\Models\Kot;
use App\Events\OrderUpdated;
use App\Events\OrderSuccessEvent;


class OrderObserver
{

    public function creating(Order $order)
    {
        if (branch() && $order->branch_id == null) {
            $order->branch_id = branch()->id;
        }
    }

    public function created(Order $order)
    {
        $todayKotCount = Kot::join('orders', 'kots.order_id', '=', 'orders.id')
            ->whereDate('kots.created_at', '>=', now()->startOfDay()->toDateTimeString())
            ->whereDate('kots.created_at', '<=', now()->endOfDay()->toDateTimeString())
            ->where('orders.status', '<>', 'canceled')
            ->where('orders.status', '<>', 'draft')
            ->count();

        event(new OrderUpdated($order, 'created'));
        event(new TodayOrdersUpdated($todayKotCount));
    }

    public function updated(Order $order)
    {
        if ($order->isDirty('status') && $order->status == 'canceled') {
            OrderCancelled::dispatch($order);
        }

        $todayKotCount = Kot::join('orders', 'kots.order_id', '=', 'orders.id')
            ->whereDate('kots.created_at', '>=', now()->startOfDay()->toDateTimeString())
            ->whereDate('kots.created_at', '<=', now()->endOfDay()->toDateTimeString())
            ->where('orders.status', '<>', 'canceled')
            ->where('orders.status', '<>', 'draft')
            ->count();

        event(new OrderUpdated($order, 'updated'));
        event(new TodayOrdersUpdated($todayKotCount));

        event(new OrderSuccessEvent($order));
    }
}
