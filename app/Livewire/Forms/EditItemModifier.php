<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\MenuItem;
use App\Models\ItemModifier;
use App\Models\ModifierGroup;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditItemModifier extends Component
{
    use LivewireAlert;

    public $menuItems;
    public $modifierGroups;
    public $menuItemId;
    public $modifierGroupId;
    public $itemModifierId;
    public $variationId = null;
    public $variations = [];
    public $isRequired = false;
    public $allowMultipleSelection = false;

    public function mount()
    {
        $this->menuItems = MenuItem::select('id', 'item_name')->get();
        $this->modifierGroups = ModifierGroup::select('id', 'name')->get();
        $this->loadItemModifier();
    }

    public function loadItemModifier()
    {
        $itemModifier = ItemModifier::findOrFail($this->itemModifierId);
        $this->menuItemId = $itemModifier->menu_item_id;
        $this->modifierGroupId = $itemModifier->modifier_group_id;
        $this->variationId = $itemModifier->menu_item_variation_id;
        $this->isRequired = (bool) $itemModifier->is_required;
        $this->allowMultipleSelection = (bool) $itemModifier->allow_multiple_selection;

        if ($this->menuItemId) {
            $this->variations = MenuItem::find($this->menuItemId)?->variations()->get() ?? [];
        }
        
        // We only need to dispatch refreshDropdowns now since we're using $watch in Alpine
        $this->dispatch('refreshDropdowns');
    }

    public function updatedMenuItemId($value)
    {
        $this->variations = $value
            ? MenuItem::find($value)?->variations()->get() ?? []
            : [];
        $this->variationId = null;
    }

    public function submitForm()
    {
        $this->validate([
            'menuItemId' => 'required',
            'modifierGroupId' => 'required|exists:modifier_groups,id',
        ]);

        // Check for existing modifier based on whether this is for a variation or a menu item
        $query = ItemModifier::where('modifier_group_id', $this->modifierGroupId)
            ->where('menu_item_id', $this->menuItemId)
            ->where('id', '!=', $this->itemModifierId);

        if ($this->variationId) {
            $query->where('menu_item_variation_id', $this->variationId);
            $errorMessage = __('messages.modifierGroupAlreadyAssociatedWithVariation');
        } else {
            $query->whereNull('menu_item_variation_id');
            $errorMessage = __('messages.modifierGroupAlreadyAssociatedWithMenuItem');
        }

        if ($query->exists()) {
            $this->addError('modifierGroupId', $errorMessage);
            return;
        }

        $itemModifier = ItemModifier::findOrFail($this->itemModifierId);
        $itemModifier->update([
            'menu_item_id' => $this->menuItemId,
            'menu_item_variation_id' => $this->variationId,
            'modifier_group_id' => $this->modifierGroupId,
            'is_required' => $this->isRequired,
            'allow_multiple_selection' => $this->allowMultipleSelection,
        ]);

        $this->dispatch('hideEditItemModifierModal');

        $this->alert('success', __('messages.itemModifierGroupUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.forms.edit-item-modifier');
    }
}
