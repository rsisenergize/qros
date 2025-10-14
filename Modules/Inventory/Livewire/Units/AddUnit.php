<?php

namespace Modules\Inventory\Livewire\Units;

use Livewire\Component;
use Modules\Inventory\Entities\Unit;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddUnit extends Component
{
    use LivewireAlert;

    public $unitName;
    public $unitSymbol;

    public function submitForm()
    {
        $this->validate([
            'unitName' => 'required|string|max:255',
            'unitSymbol' => 'required|string|max:255',
        ]);
       
        Unit::create([
            'name' => $this->unitName,
            'symbol' => $this->unitSymbol,
        ]);

        $this->alert('success', __('inventory::modules.unit.unitAdded'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        $this->reset('unitName', 'unitSymbol');
        $this->showAddUnit = false;
    }

    public function render()
    {
        return view('inventory::livewire.units.add-unit');
    }
}
