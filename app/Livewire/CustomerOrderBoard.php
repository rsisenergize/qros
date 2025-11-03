<?php

namespace App\Livewire;

use App\Models\Kot;
use Livewire\Component;
use Carbon\Carbon;
use App\Enums\OrderStatus;

class CustomerOrderBoard extends Component
{
    public array $preparingOrders = [];
    public array $readyOrders = [];

    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = now()->startOfDay()->format('m/d/Y');
        $this->endDate = now()->endOfDay()->format('m/d/Y');
    }

    public function render()
    {
        $this->loadOrders();
        return view('livewire.customer-order-board');
    }

    public function refreshBoard(): void
    {
        $this->loadOrders();
    }

    private function loadOrders(): void
    {
        $tz = timezone();

        $start = Carbon::createFromFormat('m/d/Y', $this->startDate, $tz)
            ->startOfDay()
            ->setTimezone('UTC')
            ->toDateTimeString();

        $end = Carbon::createFromFormat('m/d/Y', $this->endDate, $tz)
            ->endOfDay()
            ->setTimezone('UTC')
            ->toDateTimeString();

        $baseQuery = Kot::query()
            ->select(['kots.id', 'kots.order_id', 'kots.token_number', 'kots.status'])
            ->join('orders', 'kots.order_id', '=', 'orders.id')
            ->whereNotNull('kots.token_number')
            ->where('kots.status', '!=', 'cancelled')
            ->where('orders.date_time', '>=', $start)
            ->where('orders.date_time', '<=', $end)
            ->with(['order' => function ($query) {
                $query->select(['id', 'order_number', 'formatted_order_number', 'order_type_id'])
                    ->with('orderType:id,order_type_name');
            }])
            ->orderBy('kots.id', 'desc');

        $preparing = (clone $baseQuery)
            ->whereIn('kots.status', ['in_kitchen', 'pending_confirmation'])
            ->whereNotIn('orders.order_status', ['out_for_delivery', 'delivered', 'served'])
            ->limit(15)
            ->get();

        $ready = (clone $baseQuery)
            ->where('kots.status', 'food_ready')
            ->whereNotIn('orders.order_status', ['out_for_delivery', 'delivered', 'served'])
            ->limit(15)
            ->get();

        $this->preparingOrders = $preparing->map(function (Kot $kot) {
            return [
                'id' => $kot->order_id,
                'display_number' => $kot->order?->show_formatted_order_number,
                'token' => $kot->token_number,
                'order_type' => $kot->order?->orderType?->order_type_name,
            ];
        })->toArray();

        $this->readyOrders = $ready->map(function (Kot $kot) {
            return [
                'id' => $kot->order_id,
                'display_number' => $kot->order?->show_formatted_order_number,
                'token' => $kot->token_number,
                'order_type' => $kot->order?->orderType?->order_type_name,
            ];
        })->toArray();
    }
}


