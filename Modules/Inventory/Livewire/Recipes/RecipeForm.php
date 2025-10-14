<?php

namespace Modules\Inventory\Livewire\Recipes;

use Livewire\Component;
use Modules\Inventory\Entities\Recipe;
use Modules\Inventory\Entities\InventoryItem;
use Modules\Inventory\Entities\Unit;
use App\Models\MenuItem;
use App\Models\MenuItemVariation;
use App\Models\ModifierOption;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class RecipeForm extends Component
{
    use LivewireAlert;
    public $showModal = false;
    public $isEditing = false;
    public $recipeId;
    public $menuItemId;
    public $ingredients = [];

    // Form properties
    public $selectedMenuItem;
    public $availableMenuItems;
    public $availableInventoryItems;
    public $availableUnits;
    
    // Variation properties
    public $selectedVariationId;
    public $menuItemVariations = [];
    public $hasVariations = false;
    
    // Modifier option properties
    public $selectedModifierOptionId;
    public $availableModifierOptions;
    public $hasModifierOptions = false;
    public $recipeType = 'menu_item'; // 'menu_item' or 'modifier_option'

    protected $listeners = ['showRecipeForm', 'editRecipe'];

    protected function rules()
    {
        $rules = [
            'recipeType' => 'required|in:menu_item,modifier_option',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'ingredients.*.quantity' => 'required|numeric|min:0',
            'ingredients.*.unit_id' => 'required|exists:units,id',
        ];

        // Add conditional validation based on recipe type
        if ($this->recipeType === 'menu_item') {
            $rules['menuItemId'] = 'required|exists:menu_items,id';
            $rules['selectedVariationId'] = 'nullable|exists:menu_item_variations,id';
        } elseif ($this->recipeType === 'modifier_option') {
            $rules['selectedModifierOptionId'] = 'required|exists:modifier_options,id';
        }

        return $rules;
    }

    private function validateWithCustomRules()
    {
        $rules = [
            'recipeType' => 'required|in:menu_item,modifier_option',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'ingredients.*.quantity' => 'required|numeric|min:0',
            'ingredients.*.unit_id' => 'required|exists:units,id',
        ];

        // Add conditional validation based on recipe type
        if ($this->recipeType === 'menu_item') {
            $rules['menuItemId'] = 'required|exists:menu_items,id';
            if ($this->hasVariations) {
                $rules['selectedVariationId'] = 'nullable|exists:menu_item_variations,id';
            }
        } elseif ($this->recipeType === 'modifier_option') {
            $rules['selectedModifierOptionId'] = 'required|exists:modifier_options,id';
        }

        $this->validate($rules);
    }

    public function mount()
    {
        // Get menu items that don't have recipes or have variations without recipes
        $this->availableMenuItems = MenuItem::where('is_available', 1)
            ->where('branch_id', branch()->id)
            ->where(function ($query) {
                // Menu items without any recipes
                $query->whereNotIn('id', function ($subquery) {
                    $subquery->select('menu_item_id')
                        ->from('recipes')
                        ->distinct();
                })
                // OR menu items with variations that don't have recipes for all variations
                ->orWhereHas('variations', function ($variationQuery) {
                    $variationQuery->whereNotIn('id', function ($recipeQuery) {
                        $recipeQuery->select('menu_item_variation_id')
                            ->from('recipes')
                            ->whereNotNull('menu_item_variation_id');
                    });
                });
            })
            ->get(['id', 'item_name']);

        // Get modifier options that don't have recipes and belong to current branch
        $this->availableModifierOptions = ModifierOption::where('is_available', 1)
            ->whereNotIn('id', function ($query) {
                $query->select('modifier_option_id')
                    ->from('recipes')
                    ->whereNotNull('modifier_option_id');
            })
            ->whereHas('modifierGroup', function ($query) {
                $query->where('branch_id', branch()->id);
            })
            ->with('modifierGroup')
            ->get(['id', 'name', 'modifier_group_id']);

        // Load inventory items with their units
        $this->availableInventoryItems = InventoryItem::with('unit')
            ->get(['id', 'name', 'unit_id']);

        $this->ingredients = [
            $this->getEmptyIngredient()
        ];
    }

    public function showRecipeForm()
    {
        // Refresh available menu items when showing form
        $this->availableMenuItems = MenuItem::where('is_available', 1)
            ->where('branch_id', branch()->id)
            ->where(function ($query) {
                // Menu items without any recipes
                $query->whereNotIn('id', function ($subquery) {
                    $subquery->select('menu_item_id')
                        ->from('recipes')
                        ->distinct();
                })
                // OR menu items with variations that don't have recipes for all variations
                ->orWhereHas('variations', function ($variationQuery) {
                    $variationQuery->whereNotIn('id', function ($recipeQuery) {
                        $recipeQuery->select('menu_item_variation_id')
                            ->from('recipes')
                            ->whereNotNull('menu_item_variation_id');
                    });
                });
            })
            ->get(['id', 'item_name']);

        // Refresh available modifier options
        $this->availableModifierOptions = ModifierOption::where('is_available', 1)
            ->whereNotIn('id', function ($query) {
                $query->select('modifier_option_id')
                    ->from('recipes')
                    ->whereNotNull('modifier_option_id');
            })
            ->whereHas('modifierGroup', function ($query) {
                $query->where('branch_id', branch()->id);
            })
            ->with('modifierGroup')
            ->get(['id', 'name', 'modifier_group_id']);

        $this->reset(['menuItemId', 'selectedVariationId', 'selectedModifierOptionId', 'ingredients', 'isEditing', 'recipeId', 'menuItemVariations', 'hasVariations']);
        $this->recipeType = 'menu_item'; // Set default recipe type
        $this->ingredients = [
            $this->getEmptyIngredient()
        ];
        $this->showModal = true;
    }

    public function editRecipe($recipeId, $variationId = null, $modifierOptionId = null)
    {
        $this->isEditing = true;
        $this->recipeId = $recipeId;

        if ($modifierOptionId) {
            // Editing a modifier option recipe
            $this->recipeType = 'modifier_option';
            $this->selectedModifierOptionId = $modifierOptionId;
            
            // Get the modifier option details
            $modifierOption = ModifierOption::with('modifierGroup')
                ->where('id', $modifierOptionId)
                ->where('is_available', 1)
                ->first();

            if (!$modifierOption) {
                return;
            }

            // Set available modifier options to include only this one
            $this->availableModifierOptions = collect([
                (object)[
                    'id' => $modifierOption->id,
                    'name' => $modifierOption->name,
                    'modifier_group_id' => $modifierOption->modifier_group_id
                ]
            ]);

            // Get all ingredients for this modifier option
            $this->ingredients = Recipe::where('modifier_option_id', $modifierOptionId)
                ->with(['inventoryItem', 'unit'])
                ->get()
                ->map(function ($recipe) {
                    return [
                        'inventory_item_id' => $recipe->inventory_item_id,
                        'quantity' => $recipe->quantity,
                        'unit_id' => $recipe->unit_id,
                    ];
                })->toArray();

        } else {
            // Editing a menu item recipe
            $this->recipeType = 'menu_item';
            
            // Get the menu item details
            $menuItem = MenuItem::with(['variations', 'category'])
                ->where('id', $recipeId)
                ->where('is_available', 1)
                ->where('branch_id', branch()->id)
                ->first();

            if (!$menuItem) {
                return;
            }

            // Set the menu item ID
            $this->menuItemId = $recipeId;
            
            // Load variations for this menu item
            $this->loadMenuItemVariations();

            // Set available menu items to include only this menu item
            $this->availableMenuItems = collect([
                (object)[
                    'id' => $menuItem->id,
                    'item_name' => $menuItem->item_name
                ]
            ]);

            // If editing a specific variation, set it
            if ($variationId) {
                $this->selectedVariationId = $variationId;
            }

            // Get all ingredients for this menu item and variation
            $query = Recipe::where('menu_item_id', $recipeId)
                ->with(['inventoryItem', 'unit']);
                
            // If there are variations and a specific variation is selected, get recipes for that variation
            if ($this->hasVariations && $this->selectedVariationId) {
                $query->where('menu_item_variation_id', $this->selectedVariationId);
            } else {
                $query->whereNull('menu_item_variation_id');
            }
            
            $this->ingredients = $query->get()
                ->map(function ($recipe) {
                    return [
                        'inventory_item_id' => $recipe->inventory_item_id,
                        'quantity' => $recipe->quantity,
                        'unit_id' => $recipe->unit_id,
                    ];
                })->toArray();
        }

        $this->showModal = true;
    }

    public function updatedMenuItemId($value)
    {
        if ($value) {
            $this->recipeType = 'menu_item';
            $this->loadMenuItemVariations();
            $this->selectedVariationId = null;
            $this->selectedModifierOptionId = null;
            $this->ingredients = [$this->getEmptyIngredient()];
        } else {
            $this->menuItemVariations = [];
            $this->hasVariations = false;
            $this->selectedVariationId = null;
        }
    }

    public function updatedSelectedVariationId($value)
    {
        if ($this->isEditing && $value) {
            // Load ingredients for the selected variation
            $this->ingredients = Recipe::where('menu_item_id', $this->menuItemId)
                ->where('menu_item_variation_id', $value)
                ->with(['inventoryItem', 'unit'])
                ->get()
                ->map(function ($recipe) {
                    return [
                        'inventory_item_id' => $recipe->inventory_item_id,
                        'quantity' => $recipe->quantity,
                        'unit_id' => $recipe->unit_id,
                    ];
                })->toArray();
                
            if (empty($this->ingredients)) {
                $this->ingredients = [$this->getEmptyIngredient()];
            }
        }
    }

    public function updatedSelectedModifierOptionId($value)
    {
        if ($value) {
            $this->recipeType = 'modifier_option';
            $this->menuItemId = null;
            $this->selectedVariationId = null;
            $this->ingredients = [$this->getEmptyIngredient()];
        }
    }

    public function updatedRecipeType($value)
    {
        // Clear validation errors when recipe type changes
        $this->resetValidation();
        
        // Reset form fields based on recipe type
        if ($value === 'menu_item') {
            $this->selectedModifierOptionId = null;
            $this->ingredients = [$this->getEmptyIngredient()];
        } elseif ($value === 'modifier_option') {
            $this->menuItemId = null;
            $this->selectedVariationId = null;
            $this->ingredients = [$this->getEmptyIngredient()];
        }
    }

    private function loadMenuItemVariations()
    {
        if ($this->menuItemId) {
            $menuItem = MenuItem::with('variations')->find($this->menuItemId);
            
            if ($menuItem->variations->count() > 0) {
                if ($this->isEditing) {
                    // When editing, show all variations so user can edit existing recipes
                    $this->menuItemVariations = $menuItem->variations;
                    $this->hasVariations = true;
                } else {
                    // When adding new, filter variations to show only those that don't have recipes yet
                    $this->menuItemVariations = $menuItem->variations->filter(function ($variation) {
                        return !Recipe::where('menu_item_id', $this->menuItemId)
                            ->where('menu_item_variation_id', $variation->id)
                            ->exists();
                    });
                    $this->hasVariations = $this->menuItemVariations->count() > 0;
                }
            } else {
                $this->menuItemVariations = collect();
                $this->hasVariations = false;
            }
        }
    }

    public function addIngredient()
    {
        $this->ingredients[] = $this->getEmptyIngredient();
    }

    public function removeIngredient($index)
    {
        unset($this->ingredients[$index]);
        $this->ingredients = array_values($this->ingredients);
    }

    public function save()
    {
        // Custom validation to ensure proper validation based on recipe type
        $this->validateWithCustomRules();

        // Delete existing recipes if editing
        if ($this->isEditing) {
            if ($this->recipeType === 'modifier_option') {
                // Delete recipes for modifier option
                Recipe::where('modifier_option_id', $this->selectedModifierOptionId)
                    ->delete();
            } else {
                if ($this->hasVariations && $this->selectedVariationId) {
                    // Delete recipes for specific variation
                    Recipe::where('menu_item_id', $this->menuItemId)
                        ->where('menu_item_variation_id', $this->selectedVariationId)
                        ->delete();
                } else {
                    // Delete recipes for menu item without variation
                    Recipe::where('menu_item_id', $this->menuItemId)
                        ->whereNull('menu_item_variation_id')
                        ->delete();
                }
            }
        }

        // Create new recipes
        foreach ($this->ingredients as $ingredient) {
            Recipe::create([
                'menu_item_id' => $this->recipeType === 'menu_item' ? $this->menuItemId : null,
                'menu_item_variation_id' => $this->recipeType === 'menu_item' && $this->hasVariations ? $this->selectedVariationId : null,
                'modifier_option_id' => $this->recipeType === 'modifier_option' ? $this->selectedModifierOptionId : null,
                'inventory_item_id' => $ingredient['inventory_item_id'],
                'quantity' => $ingredient['quantity'],
                'unit_id' => $ingredient['unit_id'],
            ]);
        }

        $this->dispatch('recipeUpdated');
        // Update parent's showAddRecipe property
        $this->dispatch('closeAddRecipeModal');

        $this->alert('success', __('inventory::modules.recipe.recipe_saved'));
    }

    private function getEmptyIngredient()
    {
        return [
            'inventory_item_id' => '',
            'quantity' => '',
            'unit_id' => '',
        ];
    }

    public function updatedIngredients($value, $key)
    {
        // Check if the updated field is inventory_item_id
        if (str_contains($key, 'inventory_item_id')) {
            $index = explode('.', $key)[0];
            $inventoryItemId = $value;

            // Find the inventory item
            $inventoryItem = $this->availableInventoryItems->find($inventoryItemId);

            if ($inventoryItem) {
                // Set the unit_id to match the inventory item's unit
                $this->ingredients[$index]['unit_id'] = $inventoryItem->unit_id;
            }
        }
    }

    public function render()
    {
        return view('inventory::livewire.recipes.recipe-form', [
            'inventoryItemsWithUnits' => $this->availableInventoryItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'unit_symbol' => $item->unit->symbol
                ];
            })
        ]);
    }
}
