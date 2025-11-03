<?php

namespace App\Livewire\Reports;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Order;
use App\Models\DeliveryPlatform;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use App\Models\OrderType;

class DeliveryAppReport extends Component
{
    public $dateRangeType;
    public $startDate;
    public $endDate;
    public $startTime = '00:00'; // Default start time
    public $endTime = '23:59';  // Default end time
    public $searchTerm;
    public $selectedDeliveryApp = 'all';

    public function mount()
    {
        abort_if(!in_array('Report', restaurant_modules()), 403);
        abort_if((!user_can('Show Reports')), 403);

        $tz = timezone();
        
        // Load date range type from cookie
        $this->dateRangeType = request()->cookie('delivery_app_report_date_range_type', 'currentWeek');
        $this->startDate = Carbon::now($tz)->startOfWeek()->format('m/d/Y');
        $this->endDate = Carbon::now($tz)->endOfWeek()->format('m/d/Y');
    }

    public function updatedDateRangeType($value)
    {
        cookie()->queue(cookie('delivery_app_report_date_range_type', $value, 60 * 24 * 30)); // 30 days
    }

    public function setDateRange()
    {
        $tz = timezone();
        
        switch ($this->dateRangeType) {
        case 'today':
            $this->startDate = Carbon::now($tz)->startOfDay()->format('m/d/Y');
            $this->endDate = Carbon::now($tz)->startOfDay()->format('m/d/Y');
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
            $this->endDate = Carbon::now($tz)->startOfDay()->format('m/d/Y');
            break;

        case 'lastMonth':
            $this->startDate = Carbon::now($tz)->subMonth()->startOfMonth()->format('m/d/Y');
            $this->endDate = Carbon::now($tz)->subMonth()->endOfMonth()->format('m/d/Y');
            break;

        case 'currentYear':
            $this->startDate = Carbon::now($tz)->startOfYear()->format('m/d/Y');
            $this->endDate = Carbon::now($tz)->startOfDay()->format('m/d/Y');
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

        // Get all delivery platforms
        $deliveryApps = DeliveryPlatform::all();

        $deliveryOrderTypes = OrderType::where('slug', 'delivery')->first();

        // Get aggregated data grouped by delivery app (including null for direct delivery)
        $deliveryAppStats = Order::select(
                'delivery_app_id',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(sub_total) as total_revenue'),
                DB::raw('SUM(delivery_fee) as total_delivery_fees'),
                DB::raw('AVG(sub_total) as avg_order_value')
            )
            ->where('date_time', '>=', $start)
            ->where('date_time', '<=', $end)
            ->where('status', 'paid')
            ->where('order_type_id', $deliveryOrderTypes->id);

        // Filter by selected delivery app for stats
        if ($this->selectedDeliveryApp !== 'all') {
            if ($this->selectedDeliveryApp === 'direct') {
                // Direct delivery (no delivery app)
                $deliveryAppStats->whereNull('delivery_app_id');
            } else {
                // Specific delivery app
                $deliveryAppStats->where('delivery_app_id', $this->selectedDeliveryApp);
            }
        }

        $deliveryAppStats = $deliveryAppStats->groupBy('delivery_app_id')->get();

        // Calculate commission for each delivery app
        $reportData = $deliveryAppStats->map(function ($stat) use ($deliveryApps) {
            $deliveryApp = $deliveryApps->firstWhere('id', $stat->delivery_app_id);
            
            // Handle direct delivery (no delivery app)
            if (!$deliveryApp && $stat->delivery_app_id === null) {
                return [
                    'delivery_app' => (object) [
                        'id' => null,
                        'name' => __('modules.report.directDelivery'),
                        'logo_url' => null,
                        'commission_type' => 'percent',
                        'commission_value' => 0,
                    ],
                    'total_orders' => $stat->total_orders,
                    'total_revenue' => $stat->total_revenue,
                    'total_delivery_fees' => $stat->total_delivery_fees,
                    'avg_order_value' => $stat->avg_order_value,
                    'commission' => 0,
                    'net_revenue' => $stat->total_revenue,
                    'is_direct' => true,
                ];
            }

            // Skip if delivery app not found and not direct delivery
            if (!$deliveryApp) {
                return null;
            }

            $commission = 0;
            if ($deliveryApp->commission_type === 'percent') {
                $commission = ($stat->total_revenue * $deliveryApp->commission_value) / 100;
            } else {
                $commission = $deliveryApp->commission_value * $stat->total_orders;
            }

            return [
                'delivery_app' => $deliveryApp,
                'total_orders' => $stat->total_orders,
                'total_revenue' => $stat->total_revenue,
                'total_delivery_fees' => $stat->total_delivery_fees,
                'avg_order_value' => $stat->avg_order_value,
                'commission' => $commission,
                'net_revenue' => $stat->total_revenue - $commission,
                'is_direct' => false,
            ];
        })->filter()->values();

        // Calculate overall totals
        $totalOrders = $reportData->sum('total_orders');
        $totalRevenue = $reportData->sum('total_revenue');
        $totalCommission = $reportData->sum('commission');
        $totalDeliveryFees = $reportData->sum('total_delivery_fees');
        $netRevenue = $reportData->sum('net_revenue');

        return view('livewire.reports.delivery-app-report', [
            'deliveryApps' => $deliveryApps,
            'reportData' => $reportData,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'totalCommission' => $totalCommission,
            'totalDeliveryFees' => $totalDeliveryFees,
            'netRevenue' => $netRevenue,
        ]);
    }
}
