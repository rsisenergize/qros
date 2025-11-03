<?php

namespace App\Livewire\Forms;

use App\Models\Tax;
use Livewire\Component;
use App\Models\MenuItem;
use App\Models\OrderType;
use App\Models\ItemModifier;
use App\Models\ModifierGroup;
use App\Models\ModifierOption;
use Illuminate\Support\Facades\DB;
use App\Models\DeliveryPlatform;
use App\Models\ModifierOptionPrice;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class CreateModifierGroup extends Component
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

    // Pricing Properties for each modifier option
    public array $optionOrderTypePrices = []; // Structure: [optionIndex => [orderTypeId => price]]
    public array $optionDeliveryPrices = []; // Structure: [optionIndex => [appId => calculated_price]]
    public array $optionPlatformAvailability = []; // Structure: [optionIndex => [appId => bool]]
    public array $optionBaseDeliveryPrice = []; // Structure: [optionIndex => price]

    // Menu items selection
    public $search = '';
    public $isOpen = false;
    public $selectedMenuItems = [];
    public $selectedVariations = [];

    // Cached data
    public $allMenuItems;
    public $orderTypes;
    public $deliveryApps;

    // Track which menu item variations are expanded in the UI
    public $expandedVariations = [];

    protected function rules()
    {
        $baseRules = [
            'description' => 'nullable',
            'modifierOptions.*.price' => 'required|numeric|min:0',
            'modifierOptions.*.is_available' => 'required|boolean',
            'selectedMenuItems' => 'required|array|min:1',
        ];

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

        // Initialize translation arrays
        $languageKeys = array_keys($this->languages);
        $this->translationNames = array_fill_keys($languageKeys, '');
        $this->translationDescriptions = array_fill_keys($languageKeys, '');

        // Initialize pricing collections
        $this->orderTypes = OrderType::where('is_active', 1)->get();
        $this->deliveryApps = DeliveryPlatform::where('is_active', 1)->get();

        // Add first empty modifier option
        $this->addModifierOption();

        // Eager load menu items
        $this->allMenuItems = MenuItem::with(['variations' => function($query) {
            $query->select('id', 'menu_item_id', 'variation');
        }])->select('id', 'item_name')->get();
    }

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

    public function updatedCurrentLanguage()
    {
        $this->name = $this->translationNames[$this->currentLanguage] ?? '';
        $this->description = $this->translationDescriptions[$this->currentLanguage] ?? '';

        foreach ($this->modifierOptions as $index => $option) {
            $this->modifierOptionName[$index] = $option['name'][$this->currentLanguage] ?? '';
        }
    }

    public function updateTranslation()
    {
        $this->translationNames[$this->currentLanguage] = $this->name;
        $this->translationDescriptions[$this->currentLanguage] = $this->description;
    }

    public function updateModifierOptionTranslation($index)
    {
        $lang = $this->currentLanguage;
        $this->modifierOptions[$index]['name'][$lang] = $this->modifierOptionName[$index];

        if (!isset($this->modifierOptionInput[$index])) {
            $this->modifierOptionInput[$index] = [];
        }
        $this->modifierOptionInput[$index][$lang] = $this->modifierOptionName[$index];
    }

    public function addModifierOption()
    {
        $option = $this->newModifierOption();
        $this->modifierOptions[] = $option;

        $index = count($this->modifierOptions) - 1;
        $this->modifierOptionName[$index] = '';
        $this->modifierOptionInput[$index] = array_fill_keys(array_keys($this->languages), '');

        // Initialize pricing for this option
        $this->initializeOptionPricing($index);
    }

    private function initializeOptionPricing(int $index): void
    {
        // Initialize order type prices
        if (!isset($this->optionOrderTypePrices[$index])) {
            $this->optionOrderTypePrices[$index] = [];
        }
        
        foreach ($this->orderTypes as $orderType) {
            $this->optionOrderTypePrices[$index][$orderType->id] = '';
        }

        // Initialize delivery prices and availability - DEFAULT TO TRUE
        if (!isset($this->optionDeliveryPrices[$index])) {
            $this->optionDeliveryPrices[$index] = [];
        }
        
        if (!isset($this->optionPlatformAvailability[$index])) {
            $this->optionPlatformAvailability[$index] = [];
        }
        
        foreach ($this->deliveryApps as $app) {
            $this->optionDeliveryPrices[$index][$app->id] = '0.00';
            $this->optionPlatformAvailability[$index][$app->id] = true;
        }

        $this->optionBaseDeliveryPrice[$index] = '';
        
        // Calculate initial delivery prices
        $this->calculateOptionDeliveryPrices($index);
    }

    public function removeModifierOption($index)
    {
        // Ensure index is valid
        if (!isset($this->modifierOptions[$index])) {
            return;
        }

        if (count($this->modifierOptions) <= 1) {
            $this->alert('warning', __('You must have at least one modifier option'), [
                'toast' => true,
                'position' => 'top-end',
            ]);
            return;
        }

        // Remove all related data
        unset(
            $this->modifierOptions[$index],
            $this->modifierOptionInput[$index],
            $this->modifierOptionName[$index],
            $this->optionOrderTypePrices[$index],
            $this->optionDeliveryPrices[$index],
            $this->optionPlatformAvailability[$index],
            $this->optionBaseDeliveryPrice[$index]
        );

        // Reindex all arrays at once for better performance
        $arrays = [
            'modifierOptions',
            'modifierOptionInput',
            'modifierOptionName',
            'optionOrderTypePrices',
            'optionDeliveryPrices',
            'optionPlatformAvailability',
            'optionBaseDeliveryPrice'
        ];
        
        foreach ($arrays as $arrayName) {
            $this->$arrayName = array_values($this->$arrayName);
        }

        // Show success message
        $this->alert('success', __('Modifier option removed'), [
            'toast' => true,
            'position' => 'top-end',
        ]);
    }

    // PRICING MANAGEMENT
    public function updatedModifierOptions($value, $key): void
    {
        // Parse the key to get option index and orderType
        $parts = explode('.', $key);
        if (count($parts) >= 2 && is_numeric($parts[0])) {
            $optionIndex = (int)$parts[0];
            
            // Ensure the option exists
            if (!isset($this->modifierOptions[$optionIndex]) || !isset($this->optionOrderTypePrices[$optionIndex])) {
                return;
            }
            
            $orderTypeId = (int)$parts[1];
            
            // Only proceed with auto-filling if the current value is not empty
            if (!empty($value)) {
                // Check if all other prices are empty (first price entry)
                $allOthersEmpty = true;
                foreach ($this->optionOrderTypePrices[$optionIndex] as $otId => $price) {
                    if ($otId != $orderTypeId && !empty($price)) {
                        $allOthersEmpty = false;
                        break;
                    }
                }
                
                // If all others are empty, this is the first price entry
                if ($allOthersEmpty) {
                    // Auto-fill all other order types and base delivery price in one loop
                    foreach ($this->optionOrderTypePrices[$optionIndex] as $otId => &$price) {
                        if ($otId != $orderTypeId) {
                            $price = $value;
                        }
                    }
                    unset($price); // Break reference
                    
                    // Set base delivery price if empty
                    if (empty($this->optionBaseDeliveryPrice[$optionIndex])) {
                        $this->optionBaseDeliveryPrice[$optionIndex] = $value;
                    }
                }
            }
            
            // Always recalculate delivery prices after an update
            $this->calculateOptionDeliveryPrices($optionIndex);
        }
    }

    public function updatedOptionBaseDeliveryPrice($value, $key): void
    {
        $this->calculateOptionDeliveryPrices((int)$key);
    }

    private function calculateOptionDeliveryPrices(int $optionIndex): void
    {
        // Check if the option exists
        if (!isset($this->modifierOptions[$optionIndex])) {
            return;
        }

        // Ensure we have delivery apps
        if (!$this->deliveryApps || $this->deliveryApps->isEmpty()) {
            return;
        }

        $basePrice = (float)($this->modifierOptions[$optionIndex]['price'] ?? 0);
        $baseDeliveryPrice = (float)($this->optionBaseDeliveryPrice[$optionIndex] ?? 0);
        
        // Ensure prices are not negative
        $basePrice = max(0, $basePrice);
        $baseDeliveryPrice = max(0, $baseDeliveryPrice);

        foreach ($this->deliveryApps as $app) {
            // Ensure the pricing array is initialized
            if (!isset($this->optionDeliveryPrices[$optionIndex])) {
                $this->optionDeliveryPrices[$optionIndex] = [];
            }

            // Only calculate if platform is available
            if (isset($this->optionPlatformAvailability[$optionIndex][$app->id]) 
                && $this->optionPlatformAvailability[$optionIndex][$app->id]) {
                $commission = (float)($app->commission_value ?? 0);
                $priceToUse = $baseDeliveryPrice > 0 ? $baseDeliveryPrice : $basePrice;
                
                // Calculate final price with commission
                $calculatedPrice = $priceToUse * (1 + ($commission / 100));
                
                $this->optionDeliveryPrices[$optionIndex][$app->id] = number_format($calculatedPrice, 2, '.', '');
            } else {
                // Set to 0.00 if not available
                $this->optionDeliveryPrices[$optionIndex][$app->id] = '0.00';
            }
        }
    }

    public function updatedOptionPlatformAvailability($value, $key): void
    {
        $parts = explode('.', $key);
        if (count($parts) >= 2 && is_numeric($parts[0])) {
            $optionIndex = (int)$parts[0];
            $this->calculateOptionDeliveryPrices($optionIndex);
        }
    }

    public function updatedIsOpen($value)
    {
        if (!$value) {
            $this->reset(['search']);
        }
    }

    public function updatedSelectedVariations($value, $key)
    {
        if (strpos($key, '.') !== false) {
            list($menuItemId, $variationId) = explode('.', $key);
            
            if ($variationId === 'item' && $value === true) {
                if (isset($this->selectedVariations[$menuItemId])) {
                    foreach ($this->selectedVariations[$menuItemId] as $varId => $isSelected) {
                        if ($varId !== 'item') {
                            $this->selectedVariations[$menuItemId][$varId] = false;
                        }
                    }
                }
            }
            elseif ($variationId !== 'item' && $value === true) {
                if (isset($this->selectedVariations[$menuItemId]['item'])) {
                    $this->selectedVariations[$menuItemId]['item'] = false;
                }
            }
            elseif ($variationId !== 'item' && $value === false) {
                $anyVariationSelected = false;
                foreach ($this->selectedVariations[$menuItemId] as $varId => $isSelected) {
                    if ($varId !== 'item' && $isSelected) {
                        $anyVariationSelected = true;
                        break;
                    }
                }

                if (!$anyVariationSelected) {
                    $this->selectedVariations[$menuItemId]['item'] = true;
                }
            }
        }
    }

    public function getMenuItemsProperty()
    {
        if (empty($this->search)) {
            return $this->allMenuItems;
        }

        return $this->allMenuItems->filter(function($item) {
            return stripos($item->item_name, $this->search) !== false;
        });
    }

    public function toggleVariationExpansion($menuItemId)
    {
        if (in_array($menuItemId, $this->expandedVariations)) {
            $this->expandedVariations = array_diff($this->expandedVariations, [$menuItemId]);
        } else {
            $this->expandedVariations[] = $menuItemId;
        }
    }

    public function toggleSelectItem($item)
    {
        $itemId = $item['id'];
        $menuItem = $this->allMenuItems->firstWhere('id', $itemId);
        $hasVariations = $menuItem && $menuItem->variations->count() > 0;

        if (($key = array_search($itemId, $this->selectedMenuItems)) !== false) {
            unset($this->selectedMenuItems[$key]);

            if (isset($this->selectedVariations[$itemId])) {
                unset($this->selectedVariations[$itemId]);
            }

            if (in_array($itemId, $this->expandedVariations)) {
                $this->expandedVariations = array_diff($this->expandedVariations, [$itemId]);
            }
        } else {
            $this->selectedMenuItems[] = $itemId;

            if ($hasVariations) {
                $this->selectedVariations[$itemId] = ['item' => true];

                foreach ($menuItem->variations as $variation) {
                    $this->selectedVariations[$itemId][$variation->id] = false;
                }

                if (!in_array($itemId, $this->expandedVariations)) {
                    $this->expandedVariations[] = $itemId;
                }
            }
        }

        $this->selectedMenuItems = array_values($this->selectedMenuItems);
    }

    public function submitForm()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Show validation errors
            $this->alert('error', __('Please fix the errors in the form'), [
                'toast' => true,
                'position' => 'top-end',
            ]);
            throw $e;
        }

        // Ensure all modifier option translations are synced
        foreach ($this->modifierOptions as $index => &$option) {
            foreach (array_keys($this->languages) as $lang) {
                if (!empty($this->modifierOptionInput[$index][$lang])) {
                    $option['name'][$lang] = $this->modifierOptionInput[$index][$lang];
                }
            }
        }
        unset($option); // Break reference

        try {
            DB::beginTransaction();

            // Create the modifier group
            $modifierGroup = ModifierGroup::create([
                'name' => $this->translationNames[$this->globalLocale],
                'description' => $this->translationDescriptions[$this->globalLocale] ?? null,
                'branch_id' => branch()->id,
            ]);

            if (!$modifierGroup) {
                throw new \Exception('Failed to create modifier group');
            }

            // Create translations
            $translations = collect($this->translationNames)
                ->filter(fn($name, $locale) => !empty($name) || !empty($this->translationDescriptions[$locale]))
                ->map(fn($name, $locale) => [
                    'locale' => $locale,
                    'name' => $name ?? '',
                    'description' => $this->translationDescriptions[$locale] ?? ''
                ])->values()->all();

            if (!empty($translations)) {
                $modifierGroup->translations()->createMany($translations);
            }

            // Create modifier options with pricing
            foreach ($this->modifierOptions as $index => $option) {
                // Ensure price is valid
                $price = isset($option['price']) && is_numeric($option['price']) 
                    ? (float)$option['price'] 
                    : 0.0;

                $modifierOption = $modifierGroup->options()->create([
                    'name' => $option['name'],
                    'price' => $price,
                    'is_available' => $option['is_available'] ?? true,
                    'sort_order' => $index,
                ]);

                if (!$modifierOption) {
                    throw new \Exception('Failed to create modifier option');
                }

                // Save pricing data for this modifier option
                $this->saveOptionPricingData($modifierOption->id, $modifierGroup->id, $price, $index);
            }

            // Associate with menu items and variations
            $itemModifiers = [];

            foreach ($this->selectedMenuItems as $menuItemId) {
                $menuItem = $this->allMenuItems->firstWhere('id', $menuItemId);

                if (!$menuItem) {
                    continue; // Skip if menu item not found
                }

                if ($menuItem->variations->count() > 0 && isset($this->selectedVariations[$menuItemId])) {
                    if (isset($this->selectedVariations[$menuItemId]['item']) && $this->selectedVariations[$menuItemId]['item']) {
                        $itemModifiers[] = [
                            'menu_item_id' => $menuItemId,
                            'menu_item_variation_id' => null,
                            'modifier_group_id' => $modifierGroup->id,
                        ];
                    } else {
                        $hasSelectedVariations = false;
                        foreach ($this->selectedVariations[$menuItemId] as $variationId => $isSelected) {
                            if ($variationId !== 'item' && $isSelected) {
                                $hasSelectedVariations = true;
                                $itemModifiers[] = [
                                    'menu_item_id' => $menuItemId,
                                    'menu_item_variation_id' => $variationId,
                                    'modifier_group_id' => $modifierGroup->id,
                                ];
                            }
                        }

                        if (!$hasSelectedVariations) {
                            $itemModifiers[] = [
                                'menu_item_id' => $menuItemId,
                                'menu_item_variation_id' => null,
                                'modifier_group_id' => $modifierGroup->id,
                            ];
                        }
                    }
                } else {
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

            $this->alert('success', __('messages.ModifierGroupAdded'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
            ]);

            $this->resetForm();
            $this->redirect(route('modifier-groups.index'), true);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', __('messages.somethingWentWrong') . ': ' . $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end',
            ]);
        }
    }

    private function saveOptionPricingData(int $modifierOptionId, int $modifierGroupId, float $basePrice, int $optionIndex): void
    {
        $pricingRecords = [];

        // Validate inputs
        if ($basePrice < 0) {
            $basePrice = 0;
        }

        // Ensure the option index exists in our pricing arrays
        if (!isset($this->optionOrderTypePrices[$optionIndex])) {
            return;
        }

        // Create pricing for regular order types
        foreach ($this->orderTypes as $orderType) {
            
            // // Skip delivery order type in this loop as it's handled separately
            // if (strtolower($orderType->slug ?? $orderType->name ?? '') === 'delivery') {
            //     continue;
            // }

            $customPrice = $this->optionOrderTypePrices[$optionIndex][$orderType->id] ?? '';
            $calculatedPrice = (!empty($customPrice) && is_numeric($customPrice))
                ? (float)$customPrice
                : $basePrice;

            // Ensure price is not negative
            $calculatedPrice = max(0, $calculatedPrice);

            $pricingRecords[] = [
                'modifier_group_id' => $modifierGroupId,
                'modifier_option_id' => $modifierOptionId,
                'order_type_id' => $orderType->id,
                'delivery_app_id' => null,
                'calculated_price' => $calculatedPrice,
                'override_price' => null,
                'final_price' => $calculatedPrice,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        // Create pricing for delivery platforms
        $baseDeliveryPrice = (!empty($this->optionBaseDeliveryPrice[$optionIndex]) 
            && is_numeric($this->optionBaseDeliveryPrice[$optionIndex]))
            ? (float)$this->optionBaseDeliveryPrice[$optionIndex]
            : $basePrice;

        // Ensure base delivery price is not negative
        $baseDeliveryPrice = max(0, $baseDeliveryPrice);

        // Find delivery order type (or use first order type as fallback)
        $deliveryOrderType = $this->orderTypes->first(function($type) {
            return strtolower($type->slug ?? $type->name ?? '') === 'delivery';
        });
        
        // Fallback to first order type if no delivery type found
        if (!$deliveryOrderType) {
            $deliveryOrderType = $this->orderTypes->first();
        }

        // Ensure we have an order type before processing delivery platforms
        if ($deliveryOrderType && $this->deliveryApps && $this->deliveryApps->isNotEmpty()) {
            foreach ($this->deliveryApps as $app) {
                // Only save if platform is available
                if (isset($this->optionPlatformAvailability[$optionIndex][$app->id]) 
                    && $this->optionPlatformAvailability[$optionIndex][$app->id]) {
                    
                    $commission = (float)($app->commission_value ?? 0);
                    
                    // Use base delivery price if set, otherwise use base price
                    $priceToUse = $baseDeliveryPrice > 0 ? $baseDeliveryPrice : $basePrice;
                    
                    // Calculate final price with commission
                    $calculatedPrice = $priceToUse * (1 + ($commission / 100));
                    
                    // Ensure calculated price is not negative and round to 2 decimals
                    $calculatedPrice = max(0, round($calculatedPrice, 2));

                    $pricingRecords[] = [
                        'modifier_option_id' => $modifierOptionId,
                        'modifier_group_id' => $modifierGroupId,
                        'order_type_id' => $deliveryOrderType->id,
                        'delivery_app_id' => $app->id,
                        'calculated_price' => $calculatedPrice,
                        'override_price' => null,
                        'final_price' => $calculatedPrice,
                        'status' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (!empty($pricingRecords)) {
            ModifierOptionPrice::insert($pricingRecords);
        }
    }

    private function resetForm(): void
    {
        $this->reset([
            'name', 'description', 'modifierOptions', 'modifierOptionInput',
            'modifierOptionName', 'selectedMenuItems', 'selectedVariations',
            'search', 'isOpen', 'translationNames', 'translationDescriptions',
            'optionOrderTypePrices', 'optionDeliveryPrices',
            'optionPlatformAvailability', 'optionBaseDeliveryPrice', 'expandedVariations'
        ]);
        
        // Reinitialize with one empty option
        $this->addModifierOption();
        
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.forms.create-modifier-group');
    }
}
