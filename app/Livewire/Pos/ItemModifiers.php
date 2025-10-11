<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use App\Models\MenuItem;
use App\Models\MenuItemVariation;

class ItemModifiers extends Component
{
    public $selectedModifierItem;
    public $menuItemId;
    public $selectedModifiers = [];
    public $finalModifiers = [];
    public $modifiers = [];
    public $requiredModifiers = [];
    public $selectedVariationName;

    public function mount()
    {
        $variationId = null;

        if (strpos($this->menuItemId, '_') !== false) {
            [$itemId, $variationId] = explode('_', $this->menuItemId);
            $this->menuItemId = $itemId;
            $menuItemVariation = MenuItemVariation::find($variationId);
            $this->selectedVariationName = $menuItemVariation->variation ?? null;
        }

        $this->selectedModifierItem = MenuItem::with(['modifierGroups', 'modifierGroups.options'])
            ->findOrFail($this->menuItemId);

        // New logic for modifiers
        // Get all modifiers that apply to this item (base modifiers where variation_id is null)
        $baseModifiers = \App\Models\ModifierGroup::whereHas('itemModifiers', function($query) {
            $query->where('menu_item_id', $this->menuItemId)
                ->whereNull('menu_item_variation_id');
        })->with(['options', 'itemModifiers' => function($query) {
            $query->where('menu_item_id', $this->menuItemId)
                ->whereNull('menu_item_variation_id');
        }])->get();

        $this->modifiers = $baseModifiers;

        // If we have a variation, add variation-specific modifiers
        if ($variationId) {
            $variationSpecificModifiers = \App\Models\ModifierGroup::whereHas('itemModifiers', function($query) use ($variationId) {
                $query->where('menu_item_id', $this->menuItemId)
                    ->where('menu_item_variation_id', $variationId);
            })->with(['options', 'itemModifiers' => function($query) use ($variationId) {
                $query->where('menu_item_id', $this->menuItemId)
                    ->where('menu_item_variation_id', $variationId);
            }])->get();

            foreach ($variationSpecificModifiers as $modifier) {
                // Mark this modifier as variation-specific
                $modifier->variationSpecific = true;
                $modifier->menu_item_variation_id = $variationId;
            }

            // Merge variation-specific modifiers with base modifiers
            $this->modifiers = collect($baseModifiers)->concat($variationSpecificModifiers);
        }

    }

    public function toggleSelection($groupId, $optionId)
    {
        $modifierGroup = $this->selectedModifierItem->modifierGroups()
            ->withPivot(['is_required', 'allow_multiple_selection'])
            ->firstWhere('modifier_groups.id', $groupId);

        $allowMultiple = $modifierGroup->pivot->allow_multiple_selection;

        if ($allowMultiple) {
            if (in_array($optionId, $this->selectedModifiers)) {
                if ($optionId !== 1) {
                    $this->selectedModifiers = array_diff($this->selectedModifiers, [$optionId]);
                }
            }
        } else {
            if (isset($this->selectedModifiers[$optionId]) && $this->selectedModifiers[$optionId]) {
                foreach ($modifierGroup->options as $option) {
                    if ($option->id != $optionId) {
                        unset($this->selectedModifiers[$option->id]);
                    }
                }
            }
        }
    }

    public function saveModifiers()
    {
        $this->validateRequiredModifiers();
        $this->finalModifiers = [
            $this->menuItemId => array_keys(array_filter($this->selectedModifiers))
        ];

        $this->dispatch('setPosModifier', $this->finalModifiers);
    }

    public function validateRequiredModifiers()
    {
        $rules = [];
        $messages = [];

        // Use the already loaded modifiers instead of querying the database again
        foreach ($this->modifiers as $modifierGroup) {

            $isRequired = $modifierGroup->itemModifiers->isNotEmpty()
                ? ($modifierGroup->itemModifiers->first()->is_required ?? false)
                : false;

            if ($isRequired) {
                $selectedOptions = array_keys(array_filter($this->selectedModifiers, function ($selected, $optionId) use ($modifierGroup) {
                    return $selected && $modifierGroup->options->contains('id', $optionId);
                }, ARRAY_FILTER_USE_BOTH));

                if (empty($selectedOptions)) {
                    $rules["requiredModifiers.{$modifierGroup->id}"] = 'required';
                    $messages["requiredModifiers.{$modifierGroup->id}.required"] = __('validation.requiredModifierGroup', ['name' => $modifierGroup->name]);
                }
            }
        }

        if (!empty($rules)) {
            $this->validate($rules, $messages);
        }
    }

    public function render()
    {
        return view('livewire.pos.item-modifiers');
    }
}
