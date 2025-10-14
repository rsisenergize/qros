<?php

namespace Modules\Inventory\Livewire\Units;

use Livewire\Component;
use Modules\Inventory\Entities\Unit;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class UnitTable extends Component
{
    use WithPagination, WithoutUrlPagination;
    use LivewireAlert;

    public $search;
    public $showEditUnitModal = false;
    public $showDeleteUnitModal = false;
    public $unit;

    public function showEditUnit($id)
    {
        $this->unit = Unit::find($id);
        $this->showEditUnitModal = true;
    }

    public function showDeleteUnit($id)
    {
        $this->unit = Unit::find($id);
        $this->showDeleteUnitModal = true;
    }

    #[On('hideEditUnitModal')]
    public function hideEditUnitModal()
    {
        $this->showEditUnitModal = false;
    }

    public function deleteUnit($id)
    {
        $unit = Unit::destroy($id);
        $this->showDeleteUnitModal = false;

        $this->alert('success', __('inventory::modules.unit.unitDeleted'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        $query = Unit::where('name', 'like', '%'.$this->search.'%')
            ->orWhere('symbol', 'like', '%'.$this->search.'%')
            ->paginate(10);

        return view('inventory::livewire.units.unit-table', [
            'units' => $query
        ]);
    }
}
