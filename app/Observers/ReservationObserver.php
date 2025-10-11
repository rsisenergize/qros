<?php

namespace App\Observers;

use App\Models\Reservation;
use App\Events\TodayReservationCreatedEvent;

class ReservationObserver
{

    public function creating(Reservation $reservation)
    {
        if (branch()) {
            $reservation->branch_id = branch()->id;
        }
    }

    public function saved(Reservation $reservation)
    {
        $count = Reservation::whereDate('reservation_date_time', '>=', now(timezone())->startOfDay()->toDateTimeString())
            ->whereDate('reservation_date_time', '<=', now(timezone())->endOfDay()->toDateTimeString())
            ->where('reservation_status', 'Confirmed')
            ->whereNull('table_id')
            ->count();

        event(new TodayReservationCreatedEvent($count));
    }
}
