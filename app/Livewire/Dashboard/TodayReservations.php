<?php

namespace App\Livewire\Dashboard;

use App\Models\Reservation;
use Livewire\Component;

class TodayReservations extends Component
{

    public function render()
    {
        $count = Reservation::whereDate('reservation_date_time', '>=', now(timezone())->startOfDay()->toDateTimeString())
            ->whereDate('reservation_date_time', '<=', now(timezone())->endOfDay()->toDateTimeString())
            ->where('reservation_status', 'Confirmed')
            ->whereNull('table_id')
            ->count();

        return view('livewire.dashboard.today-reservations', ['count' => $count]);
    }

    public function refreshReservations()
    {
        $this->dispatch('$refresh');
    }
}
