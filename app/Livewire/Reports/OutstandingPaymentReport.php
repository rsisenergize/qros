<?php

namespace App\Livewire\Reports;

use App\Exports\OutstandingReportExport;
use App\Models\Expenses;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Exports\ItemReportExport;
use Maatwebsite\Excel\Facades\Excel;

class OutstandingPaymentReport extends Component
{
    public $dateRangeType;
    public $startDate;
    public $endDate;
    public $totalAmount;
    public $expenses;

    public function mount()
    {
        abort_if(!in_array('Report', restaurant_modules()), 403);
        abort_if((!user_can('Show Reports')), 403);

        $tz = timezone();
        
        $this->dateRangeType = 'currentWeek';
        $this->startDate = Carbon::now($tz)->startOfWeek()->format('m/d/Y');
        $this->endDate = Carbon::now($tz)->endOfWeek()->format('m/d/Y');
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
        }
        else {
            return Excel::download(new OutstandingReportExport($this->startDate, $this->endDate), 'item-report-' . now()->toDateTimeString() . '.xlsx');
        }
    }

    public function render()
    {
        $start = Carbon::createFromFormat('m/d/Y', $this->startDate)->startOfDay();
        $end = Carbon::createFromFormat('m/d/Y', $this->endDate)->endOfDay();

        $this->expenses = Expenses::with(['category'])
            ->where('payment_status', '=', 'pending')
            ->whereBetween('payment_due_date', [$start, $end])
            ->get();

        $this->totalAmount = $this->expenses->sum('amount');

        return view('livewire.reports.outstanding-payment-report', [
        'expenses' => $this->expenses
        ]);
    }

}
