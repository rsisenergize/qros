<?php

namespace App\Livewire\Forms;

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

class UpdateModifierGroup extends Component
{
    use LivewireAlert;

    public $modifierGroupId;
    public $name;
    public $description;
    public $modifierOptions = [];
    public $modifierOptionInput = [];
    public $modifierOptionName = [];
    public $modifierOptionIds = []; // Track existing option IDs for updates

    // Pricing Properties for each modifier option
    public array $optionOrderTypePrices = [];
    public array $optionDeliveryPrices = [];
    public array $optionPlatformAvailability = [];
    public array $optionBaseDeliveryPrice = [];

    // Menu items selection
    public $menuItems;
    public $selectedMenuItems = [];
    public $selectedVariations = [];
    public $search = '';
    public $isOpen = false;
    public $allMenuItems;
    public $expandedVariations = [];

    // Cached data
    public $orderTypes;
    public $deliveryApps;

    // Translation
    public $languages = [];
    public $translationNames = [];
    public $translationDescriptions = [];
    public $currentLanguage;
    public $globalLocale;

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
            'modifierOptions.*.price.required' => 'Modifier option price must have a price.',
            'modifierOptions.*.price.numeric' => 'Modifier option price must be a number.',
            'modifierOptions.*.price.min' => 'Modifier option price must be at least 0.',
            'selectedMenuItems.required' => 'Please select at least one menu item or location.',
            'selectedMenuItems.min' => 'Please select at least one menu item or location.',
            'translationNames.' . $this->globalLocale . '.required' => __('validation.modifierGroupNameRequired', ['language' => $this->languages[$this->globalLocale] ?? 'default']),
            'modifierOptions.*.name.' . $this->globalLocale . '.required' => __('validation.modifierOptionNameRequired', ['language' => $this->languages[$this->globalLocale] ?? 'default']),
        ];
    }

    public function mount($id)
    {
        $this->resetValidation();
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

        $modifierGroup = ModifierGroup::with(['options.prices', 'translations'])->findOrFail($id);
        $this->modifierGroupId = $modifierGroup->id;

        // Load translations
        foreach ($modifierGroup->translations as $translation) {
            $this->translationNames[$translation->locale] = $translation->name;
            $this->translationDescriptions[$translation->locale] = $translation->description;
        }

        $this->name = $this->translationNames[$this->currentLanguage] ?: $modifierGroup->name;
        $this->description = $this->translationDescriptions[$this->currentLanguage] ?: $modifierGroup->description;

        // Load modifier options with translations and pricing
        $this->modifierOptions = $modifierGroup->options->map(function ($option, $optIndex) {
            $this->modifierOptionInput[$optIndex] = [];
            $this->modifierOptionIds[$optIndex] = $option->id;

            $optionNames = $option->getTranslations('name');

            foreach (array_keys($this->languages) as $lang) {
                $this->modifierOptionInput[$optIndex][$lang] = $optionNames[$lang] ?? '';
            }

            $this->modifierOptionName[$optIndex] = $optionNames[$this->currentLanguage] ?? '';

            // Load pricing data for this option
            $this->loadOptionPricing($optIndex, $option->id);

            return [
                'id' => $option->id,
                'name' => $optionNames,
                'price' => $option->price,
                'is_available' => (bool) $option->is_available,
                'sort_order' => $option->sort_order,
            ];
        })->toArray();

        // Load menu items
        $this->allMenuItems = MenuItem::with(['variations' => function($query) {
            $query->select('id', 'menu_item_id', 'variation');
        }])->select('id', 'item_name')->get();

        // Load currently associated menu items
        $this->selectedMenuItems = $modifierGroup->itemModifiers()->pluck('menu_item_id')->unique()->toArray();

        // Load variations
        $itemModifiers = $modifierGroup->itemModifiers()->get();
        foreach ($itemModifiers as $itemModifier) {
            if ($itemModifier->menu_item_variation_id) {
                if (!isset($this->selectedVariations[$itemModifier->menu_item_id])) {
                    $this->selectedVariations[$itemModifier->menu_item_id] = ['item' => false];
                }
                $this->selectedVariations[$itemModifier->menu_item_id][$itemModifier->menu_item_variation_id] = true;
            } else {
                $this->selectedVariations[$itemModifier->menu_item_id] = ['item' => true];
            }
        }

        $this->updateTranslation();
        $this->syncModifierOptions();
    }

    private function loadOptionPricing(int $optionIndex, int $optionId): void
    {
        // Initialize pricing arrays for this option
        foreach ($this->orderTypes as $orderType) {
            $this->optionOrderTypePrices[$optionIndex][$orderType->id] = '';
        }

        foreach ($this->deliveryApps as $app) {
            $this->optionDeliveryPrices[$optionIndex][$app->id] = '';
            $this->optionPlatformAvailability[$optionIndex][$app->id] = false;
        }

        $this->optionBaseDeliveryPrice[$optionIndex] = '';

        // Load existing pricing
        $prices = ModifierOptionPrice::where('modifier_option_id', $optionId)->get();

        foreach ($prices as $price) {
            if ($price->delivery_app_id) {
                // Delivery platform pricing
                $this->optionDeliveryPrices[$optionIndex][$price->delivery_app_id] = $price->calculated_price;
                $this->optionPlatformAvailability[$optionIndex][$price->delivery_app_id] = $price->status;
            } else {
                // Regular order type pricing
                $this->optionOrderTypePrices[$optionIndex][$price->order_type_id] = $price->calculated_price;
            }
        }

        // Calculate base delivery price (reverse calculate from first available platform)
        foreach ($this->deliveryApps as $app) {
            if (!empty($this->optionPlatformAvailability[$optionIndex][$app->id])) {
                $calculatedPrice = (float)$this->optionDeliveryPrices[$optionIndex][$app->id];
                $commission = (float)$app->commission_value;
                if ($commission > 0) {
                    $this->optionBaseDeliveryPrice[$optionIndex] = number_format(
                        $calculatedPrice / (1 + $commission / 100),
                        2,
                        '.',
                        ''
                    );
                }
                break;
            }
        }
    }

    protected function syncModifierOptions()
    {
        if (!is_array($this->languages) || empty($this->languages)) {
            return;
        }

        if (!isset($this->currentLanguage) || !isset($this->languages[$this->currentLanguage])) {
            $this->currentLanguage = $this->globalLocale ?? array_key_first($this->languages);
        }

        $this->name = $this->translationNames[$this->currentLanguage] ?? '';
        $this->description = $this->translationDescriptions[$this->currentLanguage] ?? '';

        if (!is_array($this->modifierOptions)) {
            return;
        }

        foreach ($this->modifierOptions as $index => $option) {
            if (!isset($this->modifierOptionInput[$index])) {
                $this->modifierOptionInput[$index] = [];
            }

            if (!isset($option['name']) || !is_array($option['name'])) {
                $option['name'] = [];
            }

            foreach (array_keys($this->languages) as $lang) {
                $this->modifierOptionInput[$index][$lang] = $option['name'][$lang] ?? '';
            }

            $this->modifierOptionName[$index] = $this->modifierOptionInput[$index][$this->currentLanguage] ?? '';
        }
    }

    public function newModifierOption()
    {
        $langs = !empty($this->languages) ? array_keys($this->languages) : [$this->globalLocale ?? 'en'];
        return [
            'id' => uniqid(),
            'name' => array_fill_keys($langs, ''),
            'price' => 0,
            'is_available' => true,
            'sort_order' => count($this->modifierOptions),
        ];
    }

    public function updateTranslation()
    {
        $this->translationNames[$this->currentLanguage] = $this->name;
        $this->translationDescriptions[$this->currentLanguage] = $this->description;
    }

    public function updatedCurrentLanguage($value)
    {
        // Save current language data before switching
        $this->updateTranslation();

        // Load data for the new language
        $this->name = $this->translationNames[$value] ?? '';
        $this->description = $this->translationDescriptions[$value] ?? '';

        // Update modifier option names for the new language
        if (!empty($this->modifierOptions)) {
            foreach ($this->modifierOptions as $index => $option) {
                if (isset($this->modifierOptionInput[$index]) && is_array($this->modifierOptionInput[$index])) {
                    $this->modifierOptionName[$index] = $this->modifierOptionInput[$index][$value] ?? '';
                } else {
                    $this->modifierOptionName[$index] = '';
                }
            }
        }
    }

    public function updateModifierOptionTranslation($index)
    {
        if (!isset($this->modifierOptionInput[$index]) || !is_array($this->modifierOptionInput[$index])) {
            $this->modifierOptionInput[$index] = array_fill_keys(array_keys($this->languages), '');
        }

        if (!isset($this->modifierOptions[$index])) {
            return;
        }

        $lang = $this->currentLanguage;
        $this->modifierOptionInput[$index][$lang] = $this->modifierOptionName[$index] ?? '';

        if (!isset($this->modifierOptions[$index]['name']) || !is_array($this->modifierOptions[$index]['name'])) {
            $this->modifierOptions[$index]['name'] = array_fill_keys(array_keys($this->languages), '');
        }

        $this->modifierOptions[$index]['name'][$lang] = $this->modifierOptionName[$index] ?? '';
    }

    public function addModifierOption()
    {
        $index = count($this->modifierOptions);
        $this->modifierOptions[] = $this->newModifierOption();
        $this->initializeOptionPricing($index);

        // Initialize translations for new option
        $this->modifierOptionInput[$index] = array_fill_keys(array_keys($this->languages), '');
        $this->modifierOptionName[$index] = '';

        // Explicitly set that this new option has NO existing ID
        // This is critical to differentiate new options from existing ones
        $this->modifierOptionIds[$index] = null;
    }

    private function initializeOptionPricing(int $index): void
    {
        foreach ($this->orderTypes as $orderType) {
            $this->optionOrderTypePrices[$index][$orderType->id] = '';
        }

        foreach ($this->deliveryApps as $app) {
            $this->optionDeliveryPrices[$index][$app->id] = '';
            $this->optionPlatformAvailability[$index][$app->id] = true;
        }

        $this->optionBaseDeliveryPrice[$index] = '';
    }

    public function removeModifierOption($index)
    {
        unset(
            $this->modifierOptions[$index],
            $this->modifierOptionInput[$index],
            $this->modifierOptionName[$index],
            $this->modifierOptionIds[$index],
            $this->optionOrderTypePrices[$index],
            $this->optionDeliveryPrices[$index],
            $this->optionPlatformAvailability[$index],
            $this->optionBaseDeliveryPrice[$index]
        );

        $this->modifierOptions = array_values($this->modifierOptions);
        $this->modifierOptionInput = array_values($this->modifierOptionInput);
        $this->modifierOptionName = array_values($this->modifierOptionName);
        $this->modifierOptionIds = array_values($this->modifierOptionIds);
        $this->optionOrderTypePrices = array_values($this->optionOrderTypePrices);
        $this->optionDeliveryPrices = array_values($this->optionDeliveryPrices);
        $this->optionPlatformAvailability = array_values($this->optionPlatformAvailability);
        $this->optionBaseDeliveryPrice = array_values($this->optionBaseDeliveryPrice);
    }

    // PRICING MANAGEMENT
    public function updatedModifierOptions($value, $key): void
    {
        // When base price changes, recalculate delivery prices
        $parts = explode('.', $key);
        if (count($parts) >= 2 && $parts[1] === 'price') {
            $optionIndex = (int)$parts[0];
            $this->calculateOptionDeliveryPrices($optionIndex);
        }
    }

    public function updatedOptionBaseDeliveryPrice($value, $key): void
    {
        $this->calculateOptionDeliveryPrices($key);
    }

    public function updatedOptionOrderTypePrices($value, $key): void
    {
        $parts = explode('.', $key);
        if (count($parts) >= 1) {
            $optionIndex = (int)$parts[0];
            $this->calculateOptionDeliveryPrices($optionIndex);
        }
    }

    private function calculateOptionDeliveryPrices(int $optionIndex): void
    {
        $basePrice = (float)($this->modifierOptions[$optionIndex]['price'] ?? 0);
        $baseDeliveryPrice = (float)($this->optionBaseDeliveryPrice[$optionIndex] ?? 0);

        foreach ($this->deliveryApps as $app) {
                $commission = (float)$app->commission_value;
                $calculatedPrice = $baseDeliveryPrice > 0
                    ? $baseDeliveryPrice + ($baseDeliveryPrice * $commission / 100)
                    : $basePrice + ($basePrice * $commission / 100);
                $this->optionDeliveryPrices[$optionIndex][$app->id] = number_format($calculatedPrice, 2, '.', '');
        }
    }

    public function updatedOptionPlatformAvailability($value, $key): void
    {
        $parts = explode('.', $key);
        if (count($parts) >= 2) {
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

    public function toggleVariationExpansion($menuItemId)
    {
        if (in_array($menuItemId, $this->expandedVariations)) {
            $this->expandedVariations = array_diff($this->expandedVariations, [$menuItemId]);
        } else {
            $this->expandedVariations[] = $menuItemId;
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
        if (!$this->allMenuItems) {
            return collect([]);
        }

        if (empty($this->search)) {
            return $this->allMenuItems;
        }

        return $this->allMenuItems->filter(function($item) {
            return $item && isset($item->item_name) && stripos($item->item_name, $this->search) !== false;
        });
    }

    public function submitForm()
    {
        // Update current language translations before validation
        $this->updateTranslation();

        // Update all modifier option translations
        if (is_array($this->modifierOptions)) {
            foreach ($this->modifierOptions as $index => $option) {
                $this->updateModifierOptionTranslation($index);
            }
        }

        $this->validate($this->rules(), $this->messages());

        // Ensure all modifier option names are arrays
        if (is_array($this->modifierOptions)) {
            foreach ($this->modifierOptions as $index => &$option) {
                $option['name'] = [];

                if (isset($this->modifierOptionInput[$index]) && is_array($this->modifierOptionInput[$index])) {
                    foreach (array_keys($this->languages) as $lang) {
                        $option['name'][$lang] = $this->modifierOptionInput[$index][$lang] ?? '';
                    }
                }

                $option['name'] = array_filter($option['name'], fn($val) => !empty(trim($val)));
            }
        }

        try {
            DB::beginTransaction();

            $modifierGroup = ModifierGroup::findOrFail($this->modifierGroupId);
            $modifierGroup->update([
                'name' => $this->translationNames[$this->globalLocale],
                'description' => $this->translationDescriptions[$this->globalLocale],
            ]);

            // Update translations
            $modifierGroup->translations()->delete();
            $translations = collect($this->translationNames)
                ->filter(fn($name, $locale) => !empty($name) || !empty($this->translationDescriptions[$locale]))
                ->map(fn($name, $locale) => [
                    'locale' => $locale,
                    'name' => $name,
                    'description' => $this->translationDescriptions[$locale]
                ])->values()->all();

            $modifierGroup->translations()->createMany($translations);

            // Track which options we've processed (to know what to delete)
            $processedOptionIds = [];

            // Update existing or create new modifier options
            foreach ($this->modifierOptions as $index => $option) {
                // Prepare translations for this option
                $optionNames = [];
                if (isset($this->modifierOptionInput[$index]) && is_array($this->modifierOptionInput[$index])) {
                    foreach (array_keys($this->languages) as $lang) {
                        $translatedName = $this->modifierOptionInput[$index][$lang] ?? '';
                        if (!empty(trim($translatedName))) {
                            $optionNames[$lang] = trim($translatedName);
                        }
                    }
                }

                $optionData = [
                    'name' => !empty($optionNames) ? $optionNames : $option['name'], // Spatie will cast this as array
                    'price' => $option['price'],
                    'is_available' => $option['is_available'],
                    'sort_order' => $option['sort_order'],
                ];

                // Check if this is an existing option (has an ID) or a new one
                $hasExistingId = isset($this->modifierOptionIds[$index]) && !empty($this->modifierOptionIds[$index]);

                if ($hasExistingId) {
                    // UPDATE existing option
                    $optionId = $this->modifierOptionIds[$index];
                    $modifierOption = ModifierOption::find($optionId);

                    if ($modifierOption && $modifierOption->modifier_group_id == $modifierGroup->id) {
                        $modifierOption->update($optionData);
                        $processedOptionIds[] = $optionId;

                        // Delete old pricing and save new pricing data
                        ModifierOptionPrice::where('modifier_option_id', $optionId)->delete();
                        $this->saveOptionPricingData($optionId, (float)$option['price'], $index);
                    }
                } else {
                    // CREATE new option (no ID means it's a newly added option)
                    $modifierOption = $modifierGroup->options()->create($optionData);

                    // Store the new ID for tracking
                    $this->modifierOptionIds[$index] = $modifierOption->id;
                    $processedOptionIds[] = $modifierOption->id;

                    // Save pricing data for this new option
                    $this->saveOptionPricingData($modifierOption->id, (float)$option['price'], $index);
                }
            }

            // Delete ONLY options that were removed (not in processed list)
            if (!empty($processedOptionIds)) {
                ModifierOption::where('modifier_group_id', $modifierGroup->id)
                    ->whereNotIn('id', $processedOptionIds)
                    ->delete();
            }

            // Update menu item associations
            $modifierGroup->itemModifiers()->delete();

            $itemModifiers = [];
            foreach ($this->selectedMenuItems as $menuItemId) {
                $menuItem = $this->allMenuItems->firstWhere('id', $menuItemId);

                if ($menuItem && $menuItem->variations->count() > 0 && isset($this->selectedVariations[$menuItemId])) {
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


            $this->alert('success', __('messages.ModifierGroupUpdated'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
            ]);

            $this->redirect(route('modifier-groups.index'), true);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', __('messages.somethingWentWrong') . ': ' . $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end',
            ]);
        }
    }

    private function saveOptionPricingData(int $modifierOptionId, float $basePrice, int $optionIndex): void
    {
        $pricingRecords = [];

        // Create pricing for regular order types
        foreach ($this->orderTypes as $orderType) {
            $calculatedPrice = !empty($this->optionOrderTypePrices[$optionIndex][$orderType->id])
                ? (float)$this->optionOrderTypePrices[$optionIndex][$orderType->id]
                : $basePrice;

            $pricingRecords[] = [
                'modifier_option_id' => $modifierOptionId,
                'modifier_group_id' => $this->modifierGroupId,
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
        $baseDeliveryPrice = !empty($this->optionBaseDeliveryPrice[$optionIndex])
            ? (float)$this->optionBaseDeliveryPrice[$optionIndex]
            : $basePrice;

        foreach ($this->deliveryApps as $app) {
            if (!empty($this->optionPlatformAvailability[$optionIndex][$app->id])) {
                $commission = (float)$app->commission_value;
                $calculatedPrice = $baseDeliveryPrice + ($baseDeliveryPrice * $commission / 100);

                $pricingRecords[] = [
                    'modifier_option_id' => $modifierOptionId,
                    'modifier_group_id' => $this->modifierGroupId,
                    'order_type_id' => $this->orderTypes->first()->id,
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

        if (!empty($pricingRecords)) {
            ModifierOptionPrice::insert($pricingRecords);
        }
    }

    public function render()
    {
        $this->syncModifierOptions();
        return view('livewire.forms.update-modifier-group');
    }
}
