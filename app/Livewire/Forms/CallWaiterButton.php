<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\WaiterRequest;
use App\Models\Table;
use App\Events\ActiveWaiterRequestCreatedEvent;

class CallWaiterButton extends Component
{

    public $showConfirmation = false;
    public $notificationSent = false;
    public $tableNumber;
    public $tables;
    public $shopBranch;
    public $showTableSelection = false;
    public $table;
    public $initialTableNumber; // Store the original table number from QR code

    public function mount()
    {
        $this->initialTableNumber = $this->tableNumber; // Store initial value
        $this->tableNumber = $this->tableNumber;
        if ($this->tableNumber) {
            $this->table = Table::where('id', $this->tableNumber)->first();
        }
        $this->tables = Table::where('branch_id', $this->shopBranch->id)->get();
    }

    public function callWaiter()
    {
        if (!$this->tableNumber) {
            $this->showTableSelection = true;
        } else {
            $this->showConfirmation = true;
        }
    }

    public function selectTable($tableId)
    {
        $this->tableNumber = $tableId;
        $this->table = Table::where('id', $tableId)->first();

        $this->showTableSelection = false;
        $this->showConfirmation = true;
    }

    public function confirmCall()
    {
        // Save request to database
        WaiterRequest::create([
            'table_id' => $this->tableNumber,
            'branch_id' => $this->shopBranch->id,
            'status' => 'Pending',
        ]);

        $this->showConfirmation = false;
        $this->notificationSent = true;

        $count = WaiterRequest::where('status', 'pending')->where('branch_id', $this->shopBranch->id)->distinct('table_id')->count();

        event(new ActiveWaiterRequestCreatedEvent($count));

        $this->dispatch('newWaiterRequest');
        $this->dispatch('waiterRequestCreated', ['count' => $count, 'table_id' => $this->tableNumber]);

        // If table was initially provided (from QR code), keep it. Otherwise, reset it.
        if ($this->initialTableNumber) {
            $this->tableNumber = $this->initialTableNumber;
            $this->table = Table::where('id', $this->initialTableNumber)->first();
        } else {
            $this->tableNumber = null;
            $this->table = null;
        }
    }

    public function cancelCall()
    {
        // If table was initially provided (from QR code), restore it. Otherwise, reset it.
        if ($this->initialTableNumber) {
            $this->tableNumber = $this->initialTableNumber;
            $this->table = Table::where('id', $this->initialTableNumber)->first();
        } else {
            $this->tableNumber = null;
            $this->table = null;
        }
        $this->showConfirmation = false;
        $this->showTableSelection = false;
    }

    public function render()
    {
        return view('livewire.forms.call-waiter-button');
    }
}
