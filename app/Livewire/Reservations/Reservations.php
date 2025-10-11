<?php

namespace App\Livewire\Reservations;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Reservation;
use Livewire\Attributes\On;

class Reservations extends Component
{

    protected $listeners = ['refreshKots' => '$refresh'];
    public $dateRangeType;
    public $startDate;
    public $endDate;
    public $showAddReservation = false;
    public $search = '';

    public function mount()
    {
        // Load date range type from cookie
        $this->dateRangeType = request()->cookie('reservations_date_range_type', 'currentWeek');
        $this->startDate = now()->startOfWeek()->format('m/d/Y');
        $this->endDate = now()->endOfWeek()->format('m/d/Y');

        $this->setDateRange();
    }

    public function setDateRange()
    {
        $ranges = [
            'today' => [now()->startOfDay(), now()->startOfDay()],
            'lastWeek' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            'nextWeek' => [now()->addWeek()->startOfWeek(), now()->addWeek()->endOfWeek()],
            'last7Days' => [now()->subDays(7), now()->startOfDay()],
            'currentMonth' => [now()->startOfMonth(), now()->endOfMonth()],
            'lastMonth' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'currentYear' => [now()->startOfYear(), now()->startOfDay()],
            'lastYear' => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()],
            'default' => [now()->startOfWeek(), now()->endOfWeek()],
        ];

        [$start, $end] = $ranges[$this->dateRangeType] ?? $ranges['default'];

        $this->startDate = $start->format('m/d/Y');
        $this->endDate = $end->format('m/d/Y');
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

    public function updatedDateRangeType($value)
    {
        cookie()->queue(cookie('reservations_date_range_type', $value, 60 * 24 * 30)); // 30 days
    }

    public function render()
    {

        if (!in_array('Table Reservation', restaurant_modules())) {
            return view('livewire.license-expire');
        }

        $start = Carbon::createFromFormat('m/d/Y', $this->startDate)->startOfDay()->toDateTimeString();
        $end = Carbon::createFromFormat('m/d/Y', $this->endDate)->endOfDay()->toDateTimeString();

        $reservations = Reservation::with('customer', 'table')
            ->orderBy('reservation_date_time', 'asc')
            ->whereDate('reservation_date_time', '>=', $start)
            ->whereDate('reservation_date_time', '<=', $end)
            ->where(function ($query) {
                $query->whereHas('customer', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->get();

        return view('livewire.reservations.reservations', [
            'reservations' => $reservations
        ]);
    }
}
