<?php

namespace Modules\Inventory\Livewire\Units;

use Livewire\Component;
use Livewire\Attributes\On;

class UnitList extends Component
{
    public $search;
    public $showAddUnit = false;

    #[On('hideAddUnit')]
    public function hideAddUnit()
    {
        $this->showAddUnit = false;
    }

    public function render()
    {
        return view('inventory::livewire.units.unit-list');
    }
}
