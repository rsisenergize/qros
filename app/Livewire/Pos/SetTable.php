<?php

namespace App\Livewire\Pos;

use App\Models\Area;
use App\Models\Reservation;
use Livewire\Attributes\On;
use Livewire\Component;

class SetTable extends Component
{

    public $tables;
    public $reservations;

    private function loadTables()
    {
        return Area::with(['tables' => function ($query) {
            $query->where('available_status', '<>', 'running')
                ->where('status', 'active');
        }])->get();
    }

    public function mount()
    {
        $this->tables = $this->loadTables();

        $this->reservations = Reservation::whereDate('reservation_date_time', now(timezone())->toDateString())
            ->whereNotNull('table_id')
            ->get();
    }

    #[On('posOrderSuccess')]
    public function posOrderSuccess()
    {
        $this->tables = $this->loadTables();
    }

    public function setOrderTable($table)
    {
        $this->dispatch('setTable', table: $table);
        $this->tables = $this->loadTables();
    }

    public function render()
    {
        return view('livewire.pos.set-table');
    }

}
