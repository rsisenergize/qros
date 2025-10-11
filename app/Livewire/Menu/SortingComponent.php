<?php

namespace App\Livewire\Menu;

use App\Models\Menu;
use Livewire\Component;
use App\Models\MenuItem;
use App\Models\ItemCategory;

class SortingComponent extends Component
{
    public $menus;
    public $items;
    public $categories;
    public $selectedMenu = null;
    public $selectedCategory = null;
    public $search = '';

    public function mount()
    {
        $this->loadData();
    }

    protected function loadData()
    {
        $this->loadMenus();

        // Select first menu if none selected
        if (!$this->selectedMenu && $this->menus->isNotEmpty()) {
            $this->selectedMenu = $this->menus->first()->id;
        }

        $this->loadCategories();
        $this->filterItems();
    }

    protected function loadMenus(): void
    {
        $this->menus = Menu::where('menu_name', 'like', '%' . $this->search . '%')
            ->orderBy('sort_order')
            ->get();
    }

    protected function loadCategories()
    {
        $this->categories = ItemCategory::where('category_name', 'like', '%' . $this->search . '%')
            ->orderBy('sort_order')
            ->withCount(['items' => function ($query) {
                if ($this->selectedMenu) {
                    $query->where('menu_id', $this->selectedMenu);
                }
            }])
            ->get();

        // Select first category if none selected
        if (!$this->selectedCategory && $this->categories->isNotEmpty()) {
            $this->selectedCategory = $this->categories->first()->id;
        }
    }

    public function updatedSearch()
    {
        $this->loadData();
    }


    public function filterItems()
    {
        $query = MenuItem::with(['menu', 'category'])
            ->when($this->selectedMenu, fn($q) => $q->where('menu_id', $this->selectedMenu))
            ->when($this->selectedCategory, fn($q) => $q->where('item_category_id', $this->selectedCategory));

        if (!$this->selectedMenu && !$this->selectedCategory) {
            $query->orderBy('menu_id')->orderBy('item_category_id');
        }

        $this->items = $query->orderBy('sort_order')->get();
    }

    // Combined update handlers
    public function updated($field)
    {
        if (in_array($field, ['selectedMenu', 'selectedCategory', 'search'])) {
            if ($field !== 'search') {
                $this->loadCategories();
            }
            $this->filterItems();
        }
    }

    // Simplified sort handlers
    public function sortMenus($sortedIds)
    {
        $this->batchUpdateSortOrder(Menu::class, $sortedIds);
        $this->loadMenus();
        $this->loadCategories();
    }

    public function sortCategories($sortedIds)
    {
        $this->batchUpdateSortOrder(ItemCategory::class, $sortedIds);
        $this->loadCategories();
    }

    public function sortItems($sortedIds)
    {
        foreach ($sortedIds as $sortedItem) {
            MenuItem::where('id', $sortedItem['value'])->update(['sort_order' => $sortedItem['order']]);
        }

        $this->loadCategories();
        $this->filterItems();
    }

    protected function batchUpdateSortOrder(string $model, array $sortedIds): void
    {
        foreach ($sortedIds as $sortedItem) {
            $model::where('id', $sortedItem['value'])
                ->update(['sort_order' => $sortedItem['order']]);
        }
    }


    public function render()
    {
        return view('livewire.menu.sorting-component');
    }
}
