<?php

namespace App\Livewire\Reports;

use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\RestaurantCharge;
use App\Exports\SalesReportExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PaymentGatewayCredential;
use App\Models\User;

class SalesReport extends Component
{
    public $dateRangeType = 'currentWeek';
    public $startDate;
    public $endDate;
    public $startTime = '00:00'; // Default start time
    public $endTime = '23:59';  // Default end time
    public $currencyId;
    public $filterByWaiter = '';
    public $waiters = [];
    public $selectedWaiter = '';

    public function mount()
    {
        abort_unless(in_array('Report', restaurant_modules()), 403);
        abort_unless(user_can('Show Reports'), 403);

        // Centralize currency ID
        $this->currencyId = restaurant()->currency_id;

        // Load date range type from cookie
        $this->dateRangeType = request()->cookie('sales_report_date_range_type', 'currentWeek');
        $this->setDateRange();
        // Populate waiters
        $this->waiters = User::whereHas('roles', function($query) {
            $query->where('name', 'Waiter_'.restaurant()->id);
        })->get();

        $this->selectedWaiter = '';
    }

    public function setDateRange()
    {
        $tz = timezone();
        
        $ranges = [
            'today' => [Carbon::now($tz)->startOfDay(), Carbon::now($tz)->endOfDay()],
            'lastWeek' => [Carbon::now($tz)->subWeek()->startOfWeek(), Carbon::now($tz)->subWeek()->endOfWeek()],
            'last7Days' => [Carbon::now($tz)->subDays(7), Carbon::now($tz)->endOfDay()],
            'currentMonth' => [Carbon::now($tz)->startOfMonth(), Carbon::now($tz)->endOfDay()],
            'lastMonth' => [Carbon::now($tz)->subMonth()->startOfMonth(), Carbon::now($tz)->subMonth()->endOfMonth()],
            'currentYear' => [Carbon::now($tz)->startOfYear(), Carbon::now($tz)->endOfDay()],
            'lastYear' => [Carbon::now($tz)->subYear()->startOfYear(), Carbon::now($tz)->subYear()->endOfYear()],
            'currentWeek' => [Carbon::now($tz)->startOfWeek(), Carbon::now($tz)->endOfWeek()],
        ];

        [$start, $end] = $ranges[$this->dateRangeType] ?? $ranges['currentWeek'];
        $this->startDate = $start->format('m/d/Y');
        $this->endDate = $end->format('m/d/Y');
        $this->filterByWaiter = '';
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

    private function prepareDateTimeData()
    {
        $timezone = timezone();
        $offset = Carbon::now($timezone)->format('P');

        $startDateTime = Carbon::createFromFormat('m/d/Y H:i', $this->startDate . ' ' . $this->startTime, $timezone)
            ->setTimezone('UTC')->toDateTimeString();

        $endDateTime = Carbon::createFromFormat('m/d/Y H:i', $this->endDate . ' ' . $this->endTime, $timezone)
            ->setTimezone('UTC')->toDateTimeString();

        $startTime = Carbon::parse($this->startTime, $timezone)->setTimezone('UTC')->format('H:i');
        $endTime = Carbon::parse($this->endTime, $timezone)->setTimezone('UTC')->format('H:i');

        return compact('timezone', 'offset', 'startDateTime', 'endDateTime', 'startTime', 'endTime');
    }

    public function exportReport()
    {
        if (!in_array('Export Report', restaurant_modules())) {
            $this->dispatch('showUpgradeLicense');
            return;
        }

        $dateTimeData = $this->prepareDateTimeData();

        return Excel::download(
            new SalesReportExport(
                $dateTimeData['startDateTime'],
                $dateTimeData['endDateTime'],
                $dateTimeData['startTime'],
                $dateTimeData['endTime'],
                $dateTimeData['timezone'],
                $dateTimeData['offset']
            ),
            'sales-report-' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    public function updatedDateRangeType($value)
    {
        cookie()->queue(cookie('sales_report_date_range_type', $value, 60 * 24 * 30)); // 30 days
    }

    public function filterWaiter()
    {
        $this->filterByWaiter = $this->selectedWaiter;
    }

    public function render()
    {
        $dateTimeData = $this->prepareDateTimeData();

        // Retrieve all taxes and charges
        $charges = RestaurantCharge::all();
        $taxes = Tax::all();
        $restaurant = restaurant();
        $taxMode = $restaurant->tax_mode ?? 'order';

        // Get sales report with charges grouped
        $query = Order::join('payments', 'orders.id', '=', 'payments.order_id')
            ->whereBetween('orders.date_time', [$dateTimeData['startDateTime'], $dateTimeData['endDateTime']])
            ->whereIn('orders.status', ['paid', 'payment_due'])
            ->where(function ($q) use ($dateTimeData) {
                if ($dateTimeData['startTime'] < $dateTimeData['endTime']) {
                    $q->whereRaw('TIME(orders.date_time) BETWEEN ? AND ?', [$dateTimeData['startTime'], $dateTimeData['endTime']]);
                }
                else
                 {
                    $q->where(function ($sub) use ($dateTimeData) {
                        $sub->whereRaw('TIME(orders.date_time) >= ?', [$dateTimeData['startTime']])
                            ->orWhereRaw('TIME(orders.date_time) <= ?', [$dateTimeData['endTime']]);
                    });
                }
            });

        // Get outstanding payments data separately
        $outstandingQuery = Order::whereBetween('date_time', [$dateTimeData['startDateTime'], $dateTimeData['endDateTime']])
            ->where('status', 'payment_due')
            ->where(function ($q) use ($dateTimeData) {
                if ($dateTimeData['startTime'] < $dateTimeData['endTime']) {
                    $q->whereRaw('TIME(date_time) BETWEEN ? AND ?', [$dateTimeData['startTime'], $dateTimeData['endTime']]);
                }
                else
                 {
                    $q->where(function ($sub) use ($dateTimeData) {
                        $sub->whereRaw('TIME(date_time) >= ?', [$dateTimeData['startTime']])
                            ->orWhereRaw('TIME(date_time) <= ?', [$dateTimeData['endTime']]);
                    });
                }
            });

        // Filter by waiter if selected
        if ($this->filterByWaiter) {
            $query->where('orders.waiter_id', $this->filterByWaiter);
            $outstandingQuery->where('waiter_id', $this->filterByWaiter);
        }

        $query = $query->select(
            DB::raw('DATE(CONVERT_TZ(orders.date_time, "+00:00", "' . $dateTimeData['offset'] . '")) as date'),
            DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
            DB::raw('SUM(payments.amount) as total_amount'),
            DB::raw('SUM(CASE WHEN payments.payment_method = "cash" THEN payments.amount ELSE 0 END) as cash_amount'),
            DB::raw('SUM(CASE WHEN payments.payment_method = "card" THEN payments.amount ELSE 0 END) as card_amount'),
            DB::raw('SUM(CASE WHEN payments.payment_method = "upi" THEN payments.amount ELSE 0 END) as upi_amount'),
            DB::raw('SUM(CASE WHEN payments.payment_method = "bank_transfer" THEN payments.amount ELSE 0 END) as bank_transfer_amount'),
            DB::raw('SUM(CASE WHEN payments.payment_method = "razorpay" THEN payments.amount ELSE 0 END) as razorpay_amount'),
            DB::raw('SUM(CASE WHEN payments.payment_method = "stripe" THEN payments.amount ELSE 0 END) as stripe_amount'),
            DB::raw('SUM(CASE WHEN payments.payment_method = "flutterwave" THEN payments.amount ELSE 0 END) as flutterwave_amount'),
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Get outstanding payments data
        $outstandingData = $outstandingQuery->select(
            DB::raw('DATE(CONVERT_TZ(date_time, "+00:00", "' . $dateTimeData['offset'] . '")) as date'),
            DB::raw('COUNT(*) as outstanding_orders'),
            DB::raw('SUM(total) as outstanding_amount')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

        // Get order-level data separately to avoid duplication
        $orderData = Order::whereBetween('date_time', [$dateTimeData['startDateTime'], $dateTimeData['endDateTime']])
            ->whereIn('status', ['paid', 'payment_due'])
            ->where(function ($q) use ($dateTimeData) {
                if ($dateTimeData['startTime'] < $dateTimeData['endTime']) {
                    $q->whereRaw('TIME(date_time) BETWEEN ? AND ?', [$dateTimeData['startTime'], $dateTimeData['endTime']]);
                }
                else
                 {
                    $q->where(function ($sub) use ($dateTimeData) {
                        $sub->whereRaw('TIME(date_time) >= ?', [$dateTimeData['startTime']])
                            ->orWhereRaw('TIME(date_time) <= ?', [$dateTimeData['endTime']]);
                    });
                }
            });

        // Filter by waiter if selected
        if ($this->filterByWaiter) {
            $orderData->where('waiter_id', $this->filterByWaiter);
        }

        $orderData = $orderData->select(
            DB::raw('DATE(CONVERT_TZ(date_time, "+00:00", "' . $dateTimeData['offset'] . '")) as date'),
            DB::raw('SUM(discount_amount) as discount_amount'),
            DB::raw('SUM(tip_amount) as tip_amount'),
            DB::raw('SUM(delivery_fee) as delivery_fee'),
        )
        ->groupBy('date')
        ->get()
        ->keyBy('date');

        // Process taxes and charges dynamically using actual tax breakdown data
        $groupedData = $query->map(function ($item) use ($charges, $taxes, $taxMode, $orderData, $outstandingData) {
            // Get order-level data for this date
            $orderInfo = $orderData->get($item->date);
            $outstandingInfo = $outstandingData->get($item->date);
            $chargeAmounts = [];
            foreach ($charges as $charge) {
                $chargeAmounts[$charge->charge_name] = DB::table('order_charges')
                    ->join('orders', 'order_charges.order_id', '=', 'orders.id')
                    ->join('restaurant_charges', 'order_charges.charge_id', '=', 'restaurant_charges.id')
                    ->where('order_charges.charge_id', $charge->id)
                    ->where('orders.status', 'paid', 'payment_due')
                    ->whereDate('orders.date_time', $item->date)
                    ->where('orders.branch_id', branch()->id)
                    ->sum(DB::raw('CASE WHEN restaurant_charges.charge_type = "percent"
                THEN (restaurant_charges.charge_value / 100) * orders.sub_total
                ELSE restaurant_charges.charge_value END')) ?? 0;
            }

            // Get tax breakdown from both item and order level taxes - flexible approach
            $taxAmounts = [];
            $totalTaxAmount = 0;
            $taxDetails = [];

            // Initialize tax amounts for all taxes
            foreach ($taxes as $tax) {
                $taxAmounts[$tax->tax_name] = 0;
                $taxDetails[$tax->tax_name] = [
                    'name' => $tax->tax_name,
                    'percent' => $tax->tax_percent,
                    'total_amount' => 0,
                    'items_count' => 0
                ];
            }

            // First, try to get item-level tax data (regardless of current tax mode)
            $itemTaxData = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->join('menu_item_tax', 'menu_items.id', '=', 'menu_item_tax.menu_item_id')
                ->join('taxes', 'menu_item_tax.tax_id', '=', 'taxes.id')
                ->where('orders.status', 'paid')
                ->where('orders.branch_id', branch()->id)
                ->whereDate('orders.date_time', $item->date)
                ->select(
                    'taxes.tax_name',
                    'taxes.tax_percent',
                    'order_items.tax_amount',
                    'order_items.quantity',
                    'order_items.order_id',
                    'menu_items.id as menu_item_id'
                )
                ->get();

            // Process item-level taxes if found
            if ($itemTaxData->isNotEmpty()) {
                // Group by order_id and menu_item_id to calculate tax properly per order item
                $orderItemGroups = $itemTaxData->groupBy(['order_id', 'menu_item_id']);

                foreach ($orderItemGroups as $orderId => $menuItems) {
                    foreach ($menuItems as $menuItemId => $itemTaxes) {
                        $totalTaxPercent = $itemTaxes->sum('tax_percent');
                        $orderItemTaxAmount = $itemTaxes->first()->tax_amount ?? 0;

                        foreach ($itemTaxes as $taxItem) {
                            $taxName = $taxItem->tax_name;
                            $taxPercent = $taxItem->tax_percent;

                            // Calculate proportional tax amount for this specific order item
                            $proportionalAmount = $totalTaxPercent > 0 ?
                                ($orderItemTaxAmount * ($taxPercent / $totalTaxPercent)) : 0;

                            $taxAmounts[$taxName] += $proportionalAmount;
                            $taxDetails[$taxName]['total_amount'] += $proportionalAmount;
                            $taxDetails[$taxName]['items_count'] += $taxItem->quantity;
                        }
                    }
                }
            }

            // Second, try to get order-level tax data (regardless of current tax mode)
            $orderTaxData = DB::table('order_taxes')
                ->join('orders', 'order_taxes.order_id', '=', 'orders.id')
                ->join('taxes', 'order_taxes.tax_id', '=', 'taxes.id')
                ->where('orders.status', 'paid')
                ->where('orders.branch_id', branch()->id)
                ->whereDate('orders.date_time', $item->date)
                ->select(
                    'taxes.tax_name',
                    'taxes.tax_percent',
                    'orders.sub_total',
                    'orders.discount_amount',
                    'orders.id as order_id'
                )
                ->get();

            // Process order-level taxes if found
            if ($orderTaxData->isNotEmpty()) {
                foreach ($orderTaxData as $orderTax) {
                    $taxName = $orderTax->tax_name;
                    $taxAmount = ($orderTax->tax_percent / 100) * ($orderTax->sub_total - ($orderTax->discount_amount ?? 0));

                    $taxAmounts[$taxName] += $taxAmount;
                    $taxDetails[$taxName]['total_amount'] += $taxAmount;
                    $taxDetails[$taxName]['items_count'] += 1; // Count as one order
                }
            }

            // If neither item nor order taxes found, try fallback calculation
            if (empty($itemTaxData) && empty($orderTaxData)) {
                foreach ($taxes as $tax) {
                    // Try item-level calculation using direct tax amount from order_items
                    $itemTaxAmount = DB::table('order_items')
                        ->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->join('menu_item_tax', 'order_items.menu_item_id', '=', 'menu_item_tax.menu_item_id')
                        ->join('taxes', 'menu_item_tax.tax_id', '=', 'taxes.id')
                        ->where('taxes.id', $tax->id)
                        ->where('orders.status', 'paid')
                        ->where('orders.branch_id', branch()->id)
                        ->whereDate('orders.date_time', $item->date)
                        ->sum(DB::raw('
                            CASE
                                WHEN (SELECT COUNT(*) FROM menu_item_tax WHERE menu_item_id = order_items.menu_item_id) > 1
                                THEN (order_items.tax_amount * (taxes.tax_percent /
                                    (SELECT SUM(t.tax_percent) FROM menu_item_tax mit
                                    JOIN taxes t ON mit.tax_id = t.id
                                    WHERE mit.menu_item_id = order_items.menu_item_id)
                                ))
                                ELSE COALESCE(order_items.tax_amount, 0)
                            END
                        ')) ?? 0;

                    $taxAmounts[$tax->tax_name] += $itemTaxAmount;
                    $taxDetails[$tax->tax_name]['total_amount'] += $itemTaxAmount;
                }
            }

            // Calculate total tax amount
            $totalTaxAmount = array_sum($taxAmounts);

            return [
                'date' => $item->date,
                'total_orders' => $item->total_orders,
                'total_amount' => $item->total_amount ?? 0,
                'total_excluding_tip' => ($item->total_amount ?? 0) - ($orderInfo->tip_amount ?? 0),
                'discount_amount' => $orderInfo->discount_amount ?? 0,
                'tip_amount' => $orderInfo->tip_amount ?? 0,
                'delivery_fee' => $orderInfo->delivery_fee ?? 0,
                'cash_amount' => $item->cash_amount ?? 0,
                'card_amount' => $item->card_amount ?? 0,
                'upi_amount' => $item->upi_amount ?? 0,
                'bank_transfer_amount' => $item->bank_transfer_amount ?? 0,
                'razorpay_amount' => $item->razorpay_amount ?? 0,
                'stripe_amount' => $item->stripe_amount ?? 0,
                'flutterwave_amount' => $item->flutterwave_amount ?? 0,
                'outstanding_orders' => $outstandingInfo->outstanding_orders ?? 0,
                'outstanding_amount' => $outstandingInfo->outstanding_amount ?? 0,
                'charges' => $chargeAmounts,
                'taxes' => $taxAmounts,
                'tax_details' => $taxDetails,
                'total_tax_amount' => $totalTaxAmount,
            ];
        });

        // Aggregate all taxes across all dates
        $allTaxes = [];
        foreach ($groupedData as $item) {
            if (isset($item['tax_details']) && is_array($item['tax_details'])) {
                foreach ($item['tax_details'] as $taxName => $taxDetail) {
                    if (!isset($allTaxes[$taxName])) {
                        $allTaxes[$taxName] = [
                            'name' => $taxName,
                            'percent' => $taxDetail['percent'] ?? 0,
                            'total_amount' => 0,
                            'items_count' => 0
                        ];
                    }
                    $allTaxes[$taxName]['total_amount'] += $taxDetail['total_amount'] ?? 0;
                    $allTaxes[$taxName]['items_count'] += $taxDetail['items_count'] ?? 0;
                }
            } elseif (isset($item['taxes']) && is_array($item['taxes'])) {
                // Fallback for older tax structure
                foreach ($item['taxes'] as $taxName => $taxAmount) {
                    if (!isset($allTaxes[$taxName])) {
                        // Find tax percentage from the taxes collection
                        $taxPercent = $taxes->where('tax_name', $taxName)->first()->tax_percent ?? 0;
                        $allTaxes[$taxName] = [
                            'name' => $taxName,
                            'percent' => $taxPercent,
                            'total_amount' => 0,
                            'items_count' => 1
                        ];
                    }
                    $allTaxes[$taxName]['total_amount'] += $taxAmount;
                }
            }
        }

        $paymentGateway = PaymentGatewayCredential::select('stripe_status', 'razorpay_status', 'flutterwave_status')
            ->where('restaurant_id', restaurant()->id)
            ->first();

        return view('livewire.reports.sales-report', [
            'menuItems' => $groupedData,
            'charges' => $charges,
            'taxes' => $taxes,
            'paymentGateway' => $paymentGateway,
            'taxMode' => $taxMode,
            'allTaxes' => $allTaxes,
            'currencyId' => $this->currencyId,
            'waiters' => $this->waiters,
            'filterByWaiter' => $this->filterByWaiter,
        ]);
    }

}

