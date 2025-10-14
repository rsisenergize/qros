<?php

namespace Modules\Inventory\Livewire\ItemCategories;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditItemCategory extends Component
{
    use LivewireAlert;
    public $itemCategory;
    public $itemCategoryName;

    public function mount($itemCategory)
    {
        $this->itemCategory = $itemCategory;
        $this->itemCategoryName = $itemCategory->name;
    }

    public function submitForm()
    {
        $this->validate(
            [
                'itemCategoryName' => 'required|string|max:255|unique:inventory_item_categories,name,' . $this->itemCategory->id . ',id,branch_id,' . branch()->id,
            ]
        );
        $this->itemCategory->update([
            'name' => $this->itemCategoryName,
        ]);

        $this->alert('success', __('inventory::modules.itemCategory.itemCategoryUpdated'));
        $this->dispatch('hideEditItemCategory');
    }

    public function render()
    {
        return view('inventory::livewire.item-categories.edit-item-category');
    }
}
