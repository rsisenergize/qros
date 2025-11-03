<?php

namespace App\Livewire\Order;

use App\Models\Order;
use App\Models\User;
use App\Models\ReceiptSetting;
use App\Models\KotCancelReason;
use App\Models\PusherSetting;
use App\Models\DeliveryPlatform;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Log;

class Orders extends Component
{

    use LivewireAlert;

    protected $listeners = ['refreshOrders' => '$refresh', 'newOrderCreated' => 'handleNewOrder', 'viewOrder' => 'viewOrder'];

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
    public $deliveryApps;
    public $filterDeliveryApp = '';
    public $cancelReasons;
    public $selectedCancelReason;
    public $cancelComment;

    public function mount()
    {
        $tz = timezone();
        
        // Load date range type from cookie
        $this->dateRangeType = request()->cookie('orders_date_range_type', 'today');
        $this->startDate = Carbon::now($tz)->startOfWeek()->format('m/d/Y');
        $this->endDate = Carbon::now($tz)->endOfWeek()->format('m/d/Y');
        $this->waiters = User::role('Waiter_' . restaurant()->id)->get();
        $this->deliveryApps = DeliveryPlatform::all();

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

        // Initialize session for new orders tracking
        if (!session()->has('orders_count')) {
            $count = $this->getOrdersCount();
            session(['orders_count' => $count]);
        }
    }

    public function handleNewOrder($data = null)
    {
        $recentOrder = Order::with('table', 'customer')
            ->where('status', '<>', 'draft')
            ->orderBy('id', 'desc')
            ->first();

        if ($recentOrder) {
            // Build order description
            $orderDescription = __('New order received') . ': ' . $recentOrder->show_formatted_order_number;

            // Add table info if it exists
            if ($recentOrder->table && $recentOrder->table->table_code) {
                $orderDescription .= ' - ' . __('app.table') . ': ' . $recentOrder->table->table_code;
            }
            // Add customer info for delivery/pickup orders
            else if ($recentOrder->customer && $recentOrder->customer->name) {
                $orderDescription .= ' - ' . $recentOrder->customer->name;
            }

            // Add order type
            if ($recentOrder->order_type) {
                $orderType = ucfirst(str_replace('_', ' ', $recentOrder->order_type));
                $orderDescription .= ' (' . $orderType . ')';
            }

            $this->confirm($orderDescription, [
                'position' => 'center',
                'confirmButtonText' => __('View Order'),
                'confirmButtonColor' => '#16a34a',
                'onConfirmed' => 'viewOrder',
                'showCancelButton' => true,
                'cancelButtonText' => __('app.close'),
                'data' => [
                    'orderID' => $recentOrder->id
                ]
            ]);
        }

        $count = $this->getOrdersCount();
        session(['orders_count' => $count]);

        $this->dispatch('$refresh');
    }

    public function viewOrder($data)
    {
        Log::info('viewOrder called with data:', ['data' => $data]);

        if (is_array($data) && isset($data['orderID'])) {
            Log::info('Redirecting to order:', ['order_id' => $data['orderID']]);
            $orderId = $data['orderID'];
            $url = route('pos.kot', [$orderId]) . '?show-order-detail=true';
            Log::info('Redirect URL:', ['url' => $url]);

            // Use JavaScript redirect for better compatibility with LivewireAlert
            $this->js("window.location.href = '{$url}'");
            return;
        }

        Log::warning('viewOrder: Invalid data format', ['data' => $data]);
    }

    public function refreshNewOrders()
    {
        $this->dispatch('$refresh');
    }

    private function getOrdersCount()
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

        return Order::where('status', '<>', 'draft')
            ->where('orders.date_time', '>=', $start)
            ->where('orders.date_time', '<=', $end)
            ->count();
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
        $tz = timezone();
        
        switch ($this->dateRangeType) {
            case 'today':
                $this->startDate = Carbon::now($tz)->startOfDay()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->startOfDay()->format('m/d/Y');
                break;

            case 'currentWeek':
                $this->startDate = Carbon::now($tz)->startOfWeek()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->endOfWeek()->format('m/d/Y');
                break;

            case 'lastWeek':
                $this->startDate = Carbon::now($tz)->subWeek()->startOfWeek()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->subWeek()->endOfWeek()->format('m/d/Y');
                break;

            case 'last7Days':
                $this->startDate = Carbon::now($tz)->subDays(7)->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->startOfDay()->format('m/d/Y');
                break;

            case 'currentMonth':
                $this->startDate = Carbon::now($tz)->startOfMonth()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->endOfMonth()->format('m/d/Y');
                break;

            case 'lastMonth':
                $this->startDate = Carbon::now($tz)->subMonth()->startOfMonth()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->subMonth()->endOfMonth()->format('m/d/Y');
                break;

            case 'currentYear':
                $this->startDate = Carbon::now($tz)->startOfYear()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->endOfYear()->format('m/d/Y');
                break;

            case 'lastYear':
                $this->startDate = Carbon::now($tz)->subYear()->startOfYear()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->subYear()->endOfYear()->format('m/d/Y');
                break;

            default:
                $this->startDate = Carbon::now($tz)->startOfWeek()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->endOfWeek()->format('m/d/Y');
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

        $this->dispatchBrowserEvent('orderCancelled', ['message' => __('messages.orderCanceled')]);
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
            ->with('table', 'waiter', 'customer', 'orderType', 'deliveryApp')
            ->where('status', '<>', 'draft')
            ->orderBy('id', 'desc')
            ->where('orders.date_time', '>=', $start)
            ->where('orders.date_time', '<=', $end);

        if (!empty($this->filterOrderType)) {
            $orders->where('order_type', $this->filterOrderType);
        }

        if (!empty($this->filterDeliveryApp)) {
            if ($this->filterDeliveryApp === 'direct') {
                $orders->whereNull('delivery_app_id');
            } else {
                $orders->where('delivery_app_id', $this->filterDeliveryApp);
            }
        }

        $orders = $orders->get();

        // Check for new orders and show popup
        $playSound = false;
        $currentCount = $orders->count();

        if (session()->has('orders_count') && session('orders_count') < $currentCount) {
            $playSound = true;

            $recentOrder = Order::with('table', 'customer')
                ->where('status', '<>', 'draft')
                ->orderBy('id', 'desc')
                ->first();

            if ($recentOrder) {
                // Build order description
                $orderDescription = __('New order received') . ': ' . $recentOrder->show_formatted_order_number;

                // Add table info if it exists
                if ($recentOrder->table && $recentOrder->table->table_code) {
                    $orderDescription .= ' - ' . __('app.table') . ': ' . $recentOrder->table->table_code;
                }
                // Add customer info for delivery/pickup orders
                else if ($recentOrder->customer && $recentOrder->customer->name) {
                    $orderDescription .= ' - ' . $recentOrder->customer->name;
                }

                // Add order type
                if ($recentOrder->order_type) {
                    $orderType = ucfirst(str_replace('_', ' ', $recentOrder->order_type));
                    $orderDescription .= ' (' . $orderType . ')';
                }

                $this->confirm($orderDescription, [
                    'position' => 'center',
                    'confirmButtonText' => __('View Order'),
                    'confirmButtonColor' => '#16a34a',
                    'onConfirmed' => 'viewOrder',
                    'showCancelButton' => true,
                    'cancelButtonText' => __('app.close'),
                    'data' => [
                        'orderID' => $recentOrder->id
                    ]
                ]);
            }

            session(['orders_count' => $currentCount]);
        } else if (session()->has('orders_count')) {
            session(['orders_count' => $currentCount]);
        }

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
            'playSound' => $playSound ?? false,
        ]);
    }
}
