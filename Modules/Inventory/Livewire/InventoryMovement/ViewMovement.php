<?php

namespace Modules\Inventory\Livewire\InventoryMovement;

use Livewire\Component;
use Modules\Inventory\Entities\InventoryMovement;

class ViewMovement extends Component
{
    public $movement;
    public $showEditModal = false;
    public $selectedMovement = null;

    protected $listeners = [
        'showEditMovementModal' => 'showEditModal',
        'movementUpdated' => 'handleMovementUpdated'
    ];

    public function mount(InventoryMovement $movement)
    {
        $this->movement = $movement;
    }

    public function handleMovementUpdated()
    {
        $this->showEditModal = false;
        $this->movement->refresh();
    }

    public function render()
    {
        return view('inventory::livewire.inventory-movement.view-movement');
    }
} 