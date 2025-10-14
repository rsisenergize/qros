<?php

namespace Modules\Inventory\Livewire\Units;

use Livewire\Component;
use Modules\Inventory\Entities\Unit;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditUnit extends Component
{
    use LivewireAlert;

    public $unit;
    public $unitName;
    public $unitSymbol;

    public function mount()
    {
        $this->unitName = $this->unit->name;
        $this->unitSymbol = $this->unit->symbol;
    }

    public function submitForm()
    {
        $this->validate([
            'unitName' => 'required|string|max:255',
            'unitSymbol' => 'required|string|max:255',
        ]);
       
        $this->unit->update([
            'name' => $this->unitName,
            'symbol' => $this->unitSymbol,
        ]);

        $this->alert('success', __('inventory::modules.unit.unitUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        $this->showEditUnitModal = false;
    }

    public function render()
    {
        return view('inventory::livewire.units.edit-unit');
    }
}
