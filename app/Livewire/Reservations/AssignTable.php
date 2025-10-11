<?php

namespace App\Livewire\Reservations;

use App\Models\Area;
use App\Models\Reservation;
use Livewire\Attributes\On;
use Livewire\Component;

class AssignTable extends Component
{

    public $tables;
    public $reservations;
    public $reservation;

    public function mount()
    {
        $this->tables = Area::with(['tables' => function ($query) {
            return $query->where('status', 'active');
        }])->get();

        $this->reservations = Reservation::whereDate('reservation_date_time', $this->reservation->reservation_date_time->toDateString())
            ->whereNotNull('table_id')
            ->get();
    }

    public function isTableAvailable($tableId)
    {
        // Check if there's already a reservation for this table at the same date and time
        $existingReservation = Reservation::where('table_id', $tableId)
            ->whereDate('reservation_date_time', $this->reservation->reservation_date_time->toDateString())
            ->whereTime('reservation_date_time', $this->reservation->reservation_date_time->format('H:i:s'))
            ->where('id', '!=', $this->reservation->id) // Exclude current reservation
            ->first();

        return $existingReservation === null;
    }

    public function getConflictingReservationInfo($tableId)
    {
        $existingReservation = Reservation::where('table_id', $tableId)
            ->whereDate('reservation_date_time', $this->reservation->reservation_date_time->toDateString())
            ->whereTime('reservation_date_time', $this->reservation->reservation_date_time->format('H:i:s'))
            ->where('id', '!=', $this->reservation->id)
            ->with('customer')
            ->first();

        if ($existingReservation) {
            return [
                'customer_name' => $existingReservation->customer->name,
                'party_size' => $existingReservation->party_size,
                'time' => $existingReservation->reservation_date_time->format('h:i A')
            ];
        }

        return null;
    }

    public function setReservationTable($table)
    {
        // Check if table is available before assigning
        if (!$this->isTableAvailable($table)) {
            return; // Don't assign if table is not available
        }

        // If reservation already has a table, we're changing it
        if ($this->reservation->table_id) {
            // Free up the old table
            $this->reservation->table->update(['available_status' => 'available']);
        }

        $this->reservation->update(['table_id' => $table]);
        $this->redirect(route('reservations.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.reservations.assign-table');
    }

}
