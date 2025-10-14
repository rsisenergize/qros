<?php

namespace Modules\Inventory\Livewire\InventoryItem;

use Livewire\Component;
use Livewire\Attributes\On;

class InventoryItemList extends Component
{
    public $search = '';
    public $showAddInventoryItem = false;
    public $showEditInventoryItemModal = false;

    #[On('hideAddInventoryItem')]
    public function hideAddInventoryItem()
    {
        $this->showAddInventoryItem = false;
    }

    #[On('hideEditInventoryItemModal')]
    public function hideEditInventoryItemModal()
    {
        $this->showEditInventoryItemModal = false;
    }

    public function render()
    {
        return view('inventory::livewire.inventory-item.inventory-item-list');
    }
}
