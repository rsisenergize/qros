<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\MenuItem;
use App\Models\ItemModifier;
use App\Models\ModifierGroup;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddItemModifier extends Component
{
    use LivewireAlert;

    public $menuItemId;
    public $modifierGroupId;
    public $variationId = null;
    public $isRequired = false;
    public $allowMultipleSelection = false;
    public $showAddModifierGroupModal = false;
    public $variations = [];

    public function submitForm()
    {
        $this->validate([
            'menuItemId' => 'required',
            'modifierGroupId' => 'required|exists:modifier_groups,id',
        ]);

        // Check for existing modifier based on whether this is for a variation or a menu item
        $query = ItemModifier::where('modifier_group_id', $this->modifierGroupId)
            ->where('menu_item_id', $this->menuItemId);

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

        ItemModifier::create([
            'menu_item_id' => $this->menuItemId,
            'menu_item_variation_id' => $this->variationId,
            'modifier_group_id' => $this->modifierGroupId,
            'is_required' => $this->isRequired,
            'allow_multiple_selection' => $this->allowMultipleSelection,
        ]);


        $this->dispatch('hideAddItemModifierModal');
        $this->resetForm();

        $this->alert('success', __('messages.itemModifierGroupAdded'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function resetForm()
    {
        $this->reset([
            'menuItemId',
            'modifierGroupId',
            'variationId',
            'variations',
            'isRequired',
            'allowMultipleSelection',
            'showAddModifierGroupModal',
        ]);
    }

    public function updatedMenuItemId($value)
    {
        $this->variations = $value
            ? MenuItem::find($value)?->variations()->get() ?? []
            : [];
        $this->variationId = null;
    }

     // #[On('hideAddModifierGroupModal')]
    // public function hideAddModifierGroupModal()
    // {
    //     $this->resetForm();
    // }

    public function render()
    {
        return view('livewire.forms.add-item-modifier', [
            'menuItems' => MenuItem::select('id', 'item_name')->get(),
            'modifierGroups' => ModifierGroup::select('id', 'name')->get(),
        ]);
    }
}
