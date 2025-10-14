<?php

namespace Modules\Inventory\Livewire\ItemCategories;

use Livewire\Component;
use Modules\Inventory\Entities\InventoryItemCategory;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddItemCategory extends Component
{
    use LivewireAlert;
    public $itemCategoryName;

    public function submitForm()
    {
        $this->validate([
            'itemCategoryName' => 'required|string|max:255|unique:inventory_item_categories,name,null,id,branch_id,' . branch()->id,
        ]);

        $itemCategory = InventoryItemCategory::create([
            'name' => $this->itemCategoryName,
        ]);

        $this->itemCategoryName = '';
        $this->alert('success', __('inventory::modules.itemCategory.itemCategoryAdded'));
        $this->dispatch('hideAddItemCategory');
    }

    public function render()
    {
        return view('inventory::livewire.item-categories.add-item-category');
    }
}
