<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Order;
use App\Models\RestaurantCharge;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Style};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\{FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles};

class SalesReportExport implements WithMapping, FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected string $startDateTime, $endDateTime;
    protected string $startTime, $endTime, $timezone, $offset;
    protected array $charges, $taxes;
    protected $headingDateTime, $headingEndDateTime, $headingStartTime, $headingEndTime;
    protected $currencyId;

    public function __construct(string $startDateTime, string $endDateTime, string $startTime, string $endTime, string $timezone, string $offset)
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->timezone = $timezone;
        $this->offset = $offset;
        $this->currencyId = restaurant()->currency_id;

        $this->headingDateTime = Carbon::parse($startDateTime)->setTimezone($timezone)->format('Y-m-d');
        $this->headingEndDateTime = Carbon::parse($endDateTime)->setTimezone($timezone)->format('Y-m-d');
        $this->headingStartTime = Carbon::parse($startTime)->setTimezone($timezone)->format('h:i A');
        $this->headingEndTime = Carbon::parse($endTime)->setTimezone($timezone)->format('h:i A');

        $this->charges = RestaurantCharge::pluck('charge_name')->toArray();
        $this->taxes = Tax::select('tax_name', 'tax_percent')->get()->toArray();
    }

    public function headings(): array
    {
        $taxHeadings = array_map(function($tax) {
            return "{$tax['tax_name']} ({$tax['tax_percent']}%)";
        }, $this->taxes);

        $headingTitle = $this->headingDateTime === $this->headingEndDateTime
            ? __('modules.report.salesDataFor') . " {$this->headingDateTime}, " . __('modules.report.timePeriod') . " {$this->headingStartTime} - {$this->headingEndTime}"
            : __('modules.report.salesDataFrom') . " {$this->headingDateTime} " . __('app.to') . " {$this->headingEndDateTime}, " . __('modules.report.timePeriodEachDay') . " {$this->headingStartTime} - {$this->headingEndTime}";

        return [
            [__('menu.salesReport') . ' ' . $headingTitle],
            array_merge(
            [__('app.date'), __('modules.report.totalOrders')],
            $this->charges,
            $taxHeadings,
            [
                __('modules.report.totalTaxAmount'),
                __('modules.order.cash'),
                __('modules.order.upi'),
                __('modules.order.card'),
                __('modules.order.razorpay'),
                __('modules.order.stripe'),
                __('modules.order.flutterwave'),
                __('modules.order.deliveryFee'),
                __('modules.order.discount'),
                __('modules.order.tip'),
                __('modules.order.total'),
                __('modules.order.total') . ' ' . __('modules.order.totalExcludingTip')
            ]
            )
        ];
    }

    public function map($item): array
    {
        $mappedItem = [
            $item['date'],
            $item['total_orders'],
        ];

        foreach ($this->charges as $charge) {
            $mappedItem[] = currency_format($item[$charge] ?? 0, $this->currencyId);
        }

        foreach ($this->taxes as $tax) {
            $mappedItem[] = currency_format($item[$tax['tax_name']] ?? 0, $this->currencyId);
        }

        $mappedItem[] = currency_format($item['total_tax_amount'] ?? 0, $this->currencyId);

        $mappedItem[] = currency_format($item['cash_amount'], $this->currencyId);
        $mappedItem[] = currency_format($item['upi_amount'], $this->currencyId);
        $mappedItem[] = currency_format($item['card_amount'], $this->currencyId);
        $mappedItem[] = currency_format($item['razorpay_amount'], $this->currencyId);
        $mappedItem[] = currency_format($item['stripe_amount'], $this->currencyId);
        $mappedItem[] = currency_format($item['flutterwave_amount'], $this->currencyId);
        $mappedItem[] = currency_format($item['delivery_fee'], $this->currencyId);
        $mappedItem[] = currency_format($item['discount_amount'], $this->currencyId);
        $mappedItem[] = currency_format($item['tip_amount'], $this->currencyId);
        $mappedItem[] = currency_format($item['total_amount'], $this->currencyId);
        $mappedItem[] = currency_format($item['total_excluding_tip'], $this->currencyId);


        return $mappedItem;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'name' => 'Arial'], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f5f5f5']]],
        ];
    }

    public function collection()
    {
        $charges = RestaurantCharge::all()->keyBy('id');
        $taxes = Tax::all()->keyBy('id');

        $query = Order::join('payments', 'orders.id', '=', 'payments.order_id')
            ->whereBetween('orders.date_time', [$this->startDateTime, $this->endDateTime])
            ->where('orders.status', 'paid')
            ->where(function ($q) {
                if ($this->startTime < $this->endTime) {
                    $q->whereRaw("TIME(orders.date_time) BETWEEN ? AND ?", [$this->startTime, $this->endTime]);
                } else {
                    $q->where(function ($sub) {
                        $sub->whereRaw("TIME(orders.date_time) >= ?", [$this->startTime])
                            ->orWhereRaw("TIME(orders.date_time) <= ?", [$this->endTime]);
                    });
                }
            })
            ->select(
                DB::raw("DATE(CONVERT_TZ(orders.date_time, '+00:00', '{$this->offset}')) as date"),
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(payments.amount) as total_amount'),
                DB::raw('SUM(CASE WHEN payments.payment_method = "cash" THEN payments.amount ELSE 0 END) as cash_amount'),
                DB::raw('SUM(CASE WHEN payments.payment_method = "card" THEN payments.amount ELSE 0 END) as card_amount'),
                DB::raw('SUM(CASE WHEN payments.payment_method = "upi" THEN payments.amount ELSE 0 END) as upi_amount'),
                DB::raw('SUM(CASE WHEN payments.payment_method = "razorpay" THEN payments.amount ELSE 0 END) as razorpay_amount'),
                DB::raw('SUM(CASE WHEN payments.payment_method = "stripe" THEN payments.amount ELSE 0 END) as stripe_amount'),
                DB::raw('SUM(CASE WHEN payments.payment_method = "flutterwave" THEN payments.amount ELSE 0 END) as flutterwave_amount'),
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get order-level data separately to avoid duplication
        $orderData = Order::whereBetween('date_time', [$this->startDateTime, $this->endDateTime])
            ->where('status', 'paid')
            ->where(function ($q) {
                if ($this->startTime < $this->endTime) {
                    $q->whereRaw("TIME(date_time) BETWEEN ? AND ?", [$this->startTime, $this->endTime]);
                } else {
                    $q->where(function ($sub) {
                        $sub->whereRaw("TIME(date_time) >= ?", [$this->startTime])
                            ->orWhereRaw("TIME(date_time) <= ?", [$this->endTime]);
                    });
                }
            })
            ->select(
                DB::raw("DATE(CONVERT_TZ(date_time, '+00:00', '{$this->offset}')) as date"),
                DB::raw('SUM(discount_amount) as discount_amount'),
                DB::raw('SUM(tip_amount) as tip_amount'),
                DB::raw('SUM(delivery_fee) as delivery_fee'),
            )
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $data = $query->map(function ($item) use ($charges, $taxes, $orderData) {
            // Get order-level data for this date
            $orderInfo = $orderData->get($item->date);
            
            $row = [
                'date' => $item->date,
                'total_orders' => $item->total_orders,
                'total_amount' => $item->total_amount ?? 0,
                'total_excluding_tip' => ($item->total_amount ?? 0) - ($orderInfo->tip_amount ?? 0),
                'delivery_fee' => $orderInfo->delivery_fee ?? 0,
                'tip_amount' => $orderInfo->tip_amount ?? 0,
                'cash_amount' => $item->cash_amount ?? 0,
                'card_amount' => $item->card_amount ?? 0,
                'upi_amount' => $item->upi_amount ?? 0,
                'discount_amount' => $orderInfo->discount_amount ?? 0,
                'razorpay_amount' => $item->razorpay_amount ?? 0,
                'stripe_amount' => $item->stripe_amount ?? 0,
                'flutterwave_amount' => $item->flutterwave_amount ?? 0,
            ];

            // Process charges dynamically using actual charge data
            $chargeAmounts = [];
            foreach ($charges as $charge) {
                $chargeAmounts[$charge->charge_name] = DB::table('order_charges')
                    ->join('orders', 'order_charges.order_id', '=', 'orders.id')
                    ->join('restaurant_charges', 'order_charges.charge_id', '=', 'restaurant_charges.id')
                    ->where('order_charges.charge_id', $charge->id)
                    ->where('orders.status', 'paid')
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

            // Add charge amounts to row
            foreach ($charges as $charge) {
                $row[$charge->charge_name] = $chargeAmounts[$charge->charge_name] ?? 0;
            }

            // Add tax amounts to row
            foreach ($taxes as $tax) {
                $row[$tax->tax_name] = $taxAmounts[$tax->tax_name] ?? 0;
            }

            $row['total_tax_amount'] = $totalTaxAmount;

            return collect($row);
        });

        return $data;
    }
}
