<?php

namespace App\Livewire\Reports;

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Expenses;

class ExpenseReports extends Component
{
    public $reports;
    public $activeReport;

    public function mount()
    {
        $this->reports = Expenses::all();
        $this->activeReport = request('tab') != '' ? request('tab') : 'outstandingPaymentReport';
    }

    #[On('reportsUpdated')]
    public function refreshReports()
    {
        $this->reports = Expenses::all();
    }

    public function render()
    {
        return view('livewire.reports.expense-reports');
    }

}
