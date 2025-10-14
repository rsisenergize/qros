<?php

namespace Modules\Inventory\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Collection;

class SearchableSelect extends Component
{
    public $search = '';
    public $showDropdown = false;
    public $selectedItem = null;
    public $name;
    public $placeholder;
    public $items;
    public $displayField = 'name';
    public $subField = null;
    public $modelId = null;
    public $dispatchEvent = 'item-selected';

    protected $listeners = ['clearSelection'];

    public function mount($name, $placeholder, $items, $modelId = null, $displayField = 'name', $subField = null, $dispatchEvent = null)
    {
        $this->name = $name;
        $this->placeholder = $placeholder;
        $this->items = $items;
        $this->modelId = $modelId;
        $this->displayField = $displayField;
        $this->subField = $subField;
        if ($dispatchEvent) {
            $this->dispatchEvent = $dispatchEvent;
        }
        
        if ($modelId) {
            $selectedItem = $this->items->firstWhere('id', $modelId);
            if ($selectedItem) {
                $this->selectedItem = $selectedItem;
                $this->search = data_get($selectedItem, $this->displayField);
            }
        }
    }

    public function updatedSearch()
    {
        $this->showDropdown = strlen($this->search) > 0;
        $this->dispatch('search-updated', search: $this->search);
    }

    public function selectItem($itemId)
    {
        $this->selectedItem = $this->items->firstWhere('id', $itemId);
        $this->search = data_get($this->selectedItem, $this->displayField);
        $this->modelId = $itemId;
        $this->showDropdown = false;
        $this->dispatch($this->dispatchEvent, $itemId);
    }

    public function clearSelection()
    {
        $this->selectedItem = null;
        $this->search = '';
        $this->modelId = null;
        $this->showDropdown = false;
        $this->dispatch('selection-cleared');
    }

    public function render()
    {
        $searchResults = collect([]);
        
        if (strlen($this->search) > 0) {
            // Filter items based on search
            $searchResults = $this->items->filter(function ($item) {
                $mainField = strtolower(data_get($item, $this->displayField, ''));
                $subField = strtolower(data_get($item, $this->subField, ''));
                $search = strtolower($this->search);
                
                return str_contains($mainField, $search) || 
                       ($subField && str_contains($subField, $search));
            })->take(5);
        } else {
            // Show first 5 items when no search term
            $searchResults = $this->items->take(5);
        }

        return view('inventory::livewire.components.searchable-select', [
            'searchResults' => $searchResults
        ]);
    }
} 