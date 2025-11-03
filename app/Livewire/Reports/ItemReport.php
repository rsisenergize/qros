<?php

namespace App\Livewire\Reports;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\MenuItem;
use Livewire\Attributes\On;
use App\Exports\ItemReportExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Scopes\AvailableMenuItemScope;

class ItemReport extends Component
{

    public $dateRangeType;
    public $startDate;
    public $endDate;
    public $startTime = '00:00'; // Default start time
    public $endTime = '23:59';  // Default end time
    public $searchTerm;

    public function mount()
    {
        abort_if(!in_array('Report', restaurant_modules()), 403);
        abort_if((!user_can('Show Reports')), 403);

        $tz = timezone();
        
        // Load date range type from cookie
        $this->dateRangeType = request()->cookie('item_report_date_range_type', 'currentWeek');
        $this->startDate = Carbon::now($tz)->startOfWeek()->format('m/d/Y');
        $this->endDate = Carbon::now($tz)->endOfWeek()->format('m/d/Y');
    }

    public function updatedDateRangeType($value)
    {
        cookie()->queue(cookie('item_report_date_range_type', $value, 60 * 24 * 30)); // 30 days
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

    public function exportReport()
    {
        if (!in_array('Export Report', restaurant_modules())) {
            $this->dispatch('showUpgradeLicense');
        } else {
            $data = $this->prepareDateTimeData();

            return Excel::download(
                new ItemReportExport($data['startDateTime'], $data['endDateTime'], $data['startTime'], $data['endTime'], $data['timezone'], $this->searchTerm),
                'item-report-' . now()->toDateTimeString() . '.xlsx'
            );
        }
    }

    private function prepareDateTimeData()
    {
        $timezone = timezone();

        $startDateTime = Carbon::createFromFormat('m/d/Y H:i', "{$this->startDate} {$this->startTime}", $timezone)
            ->setTimezone('UTC')->toDateTimeString();

        $endDateTime = Carbon::createFromFormat('m/d/Y H:i', "{$this->endDate} {$this->endTime}", $timezone)
            ->setTimezone('UTC')->toDateTimeString();

        $startTime = Carbon::parse($this->startTime, $timezone)->setTimezone('UTC')->format('H:i');
        $endTime = Carbon::parse($this->endTime, $timezone)->setTimezone('UTC')->format('H:i');

        return compact('timezone', 'startDateTime', 'endDateTime', 'startTime', 'endTime');
    }

    public function render()
    {
        $dateTimeData = $this->prepareDateTimeData();

        $query = MenuItem::withoutGlobalScope(AvailableMenuItemScope::class)
            ->with(['orders' => function ($q) use ($dateTimeData) {
                return $q->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->whereBetween('orders.date_time', [$dateTimeData['startDateTime'], $dateTimeData['endDateTime']])
                    ->where('orders.status', 'paid')
                    ->where(function ($q) use ($dateTimeData) {
                        if ($dateTimeData['startTime'] < $dateTimeData['endTime']) {
                            $q->whereRaw("TIME(orders.date_time) BETWEEN ? AND ?", [$dateTimeData['startTime'], $dateTimeData['endTime']]);
                        } else {
                            $q->where(function ($sub) use ($dateTimeData) {
                                $sub->whereRaw("TIME(orders.date_time) >= ?", [$dateTimeData['startTime']])
                                    ->orWhereRaw("TIME(orders.date_time) <= ?", [$dateTimeData['endTime']]);
                            });
                        }
                    });
            }, 'category', 'variations']);


        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('item_name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('category', function ($q) {
                        $q->where('category_name', 'like', '%' . $this->searchTerm . '%');
                    });
            });
        }

        $menuItems = $query->get();

        return view('livewire.reports.item-report', [
            'menuItems' => $menuItems
        ]);
    }

}
