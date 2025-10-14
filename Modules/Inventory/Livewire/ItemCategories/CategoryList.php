<?php

namespace Modules\Inventory\Livewire\ItemCategories;

use Livewire\Component;
use Livewire\Attributes\On;

class CategoryList extends Component
{
    public $showAddItemCategory = false;
    public $search = '';

    #[On('hideAddItemCategory')]
    public function hideAddItemCategory()
    {
        $this->showAddItemCategory = false;
    }

    public function render()
    {
        return view('inventory::livewire.item-categories.category-list');
    }
}
