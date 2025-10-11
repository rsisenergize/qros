<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\MenuItem;
use App\Models\ItemModifier;
use App\Models\ModifierGroup;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;

class AddModifierGroup extends Component
{
    use LivewireAlert;

    // Translation related properties
    public $languages = [];
    public $translationNames = [];
    public $translationDescriptions = [];
    public $currentLanguage;
    public $globalLocale;

    // Basic form fields
    public $name;
    public $description;

    // Modifier options
    public $modifierOptions = [];
    public $modifierOptionInput = [];
    public $modifierOptionName = [];

    // Menu items selection
    public $search = '';
    public $isOpen = false;
    public $selectedMenuItems = [];
    public $selectedVariations = []; // Structure for menu item variation selections

    // Cached data
    public $allMenuItems;

    // Track which menu item variations are expanded in the UI
    public $expandedVariations = [];

    // Define validation rules using Livewire's attribute validation
    protected function rules()
    {
        $baseRules = [
            'description' => 'nullable',
            'modifierOptions.*.price' => 'required|numeric|min:0',
            'modifierOptions.*.is_available' => 'required|boolean',
            'selectedMenuItems' => 'required|array|min:1',
        ];

        // Add dynamic validation rules for the global locale
        $baseRules['translationNames.' . $this->globalLocale] = 'required|max:255';
        $baseRules['modifierOptions.*.name.' . $this->globalLocale] = 'required|max:255';

        return $baseRules;
    }

    protected function messages()
    {
        return [
            'selectedMenuItems.required' => __('Please select at least one menu item'),
            'selectedMenuItems.min' => __('Please select at least one menu item'),
            'modifierOptions.*.price.required' => __('Modifier option must have a price'),
            'modifierOptions.*.price.numeric' => __('Modifier option price must be a number'),
            'modifierOptions.*.price.min' => __('Modifier option price must be at least 0'),
            'translationNames.' . $this->globalLocale . '.required' => __('validation.modifierGroupNameRequired', ['language' => $this->languages[$this->globalLocale]]),
            'modifierOptions.*.name.' . $this->globalLocale . '.required' => __('validation.modifierOptionNameRequired', ['language' => $this->languages[$this->globalLocale]]),
        ];
    }

    public function mount()
    {
        $this->resetValidation();

        // Load languages
        $this->languages = languages()->pluck('language_name', 'language_code')->toArray();
        $this->globalLocale = global_setting()->locale;
        $this->currentLanguage = $this->globalLocale;

        // Initialize translation arrays for all languages
        $languageKeys = array_keys($this->languages);
        $this->translationNames = array_fill_keys($languageKeys, '');
        $this->translationDescriptions = array_fill_keys($languageKeys, '');

        // Add first empty modifier option
        $this->addModifierOption();

        // Eager load menu items with their variations to prevent N+1 queries
        // Cache this query to avoid repetitive database calls
        $this->allMenuItems = MenuItem::with(['variations' => function($query) {
            $query->select('id', 'menu_item_id', 'variation');
        }])->select('id', 'item_name')->get();

        // No initialization needed for applicationType as we'll use selectedVariations
    }

    /**
     * Create a new modifier option with empty values for all languages
     */
    protected function newModifierOption()
    {
        $langs = array_keys($this->languages);
        return [
            'id' => uniqid(),
            'name' => array_fill_keys($langs, ''),
            'price' => 0,
            'is_available' => true,
            'sort_order' => count($this->modifierOptions),
        ];
    }

    /**
     * Sync the UI with the current language selection
     */
    public function updatedCurrentLanguage()
    {
        // Update form fields based on the selected language
        $this->name = $this->translationNames[$this->currentLanguage] ?? '';
        $this->description = $this->translationDescriptions[$this->currentLanguage] ?? '';

        // Update modifier option names for current language
        foreach ($this->modifierOptions as $index => $option) {
            $this->modifierOptionName[$index] = $option['name'][$this->currentLanguage] ?? '';
        }
    }

    /**
     * Save translations when name or description is updated
     */
    public function updateTranslation()
    {
        $this->translationNames[$this->currentLanguage] = $this->name;
        $this->translationDescriptions[$this->currentLanguage] = $this->description;
    }

    /**
     * Update translations for a modifier option
     */
    public function updateModifierOptionTranslation($index)
    {
        $lang = $this->currentLanguage;
        $this->modifierOptions[$index]['name'][$lang] = $this->modifierOptionName[$index];

        // Keep input synced for rendering
        if (!isset($this->modifierOptionInput[$index])) {
            $this->modifierOptionInput[$index] = [];
        }
        $this->modifierOptionInput[$index][$lang] = $this->modifierOptionName[$index];
    }

    /**
     * Add a new modifier option
     */
    public function addModifierOption()
    {
        $option = $this->newModifierOption();
        $this->modifierOptions[] = $option;

        $index = count($this->modifierOptions) - 1;
        $this->modifierOptionName[$index] = '';
        $this->modifierOptionInput[$index] = array_fill_keys(array_keys($this->languages), '');
    }

    /**
     * Remove a modifier option, but ensure at least one remains
     */
    public function removeModifierOption($index)
    {
        // Only remove if we have more than one option
        if (count($this->modifierOptions) > 1) {
            // Remove the option and reindex arrays
            unset($this->modifierOptions[$index], $this->modifierOptionInput[$index], $this->modifierOptionName[$index]);
            $this->modifierOptions = array_values($this->modifierOptions);
            $this->modifierOptionInput = array_values($this->modifierOptionInput);
            $this->modifierOptionName = array_values($this->modifierOptionName);
        }
    }

    /**
     * Reset search when dropdown is closed
     */
    public function updatedIsOpen($value)
    {
        if (!$value) {
            $this->reset(['search']);
        }
    }

    /**
     * Handle mutual exclusivity between base item and specific variations
     */
    public function updatedSelectedVariations($value, $key)
    {
        // Parse the key to get menu item ID and variation ID
        if (strpos($key, '.') !== false) {
            list($menuItemId, $variationId) = explode('.', $key);
            
            // If this is the base item being selected
            if ($variationId === 'item' && $value === true) {
                // If base item is selected, unselect all specific variations
                if (isset($this->selectedVariations[$menuItemId])) {
                    foreach ($this->selectedVariations[$menuItemId] as $varId => $isSelected) {
                        if ($varId !== 'item') {
                            $this->selectedVariations[$menuItemId][$varId] = false;
                        }
                    }
                }
            }
            // If this is a specific variation being selected
            elseif ($variationId !== 'item' && $value === true) {
                // If a specific variation is selected, unselect base item
                if (isset($this->selectedVariations[$menuItemId]['item'])) {
                    $this->selectedVariations[$menuItemId]['item'] = false;
                }
            }
            // If this is a specific variation being unselected
            elseif ($variationId !== 'item' && $value === false) {
                // Check if any other variations are still selected
                $anyVariationSelected = false;
                foreach ($this->selectedVariations[$menuItemId] as $varId => $isSelected) {
                    if ($varId !== 'item' && $isSelected) {
                        $anyVariationSelected = true;
                        break;
                    }
                }

                // If no variations are selected, auto-select the base item
                if (!$anyVariationSelected) {
                    $this->selectedVariations[$menuItemId]['item'] = true;
                }
            }
        }
    }

    /**
     * Get filtered menu items based on search
     */
    public function getMenuItemsProperty()
    {
        if (empty($this->search)) {
            return $this->allMenuItems;
        }

        return $this->allMenuItems->filter(function($item) {
            return stripos($item->item_name, $this->search) !== false;
        });
    }

    /**
     * Toggle the expansion state of a menu item's variations
     */
    public function toggleVariationExpansion($menuItemId)
    {
        if (in_array($menuItemId, $this->expandedVariations)) {
            // If already expanded, collapse it
            $this->expandedVariations = array_diff($this->expandedVariations, [$menuItemId]);
        } else {
            // Otherwise expand it
            $this->expandedVariations[] = $menuItemId;
        }
    }

    /**
     * Toggle selection of a menu item and handle variations visibility
     */
    public function toggleSelectItem($item)
    {
        $itemId = $item['id'];
        $menuItem = $this->allMenuItems->firstWhere('id', $itemId);
        $hasVariations = $menuItem && $menuItem->variations->count() > 0;

        // If already selected, remove it and close variations
        if (($key = array_search($itemId, $this->selectedMenuItems)) !== false) {
            unset($this->selectedMenuItems[$key]);

            // Clean up selected variations
            if (isset($this->selectedVariations[$itemId])) {
                unset($this->selectedVariations[$itemId]);
            }

            // Remove from expanded variations if it's there
            if (in_array($itemId, $this->expandedVariations)) {
                $this->expandedVariations = array_diff($this->expandedVariations, [$itemId]);
            }
        }
        // Otherwise add it and initialize variation selections
        else {
            $this->selectedMenuItems[] = $itemId;

            // For items with variations
            if ($hasVariations) {
                // Initialize the selected variations array with base item selected by default
                $this->selectedVariations[$itemId] = ['item' => true];

                // Initialize all variations as unselected
                foreach ($menuItem->variations as $variation) {
                    $this->selectedVariations[$itemId][$variation->id] = false;
                }

                // Auto expand variations section when selected
                if (!in_array($itemId, $this->expandedVariations)) {
                    $this->expandedVariations[] = $itemId;
                }
            }
        }

        // Re-index the array
        $this->selectedMenuItems = array_values($this->selectedMenuItems);
    }

    /**
     * Submit the form to create a new modifier group
     */
    public function submitForm()
    {
        $this->validate();

        // Ensure all modifier option translations are properly set
        foreach ($this->modifierOptions as $index => &$option) {
            foreach (array_keys($this->languages) as $lang) {
                if (!empty($this->modifierOptionInput[$index][$lang])) {
                    $option['name'][$lang] = $this->modifierOptionInput[$index][$lang];
                }
            }
        }

        try {
            DB::beginTransaction();

            // Create the modifier group with base translation
            $modifierGroup = ModifierGroup::create([
                'name' => $this->translationNames[$this->globalLocale],
                'description' => $this->translationDescriptions[$this->globalLocale],
                'branch_id' => branch()->id,
            ]);

            // Create translations for other languages
            $translations = collect($this->translationNames)
                ->filter(fn($name, $locale) => !empty($name) || !empty($this->translationDescriptions[$locale]))
                ->map(fn($name, $locale) => [
                    'locale' => $locale,
                    'name' => $name,
                    'description' => $this->translationDescriptions[$locale] ?? ''
                ])->values()->all();

            if (!empty($translations)) {
                $modifierGroup->translations()->createMany($translations);
            }

            // Create modifier options
            $options = collect($this->modifierOptions)->map(function($option, $index) {
                return [
                    'name' => $option['name'],
                    'price' => $option['price'],
                    'is_available' => $option['is_available'],
                    'sort_order' => $index,
                ];
            })->all();

            $modifierGroup->options()->createMany($options);

            // Associate with menu items and variations if any
            $itemModifiers = [];

            foreach ($this->selectedMenuItems as $menuItemId) {
                // Get the menu item to check if it has variations
                $menuItem = $this->allMenuItems->firstWhere('id', $menuItemId);

                if ($menuItem && $menuItem->variations->count() > 0 && isset($this->selectedVariations[$menuItemId])) {
                    // Check if base item is selected (applies to all variations)
                    if (isset($this->selectedVariations[$menuItemId]['item']) && $this->selectedVariations[$menuItemId]['item']) {
                        // Apply to base item (all variations will see this modifier)
                        $itemModifiers[] = [
                            'menu_item_id' => $menuItemId,
                            'menu_item_variation_id' => null, // null means applies to all variations
                            'modifier_group_id' => $modifierGroup->id,
                        ];
                    }
                    // Only check specific variations if base item is not selected
                    else {
                        // Check for specific variations selected
                        $hasSelectedVariations = false;
                        foreach ($this->selectedVariations[$menuItemId] as $variationId => $isSelected) {
                            // Skip the 'item' key as it's not a variation ID
                            if ($variationId !== 'item' && $isSelected) {
                                $hasSelectedVariations = true;
                                // Apply to specific variation
                                $itemModifiers[] = [
                                    'menu_item_id' => $menuItemId,
                                    'menu_item_variation_id' => $variationId,
                                    'modifier_group_id' => $modifierGroup->id,
                                ];
                            }
                        }

                        // If no specific variations were selected, apply to base item automatically
                        // Note: base item selection is already handled by updatedSelectedVariations method
                        if (!$hasSelectedVariations) {
                            // By this point, the base item should already be selected due to our updatedSelectedVariations method
                            $itemModifiers[] = [
                                'menu_item_id' => $menuItemId,
                                'menu_item_variation_id' => null,
                                'modifier_group_id' => $modifierGroup->id,
                            ];
                        }
                    }
                } else {
                    // For items without variations, apply to the base item
                    $itemModifiers[] = [
                        'menu_item_id' => $menuItemId,
                        'menu_item_variation_id' => null,
                        'modifier_group_id' => $modifierGroup->id,
                    ];
                }
            }

            if (!empty($itemModifiers)) {
                ItemModifier::insert($itemModifiers);
            }

            DB::commit();

            // Show success message and reset form
            $this->alert('success', __('messages.ModifierGroupAdded'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
            ]);

            // Close the modal and reset form
            $this->dispatch('hideAddModifierGroupModal');
            $this->reset([
                'name', 'description', 'modifierOptions', 'modifierOptionInput',
                'modifierOptionName', 'selectedMenuItems', 'selectedVariations',
                'search', 'isOpen', 'translationNames', 'translationDescriptions'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', __('messages.somethingWentWrong'), [
                'toast' => true,
                'position' => 'top-end',
            ]);
        }
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.forms.add-modifier-group');
    }
}
