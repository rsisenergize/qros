<?php

namespace Modules\Kitchen\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class KitchenPlaces extends Component
{
    public $search;
    public $showAddkitchenPlaces = false;
    public $showFilterButton = true;
    public $closeModal = false;
    public $selectedKitchen;

    #[On('hideAddKitchen')]
    public function hideAddKitchen()
    {
        $this->showAddkitchenPlaces = false;
    }

    #[On('clearExpenseFilter')]
    public function clearExpenseFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    public function render()
    {
        return view('kitchen::livewire.kitchen-places');
    }
}
