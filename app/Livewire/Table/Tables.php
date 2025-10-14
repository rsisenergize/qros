<?php

namespace App\Livewire\Table;

use App\Models\Area;
use App\Models\Table;
use Livewire\Component;
use App\Models\Reservation;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Tables extends Component
{

    use LivewireAlert;

    public $activeTable;
    public $areaID = null;
    public $showAddTableModal = false;
    public $showEditTableModal = false;
    public $confirmDeleteTableModal = false;
    public $filterAvailable = null;
    public $viewType = 'list';
    public $reservations;
    public $reservedTables;
    public $timeSlotDifference;

    protected $listeners = [
        'tableLockUpdated' => 'handleTableLockUpdate',
        'refreshTables' => '$refresh'
    ];

    public function mount()
    {
        // Get the saved view type from session, default to 'list' if not set
        $this->viewType = session('table_view_type', 'list');
        $this->reservations = Reservation::where('table_id', '!=', null)->get();
        // dd($this->reservations);
        $this->reservedTables = $this->reservations->pluck('table_id', 'reservation_date_time', 'reservation_status');
        // dd($this->reservedTables);

        $this->refreshDataWithCleanup();
    }

    public function updatedViewType($value)
    {
        $this->refreshDataWithCleanup();

        // Save the view type preference to session whenever it changes
        session(['table_view_type' => $value]);
    }

    #[On('refreshTables')]
    public function refreshTables()
    {
        $this->render();
    }

    #[On('hideAddTable')]
    public function hideAddTable()
    {
        $this->showAddTableModal = false;
    }

    #[On('hideEditTable')]
    public function hideEditTable()
    {
        $this->showEditTableModal = false;
    }

    public function showEditTable($id)
    {
        $this->activeTable = Table::findOrFail($id);
        $this->showEditTableModal = true;
    }

    public function showTableOrder($id)
    {
        // Check if table is locked before allowing access
        $table = Table::find($id);

        if ($table && !$table->canBeAccessedByUser(user()->id)) {
            $session = $table->tableSession;
            $lockedByUser = $session?->lockedByUser;
            $lockedUserName = $lockedByUser?->name ?? 'Admin';

            $this->alert('error', __('messages.tableLockedByUser', ['user' => $lockedUserName]), [
                'toast' => true,
                'position' => 'top-end'
            ]);
            return;
        }

        return $this->redirect(route('pos.show', $id), navigate: true);
    }

    public function showTableOrderDetail($id)
    {
        return $this->redirect(route('pos.order', [$id]), navigate: true);
    }

    public function forceUnlockTable($tableId)
    {
        $table = Table::find($tableId);

        if (!$table) {
            $this->alert('error', __('messages.tableNotFound'), [
                'toast' => true,
                'position' => 'top-end'
            ]);
            return;
        }

        // Check permissions in one condition
        $hasPermission = user()->hasRole('Admin_' . user()->restaurant_id) ||
                        ($table->tableSession && $table->tableSession->locked_by_user_id == user()->id);

        if (!$hasPermission) {
            $this->alert('error', __('messages.tableUnlockFailed'), [
                'toast' => true,
                'position' => 'top-end'
            ]);
            return;
        }

        // Force unlock and handle result
        $result = $table->unlock(null, true);

        $this->alert(
            $result['success'] ? 'success' : 'error',
            $result['success']
                ? __('messages.tableUnlockedSuccess', ['table' => $table->table_code])
                : __('messages.tableUnlockFailed'),
            ['toast' => true, 'position' => 'top-end']
        );

        $this->dispatch('refreshTables');
    }

    public function refreshDataWithCleanup()
    {
        try {
            // First, clean up expired locks and get the result
            \App\Models\Table::cleanupExpiredLocks();
            // Then refresh the data
            $this->refreshData();

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SetTable: Error in refreshDataWithCleanup', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function render()
    {
        $query = Area::with(['tables' => function ($query) {
            if (!is_null($this->filterAvailable)) {
                return $query->where('available_status', $this->filterAvailable);
            }
        }, 'tables.activeOrder', 'tables.tableSession.lockedByUser']);

        if (!is_null($this->areaID)) {
            $query = $query->where('id', $this->areaID);
        }

        $query = $query->get();

        // Get all table IDs to check for reservations
        $tableIds = $query->flatMap(function($area) {
            return $area->tables->pluck('id');
        });

        // Get reservations for these tables
        $tableReservations = $this->reservations->whereIn('table_id', $tableIds)
            ->keyBy('table_id')
            ->map(function($reservation) {
                // Get the time slot difference for this reservation's slot type
                $timeSlotDifference = \App\Models\ReservationSetting::where('slot_type', $reservation->reservation_slot_type)->first();

                return [
                    'date' => $reservation->reservation_date_time->format('M d, Y'),
                    'time' => $reservation->reservation_date_time->format('h:i A'),
                    'datetime' => $reservation->reservation_date_time->format('M d, Y h:i A'),
                    'status' => $reservation->reservation_status,
                    'reservation_slot_type' => $reservation->reservation_slot_type,
                    'timeSlotDifference' => $timeSlotDifference ? $timeSlotDifference->time_slot_difference : null
                ];
            });



        return view('livewire.table.tables', [
            'tables' => $query,
            'areas' => Area::get(),
            'tableReservations' => $tableReservations
        ]);
    }

}
