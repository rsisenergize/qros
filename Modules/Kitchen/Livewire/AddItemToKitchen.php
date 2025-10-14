<?php

namespace Modules\Kitchen\Livewire;

use Livewire\Component;
use App\Models\MenuItem;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\MultipleKot;
use Livewire\Attributes\On;

class AddItemToKitchen extends Component
{
    use LivewireAlert;
    public $search = '';
    public $selectedItems = [];
    public $kitchenId;
    public $kitchen;

    public function mount()
    {
        $kitchenId = $this->kitchenId;
    }

    #[On('preSelectItem')]
    public function preSelectItem($itemId)
    {
        if (!in_array($itemId, $this->selectedItems)) {
            $this->selectedItems[] = $itemId;
        }
    }

    public function addItems()
    {
        $this->validate([
            'selectedItems' => 'required|array|min:1',
        ]);

        MenuItem::whereIn('id', $this->selectedItems)
            ->update(['kot_place_id' => $this->kitchenId]);

        $this->reset('selectedItems', 'search');
        $this->dispatch('hideItemToKitchen');
        $this->alert('success', __('kitchen::messages.ItemAddedToKitchen'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close'),
        ]);
    }

    public function toggleItemStatus($itemId)
    {
        $item = MenuItem::findOrFail($itemId);

        $item->kot_place_id = null;
        $item->save();

        $this->dispatch('$refresh');

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('kitchen::livewire.add-item-to-kitchen', [
            'items' => $this->items, // use the computed property
            'fetchedItems' => MenuItem::where('kot_place_id', $this->kitchenId)->get(),
        ]);
    }

    public function getItemsProperty()
    {
        return MenuItem::whereNull('kot_place_id')
            ->where('item_name', 'like', '%' . $this->search . '%')
            ->get();
    }
}
