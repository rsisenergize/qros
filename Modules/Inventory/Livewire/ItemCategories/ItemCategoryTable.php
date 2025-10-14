<?php

namespace Modules\Inventory\Livewire\ItemCategories;

use Livewire\Component;
use Modules\Inventory\Entities\InventoryItemCategory;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ItemCategoryTable extends Component
{
    use WithPagination;
    use LivewireAlert;

    public $search;
    public $itemCategory;
    public $showEditItemCategoryModal = false;
    public $showDeleteItemCategoryModal = false;

    public function showEditItemCategory($id)
    {
        $this->itemCategory = InventoryItemCategory::find($id);
        $this->showEditItemCategoryModal = true;
    }

    public function showDeleteItemCategory($id)
    {
        $this->itemCategory = InventoryItemCategory::find($id);
        $this->showDeleteItemCategoryModal = true;
    }

    public function deleteItemCategory($id)
    {
        $itemCategory = InventoryItemCategory::find($id);
        $itemCategory->delete();
        $this->itemCategory = null;
        $this->alert('success', __('inventory::modules.itemCategory.itemCategoryDeleted'));
        $this->showDeleteItemCategoryModal = false;
    }

    public function render()
    {

        $itemCategories = InventoryItemCategory::where('name', 'like', '%' . $this->search . '%')->paginate(10);

        return view('inventory::livewire.item-categories.item-category-table', compact('itemCategories'));
    }
}
