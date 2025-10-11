<?php

namespace App\Livewire\Order;

use App\Models\Order;
use App\Models\User;
use App\Models\ReceiptSetting;
use App\Models\KotCancelReason;
use App\Models\PusherSetting;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class Orders extends Component
{

    protected $listeners = ['refreshOrders' => '$refresh'];

    public $orderID;
    public $filterOrders;
    public $dateRangeType;
    public $startDate;
    public $endDate;
    public $receiptSettings;
    public $waiters;
    public $filterWaiter;
    public $pollingEnabled = true;
    public $pollingInterval = 10;
    public $filterOrderType = '';
    public $cancelReasons;
    public $selectedCancelReason;
    public $cancelComment;

    public function mount()
    {
        // Load date range type from cookie
        $this->dateRangeType = request()->cookie('orders_date_range_type', 'today');
        $this->startDate = now()->startOfWeek()->format('m/d/Y');
        $this->endDate = now()->endOfWeek()->format('m/d/Y');
        $this->waiters = User::role('Waiter_' . restaurant()->id)->get();

        // Load polling settings from cookies
        $this->pollingEnabled = filter_var(request()->cookie('orders_polling_enabled', 'true'), FILTER_VALIDATE_BOOLEAN);
        $this->pollingInterval = (int)request()->cookie('orders_polling_interval', 10);


        if (!is_null($this->orderID)) {
            $this->dispatch('showOrderDetail', id: $this->orderID);
        }

        $this->setDateRange();
        $this->cancelReasons = KotCancelReason::where('cancel_order', true)->get();

        if (user()->hasRole('Waiter_' . user()->restaurant_id)) {
            $this->filterWaiter = user()->id;
        }
    }

    public function updatedDateRangeType($value)
    {
        cookie()->queue(cookie('orders_date_range_type', $value, 60 * 24 * 30)); // 30 days
    }

    public function updatedPollingEnabled($value)
    {
        cookie()->queue(cookie('orders_polling_enabled', $value ? 'true' : 'false', 60 * 24 * 30)); // 30 days
    }

    public function updatedPollingInterval($value)
    {
        cookie()->queue(cookie('orders_polling_interval', (int)$value, 60 * 24 * 30)); // 30 days
    }

    public function setDateRange()
    {
        switch ($this->dateRangeType) {
            case 'today':
                $this->startDate = now()->startOfDay()->format('m/d/Y');
                $this->endDate = now()->startOfDay()->format('m/d/Y');
                break;

            case 'currentWeek':
                $this->startDate = now()->startOfWeek()->format('m/d/Y');
                $this->endDate = now()->endOfWeek()->format('m/d/Y');
                break;

            case 'lastWeek':
                $this->startDate = now()->subWeek()->startOfWeek()->format('m/d/Y');
                $this->endDate = now()->subWeek()->endOfWeek()->format('m/d/Y');
                break;

            case 'last7Days':
                $this->startDate = now()->subDays(7)->format('m/d/Y');
                $this->endDate = now()->startOfDay()->format('m/d/Y');
                break;

            case 'currentMonth':
                $this->startDate = now()->startOfMonth()->format('m/d/Y');
                $this->endDate = now()->endOfMonth()->format('m/d/Y');
                break;

            case 'lastMonth':
                $this->startDate = now()->subMonth()->startOfMonth()->format('m/d/Y');
                $this->endDate = now()->subMonth()->endOfMonth()->format('m/d/Y');
                break;

            case 'currentYear':
                $this->startDate = now()->startOfYear()->format('m/d/Y');
                $this->endDate = now()->endOfYear()->format('m/d/Y');
                break;

            case 'lastYear':
                $this->startDate = now()->subYear()->startOfYear()->format('m/d/Y');
                $this->endDate = now()->subYear()->endOfYear()->format('m/d/Y');
                break;

            default:
                $this->startDate = now()->startOfWeek()->format('m/d/Y');
                $this->endDate = now()->endOfWeek()->format('m/d/Y');
                break;
        }
    }

    #[On('setStartDate')]
    public function setStartDate($start)
    {
        $this->startDate = $start;
    }

    #[On('setEndDate')]
    public function setEndDate($end)
    {
        $this->endDate = $end;
    }

    public function showTableOrderDetail($id)
    {
        return $this->redirect(route('pos.order', [$id]), navigate: true);
    }

    public function confirmCancelOrder()
    {
        // Validate that a cancel reason is provided
        if (!$this->selectedCancelReason && !$this->cancelComment) {
            $this->dispatchBrowserEvent('orderCancelled', ['message' => __('modules.settings.cancelReasonRequired'), 'type' => 'error']);
            return;
        }

        $order = Order::find($this->orderID);
        $order->status = 'cancelled';
        $order->cancel_reason_id = $this->selectedCancelReason;
        $order->cancel_comment = $this->cancelComment;
        $order->save();

        $this->dispatchBrowserEvent('orderCancelled', ['message' => __('Order cancelled successfully.')]);
    }

    public function render()
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

        $orders = Order::withCount('items')
            ->with('table', 'waiter', 'customer')
            ->where('status', '<>', 'draft')
            ->orderBy('id', 'desc')
            ->where('orders.date_time', '>=', $start)
            ->where('orders.date_time', '<=', $end);

        if (!empty($this->filterOrderType)) {
            $orders->where('order_type', $this->filterOrderType);
        }

        $orders = $orders->get();

        $kotCount = $orders->filter(function ($order) {
            return $order->status == 'kot';
        });


        $billedCount = $orders->filter(function ($order) {
            return $order->status == 'billed';
        });

        $paymentDue = $orders->filter(function ($order) {
            return $order->status == 'payment_due';
        });

        $paidOrders = $orders->filter(function ($order) {
            return $order->status == 'paid';
        });

        $canceledOrders = $orders->filter(function ($order) {
            return $order->status == 'canceled';
        });

        $outDeliveryOrders = $orders->filter(function ($order) {
            return $order->status == 'out_for_delivery';
        });

        $deliveredOrders = $orders->filter(function ($order) {
            return $order->status == 'delivered';
        });

        switch ($this->filterOrders) {
            case 'kot':
                $orderList = $kotCount;
                break;

            case 'billed':
                $orderList = $billedCount;
                break;

            case 'payment_due':
                $orderList = $paymentDue;
                break;

            case 'paid':
                $orderList = $paidOrders;
                break;

            case 'canceled':
                $orderList = $canceledOrders;
                break;

            case 'out_for_delivery':
                $orderList = $outDeliveryOrders;
                break;

            case 'delivered':
                $orderList = $deliveredOrders;
                break;

            default:
                $orderList = $orders;
                break;
        }





        if ($this->filterWaiter) {
            $orderList = $orderList->filter(function ($order) {
                return $order->waiter_id == $this->filterWaiter;
            });
        }

        $receiptSettings = restaurant()->receiptSetting;

        return view('livewire.order.orders', [
            'orders' => $orderList,
            'kotCount' => count($kotCount),
            'billedCount' => count($billedCount),
            'paymentDueCount' => count($paymentDue),
            'paidOrdersCount' => count($paidOrders),
            'canceledOrdersCount' => count($canceledOrders),
            'outDeliveryOrdersCount' => count($outDeliveryOrders),
            'deliveredOrdersCount' => count($deliveredOrders),
            'receiptSettings' => $receiptSettings, // Pass the fetched receipt settings to the view
            'orderID' => $this->orderID,
        ]);
    }
}
