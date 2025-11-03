<?php


namespace App\Livewire\Forms;

use App\Models\Tax;
use App\Models\Menu;
use App\Helper\Files;
use Livewire\Component;
use App\Models\KotPlace;
use App\Models\MenuItem;
use App\Models\OrderType;
use App\Models\ItemCategory;
use Livewire\WithFileUploads;
use App\Models\MenuItemPrices;
use App\Models\DeliveryPlatform;
use App\Models\MenuItemVariation;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use App\Scopes\AvailableMenuItemScope;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class UpdateMenuItem extends Component
{
    use WithFileUploads, LivewireAlert;

    protected $listeners = ['refreshCategories'];

    // MenuItem being edited
    public MenuItem $menuItem;

    // Core Properties
    #[Validate('required')]
    public string $itemName = '';

    #[Validate('required')]
    public string $menu = '';

    #[Validate('required')]
    public string $itemCategory = '';

    #[Validate('nullable|string')]
    public string $itemDescription = '';

    #[Validate('required|in:veg,non-veg,egg,drink,halal,other')]
    public string $itemType = 'veg';

    #[Validate('required|numeric|min:0')]
    public string $itemPrice = '';

    #[Validate('nullable|integer|min:0')]
    public ?int $preparationTime = null;

    #[Validate('required|boolean')]
    public bool $isAvailable = true;

    #[Validate('nullable|string')]
    public ?string $kitchenType = null;

    #[Validate('required|boolean')]
    public bool $showOnCustomerSite = true;

    #[Validate('required|boolean')]
    public bool $inStock = true;

    #[Validate('nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048')]
    public $itemImageTemp;

    public ?string $itemImage = null;

    // Translation Properties
    public array $translationNames = [];
    public array $translationDescriptions = [];
    public array $originalTranslations = [];
    public string $currentLanguage = '';
    public array $languages = [];
    public string $globalLocale = '';

    // Variation Properties - Using indexed arrays
    public array $inputs = [];
    public int $i = 0;
    public bool $hasVariations = false;
    public bool $showItemPrice = true;
    public array $variationName = [];
    public array $variationPrice = [];
    public array $variationIds = [];

    // Pricing Properties
    public array $orderTypePrices = [];
    public array $deliveryPrices = [];
    public array $platformAvailability = [];
    public string $baseDeliveryPrice = '';
    public array $variationOrderTypePrices = []; // Structure: [index => [orderTypeId => price]]
    public array $variationPlatformAvailability = []; // Structure: [index => [appId => bool]]
    public array $variationBaseDeliveryPrice = []; // Structure: [index => price]
    public array $variationDeliveryPrices = []; // Structure: [index => [appId => calculated_price]]

    // Tax Properties
    public array $selectedTaxes = [];
    public bool $taxInclusive = false;
    public ?array $taxInclusivePriceDetails = null;
    public bool $isTaxModeItem = false;
    public array $variationBreakdowns = [];

    // Modal Properties
    public bool $showMenuCategoryModal = false;

    // Collections (computed properties to avoid N+1 queries)
    public $categoryList;
    public $menus;
    public $kitchenTypes;
    public $taxes;
    public $orderTypes;
    public $deliveryApps;
    public $menuItemId;

    public function mount(): void
    {
        $this->menuItem = MenuItem::withoutGlobalScope(AvailableMenuItemScope::class)
            ->with(['translations', 'variations', 'prices', 'taxes'])
            ->findOrFail($this->menuItemId);

        $this->initializeCollections();
        $this->initializeLanguages();
        $this->loadMenuItemData();
        $this->initializePricing();
        $this->initializeTaxSettings();
    }

    /**
     * Initialize all database collections
     */
    private function initializeCollections(): void
    {
        $this->categoryList = ItemCategory::all();
        $this->menus = Menu::all();
        $this->kitchenTypes = KotPlace::where('is_active', true)->get();
        $this->taxes = Tax::where('restaurant_id', restaurant()->id)->get();
        $this->orderTypes = OrderType::where('is_active', 1)->get();
        $this->deliveryApps = DeliveryPlatform::where('is_active', 1)->get();
    }

    /**
     * Initialize language settings and arrays
     */
    private function initializeLanguages(): void
    {
        $this->languages = languages()->pluck('language_name', 'language_code')->toArray();
        $this->translationNames = array_fill_keys(array_keys($this->languages), '');
        $this->translationDescriptions = array_fill_keys(array_keys($this->languages), '');
        $this->globalLocale = global_setting()->locale;
        $this->currentLanguage = $this->globalLocale;
    }

    /**
     * Load existing menu item data
     */
    private function loadMenuItemData(): void
    {
        // Load basic data
        $this->menu = (string)$this->menuItem->menu_id;
        $this->itemCategory = (string)$this->menuItem->item_category_id;
        $this->itemPrice = (string)$this->menuItem->price;
        $this->preparationTime = $this->menuItem->preparation_time;
        $this->itemType = $this->menuItem->type;
        $this->isAvailable = (bool)$this->menuItem->is_available;
        $this->inStock = (bool)$this->menuItem->in_stock;
        $this->kitchenType = $this->menuItem->kot_place_id ? (string)$this->menuItem->kot_place_id : null;
        $this->showOnCustomerSite = (bool)$this->menuItem->show_on_customer_site;
        $this->itemImage = $this->menuItem->image;

        // Load translations
        foreach ($this->menuItem->translations as $translation) {
            $this->translationNames[$translation->locale] = $translation->item_name;
            $this->translationDescriptions[$translation->locale] = $translation->description;
            $this->originalTranslations[$translation->locale] = [
                'item_name' => $translation->item_name,
                'description' => $translation->description
            ];
        }

        $this->translationNames[$this->globalLocale] = $this->menuItem->item_name;
        $this->translationDescriptions[$this->globalLocale] = $this->menuItem->description;

        // Load variations
        $this->hasVariations = $this->menuItem->variations->count() > 0;
        $this->showItemPrice = !$this->hasVariations;

        if ($this->hasVariations) {
            foreach ($this->menuItem->variations as $key => $variation) {
                $this->variationName[$key] = $variation->variation;
                $this->variationPrice[$key] = (string)$variation->price;
                $this->variationIds[$key] = $variation->id;
                $this->inputs[] = $key;
                $this->i = $key + 1;

                // Load variation pricing
                $this->loadVariationPricing($key, $variation->id);
            }
        }

        $this->updatedCurrentLanguage();
        $this->updateTranslation();
    }

    /**
     * Initialize pricing arrays and load existing prices
     */
    private function initializePricing(): void
    {
        // Initialize order type prices
        foreach ($this->orderTypes as $orderType) {
            $this->orderTypePrices[$orderType->id] = '';
        }

        // Initialize delivery platform availability
        foreach ($this->deliveryApps as $app) {
            $this->platformAvailability[$app->id] = true;
        }

        // Load existing prices if not variations
        if (!$this->hasVariations) {
            $this->loadItemPricing();
        }
    }

    /**
     * Load pricing for the main item
     */
    private function loadItemPricing(): void
    {
        $existingPrices = MenuItemPrices::where('menu_item_id', $this->menuItem->id)
            ->whereNull('menu_item_variation_id')
            ->get();

        foreach ($existingPrices as $price) {
            if ($price->delivery_app_id) {
                // Delivery platform price
                $this->deliveryPrices[$price->delivery_app_id] = number_format((float)$price->final_price, 2);
                $this->platformAvailability[$price->delivery_app_id] = (bool)$price->status;
            } else {
                // Order type price
                $this->orderTypePrices[$price->order_type_id] = (string)$price->final_price;

                // Check if this is delivery order type to set base delivery price
                $orderType = $this->orderTypes->firstWhere('id', $price->order_type_id);
                if ($orderType && strtolower($orderType->slug ?? $orderType->name) === 'delivery') {
                    $this->baseDeliveryPrice = (string)$price->calculated_price;
                }
            }
        }

        if ($existingPrices->count() === 0) {
            $this->baseDeliveryPrice = $this->itemPrice;
            foreach ($this->deliveryApps as $app) {
                $this->deliveryPrices[$app->id] = $this->itemPrice;
                $this->platformAvailability[$app->id] = true;
            }

            foreach ($this->orderTypes as $orderType) {
                $this->orderTypePrices[$orderType->id] = (string)$this->itemPrice;
            }

        }

        $this->calculateDeliveryPrices();
    }

    /**
     * Load pricing for a variation
     */
    private function loadVariationPricing(int $index, int $variationId): void
    {
        $existingPrices = MenuItemPrices::where('menu_item_id', $this->menuItem->id)
            ->where('menu_item_variation_id', $variationId)
            ->get();

        // Initialize arrays
        $this->variationOrderTypePrices[$index] = [];
        $this->variationPlatformAvailability[$index] = [];
        $this->variationDeliveryPrices[$index] = [];

        // Initialize order type prices with empty values
        foreach ($this->orderTypes as $orderType) {
            $this->variationOrderTypePrices[$index][$orderType->id] = '';
        }

        // Initialize delivery platform availability
        // Only set defaults for platforms that don't have existing prices
        // This avoids unnecessary overwrites
        $existingPlatformIds = $existingPrices->where('delivery_app_id', '!=', null)
            ->pluck('delivery_app_id')
            ->toArray();

        foreach ($this->deliveryApps as $app) {
            // Only set default if no existing price record
            if (!in_array($app->id, $existingPlatformIds)) {
                $this->variationPlatformAvailability[$index][$app->id] = true;
            }
        }

        // Load existing prices (this will overwrite defaults where needed)
        foreach ($existingPrices as $price) {
            if ($price->delivery_app_id) {
                // Delivery platform price
                $this->variationDeliveryPrices[$index][$price->delivery_app_id] = number_format((float)$price->final_price, 2);
                $this->variationPlatformAvailability[$index][$price->delivery_app_id] = (bool)$price->status;
            } else {
                // Order type price
                $this->variationOrderTypePrices[$index][$price->order_type_id] = (string)$price->final_price;

                // Check if this is delivery order type to set base delivery price
                $orderType = $this->orderTypes->firstWhere('id', $price->order_type_id);
                if ($orderType && strtolower($orderType->slug ?? $orderType->name) === 'delivery') {
                    $this->variationBaseDeliveryPrice[$index] = (string)$price->calculated_price;
                }
            }
        }

        // Calculate delivery prices for display
        $this->calculateVariationDeliveryPrices($index);
    }

    /**
     * Initialize tax settings
     */
    private function initializeTaxSettings(): void
    {
        $this->isTaxModeItem = (restaurant()->tax_mode === 'item');
        $this->selectedTaxes = $this->menuItem->taxes->pluck('id')->toArray();
        $this->taxInclusive = (bool)($this->menuItem->tax_inclusive ?? restaurant()->tax_inclusive ?? false);

        // Calculate tax breakdown for initial display
        if ($this->hasVariations) {
            $this->variationBreakdowns = $this->getVariationBreakdowns();
            $this->taxInclusivePriceDetails = null;
        } else {
            $this->taxInclusivePriceDetails = $this->getTaxInclusivePriceDetailsProperty();
            $this->variationBreakdowns = [];
        }
    }

    // VARIATION MANAGEMENT
    public function addMoreField(int $i): void
    {
        $i = $i + 1;
        $this->i = $i;
        $this->inputs[] = $i;

        if (count($this->inputs) > 0) {
            $this->showItemPrice = false;
        }

        // Explicitly set that this new variation has NO existing ID
        // This is critical to differentiate new variations from existing ones
        $this->variationIds[$i] = null;

        // Initialize variation name and price as empty
        $this->variationName[$i] = '';
        $this->variationPrice[$i] = '';

        // Initialize pricing for new variation
        $this->initializeVariationPricing($i);
    }

    public function removeField(int $i): void
    {
        unset($this->inputs[$i]);
        unset($this->variationName[$i]);
        unset($this->variationPrice[$i]);
        unset($this->variationIds[$i]);
        unset($this->variationOrderTypePrices[$i]);
        unset($this->variationPlatformAvailability[$i]);
        unset($this->variationBaseDeliveryPrice[$i]);
        unset($this->variationDeliveryPrices[$i]);
        unset($this->variationBreakdowns[$i]);
    }

    private function initializeVariationPricing(int $index): void
    {
        // Initialize order type prices for this variation
        $this->variationOrderTypePrices[$index] = [];
        foreach ($this->orderTypes as $orderType) {
            $this->variationOrderTypePrices[$index][$orderType->id] = '';
        }

        // Initialize delivery platform availability
        $this->variationPlatformAvailability[$index] = [];
        foreach ($this->deliveryApps as $app) {
            $this->variationPlatformAvailability[$index][$app->id] = true;
        }

        // Initialize base delivery price
        $this->variationBaseDeliveryPrice[$index] = '';

        // Initialize delivery prices array
        $this->variationDeliveryPrices[$index] = [];
        $this->calculateVariationDeliveryPrices($index);
    }

    /**
     * Calculate delivery prices for a variation
     */
    private function calculateVariationDeliveryPrices(int $index): void
    {
        $basePrice = !empty($this->variationBaseDeliveryPrice[$index])
            ? (float)$this->variationBaseDeliveryPrice[$index]
            : (!empty($this->variationPrice[$index]) ? (float)$this->variationPrice[$index] : 0);

        $this->variationDeliveryPrices[$index] = [];

        foreach ($this->deliveryApps as $app) {
            $commission = $app->commission_value ?? 0;
            $finalPrice = $basePrice + ($basePrice * $commission / 100);
            $this->variationDeliveryPrices[$index][$app->id] = number_format($finalPrice, 2);
        }
    }

    public function updatedVariationPrice($value, $key): void
    {
        $this->calculateVariationDeliveryPrices($key);
        $this->recalculateTaxBreakdowns();
    }

    public function updatedVariationBaseDeliveryPrice($value, $key): void
    {
        $this->calculateVariationDeliveryPrices($key);
    }

    public function updatedVariationPlatformAvailability($value, $key): void
    {
        // Key format: "index.appId"
        $parts = explode('.', $key);
        if (count($parts) >= 2) {
            [$index, $appId] = $parts;
            $this->calculateVariationDeliveryPrices((int)$index);
        }
    }

    public function updatedVariationOrderTypePrices($value, $key): void
    {
        // Key format: "index.orderTypeId"
        $parts = explode('.', $key);
        if (count($parts) >= 2) {
            [$index, $orderTypeId] = $parts;
            $this->calculateVariationDeliveryPrices((int)$index);
        }
    }

    // UTILITY METHODS
    public function refreshCategories(): void
    {
        $this->categoryList = ItemCategory::all();
    }

    public function updatedHasVariations($value)
    {
        if ($value) {
            $this->showItemPrice = false;
            if (count($this->inputs) == 0) {
                $this->addMoreField($this->i);
            }
            $this->variationBreakdowns = $this->getVariationBreakdowns();
            $this->itemPrice = '0';
            $this->taxInclusivePriceDetails = null;
        } else {
            $this->showItemPrice = true;
            $this->taxInclusivePriceDetails = $this->getTaxInclusivePriceDetailsProperty();
            $this->variationBreakdowns = [];
        }
    }

    public function checkVariations(): void
    {
        if ($this->hasVariations) {
            $this->enableVariations();
        } else {
            $this->disableVariations();
        }
    }

    private function enableVariations(): void
    {
        $this->showItemPrice = false;
        if (count($this->inputs) == 0) {
            $this->addMoreField($this->i);
        }
        $this->variationBreakdowns = $this->getVariationBreakdowns();
        $this->taxInclusivePriceDetails = null;
    }

    private function disableVariations(): void
    {
        $this->showItemPrice = true;
        $this->taxInclusivePriceDetails = $this->getTaxInclusivePriceDetailsProperty();
        $this->variationBreakdowns = [];

        // If variations are now disabled, delete all old variations
        if ($this->menuItem->variations->count() > 0) {
            MenuItemVariation::where('menu_item_id', $this->menuItem->id)->delete();
        }
    }

    // FORM SUBMISSION AND VALIDATION
    public function submitForm(): void
    {
        try {
            DB::beginTransaction();

            // Check if variations are enabled but no valid variations exist
            if ($this->hasVariations) {
                $hasAtLeastOne = false;
                foreach ($this->inputs as $key => $value) {
                    if (!empty($this->variationName[$key]) && !empty($this->variationPrice[$key])) {
                        $hasAtLeastOne = true;
                        break;
                    }
                }
                if (!$hasAtLeastOne) {
                    $this->addError('variationName.0', __('validation.atLeastOneVariationRequired'));
                    return;
                }
            }

            $this->validateForm();
            $this->updateMenuItem();
            $this->handleTranslations($this->menuItem);
            $this->handleImageUpload($this->menuItem);
            $this->handleVariationsOrPricing($this->menuItem);
            $this->handleTaxes($this->menuItem);

            DB::commit();

            $this->handleSuccessfulSubmission();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', __('messages.somethingWentWrong') . ': ' . $e->getMessage());
        }
    }

    private function validateForm(): void
    {
        $rules = [
            'translationNames.' . $this->globalLocale => 'required',
            'baseDeliveryPrice' => 'nullable|numeric|min:0',
            'itemCategory' => 'required',
            'menu' => 'required',
            'isAvailable' => 'required|boolean',
            'showOnCustomerSite' => 'required|boolean',
            'platformAvailability.*' => 'nullable|boolean',
        ];

        // Add validation for variations if hasVariations is true
        if ($this->hasVariations) {
            foreach ($this->inputs as $key => $value) {
                if (isset($this->variationName[$key]) || isset($this->variationPrice[$key])) {
                    $rules['variationName.' . $key] = 'required';
                    $rules['variationPrice.' . $key] = 'required|numeric|min:0';
                }
            }
        } else {
            // Validate order type prices for non-variation items
            foreach ($this->orderTypes as $orderType) {
                $isDelivery = strtolower($orderType->slug ?? $orderType->name) === 'delivery';
                // If it's the delivery order type and marked as default, make price nullable (not required)
                if ($isDelivery && !empty($orderType->is_default)) {
                    $rules['orderTypePrices.' . $orderType->id] = 'nullable|numeric|min:0';
                } else {
                    $rules['orderTypePrices.' . $orderType->id] = 'required|numeric|min:0';
                }
            }
            $rules['itemPrice'] = 'required|numeric|min:0';
        }

        // Validate image if present
        if ($this->itemImageTemp) {
            $this->validateImage();
        }

        $this->validate($rules, $this->getValidationMessages());
    }

    private function getValidationMessages(): array
    {
        $messages = [
            // Item name validation
            'translationNames.' . $this->globalLocale . '.required' => __('validation.itemNameRequired', [
                'language' => $this->languages[$this->globalLocale]
            ]),

            // Base delivery price validation
            'baseDeliveryPrice.numeric' => __('validation.baseDeliveryPriceMustBeNumeric'),
            'baseDeliveryPrice.min' => __('validation.baseDeliveryPriceMustBePositive'),

            // Item price validation (for non-variation items)
            'itemPrice.required' => __('validation.itemPriceRequired'),
            'itemPrice.numeric' => __('validation.itemPriceMustBeNumeric'),
            'itemPrice.min' => __('validation.itemPriceMustBePositive'),

            // Category and menu validation
            'itemCategory.required' => __('validation.categoryRequired'),
            'menu.required' => __('validation.menuRequired'),

            // Boolean validations
            'isAvailable.required' => __('validation.availabilityRequired'),
            'isAvailable.boolean' => __('validation.availabilityMustBeBoolean'),
            'showOnCustomerSite.required' => __('validation.showOnCustomerSiteRequired'),
            'showOnCustomerSite.boolean' => __('validation.showOnCustomerSiteMustBeBoolean'),
        ];

        // Add validation messages for order type prices (non-variation)
        if (!$this->hasVariations) {
            foreach ($this->orderTypes as $orderType) {
                $messages['orderTypePrices.' . $orderType->id . '.required'] = __('validation.orderTypePriceRequired', [
                    'orderType' => $orderType->order_type_name
                ]);
                $messages['orderTypePrices.' . $orderType->id . '.numeric'] = __('validation.priceMustBeNumeric');
                $messages['orderTypePrices.' . $orderType->id . '.min'] = __('validation.priceMinZero');
            }
        }

        // Add variation-specific validation messages
        if ($this->hasVariations) {
            foreach ($this->inputs as $key => $value) {
                // Use $value as the actual index
                $messages['variationName.' . $value . '.required'] = __('validation.variationNameRequired');
                $messages['variationName.' . $value . '.string'] = __('validation.variationNameMustBeString');
                $messages['variationName.' . $value . '.max'] = __('validation.variationNameMaxLength');
                $messages['variationPrice.' . $value . '.required'] = __('validation.variationPriceRequired');
                $messages['variationPrice.' . $value . '.numeric'] = __('validation.variationPriceMustBeNumeric');
                $messages['variationPrice.' . $value . '.min'] = __('validation.variationPriceMustBePositive');
                $messages['variationOrderTypePrices.' . $value . '.*.numeric'] = __('validation.priceMustBeNumeric');
                $messages['variationOrderTypePrices.' . $value . '.*.min'] = __('validation.priceMinZero');
                $messages['variationBaseDeliveryPrice.' . $value . '.numeric'] = __('validation.baseDeliveryPriceMustBeNumeric');
                $messages['variationBaseDeliveryPrice.' . $value . '.min'] = __('validation.baseDeliveryPriceMustBePositive');
            }
        }

        return $messages;
    }

    private function updateMenuItem(): void
    {
        $updateData = [
            'item_name' => $this->translationNames[$this->globalLocale],
            'price' => (!$this->hasVariations) ? $this->itemPrice : 0,
            'item_category_id' => $this->itemCategory,
            'description' => $this->translationDescriptions[$this->globalLocale],
            'type' => $this->itemType,
            'preparation_time' => $this->preparationTime,
            'menu_id' => $this->menu,
            'is_available' => $this->isAvailable,
            'kot_place_id' => $this->kitchenType,
            'show_on_customer_site' => $this->showOnCustomerSite,
            'tax_inclusive' => $this->isTaxModeItem ? $this->taxInclusive : (restaurant()->tax_inclusive ?? false),
        ];

        // Add inStock only if Inventory module is enabled
        if (in_array('Inventory', restaurant_modules())) {
            $updateData['in_stock'] = $this->inStock;
        }

        MenuItem::withoutGlobalScope(AvailableMenuItemScope::class)
            ->where('id', $this->menuItem->id)
            ->update($updateData);

        // Refresh the model to get updated data
        $this->menuItem->refresh();
    }

    private function handleTranslations(MenuItem $menuItem): void
    {
        // Efficiently update translations - only update what has changed
        foreach ($this->translationNames as $locale => $name) {
            $description = $this->translationDescriptions[$locale];

            // Skip empty translations
            if (empty($name) && empty($description)) {
                continue;
            }

            $isNew = !isset($this->originalTranslations[$locale]);
            $hasChanged = $isNew ||
                $this->originalTranslations[$locale]['item_name'] !== $name ||
                $this->originalTranslations[$locale]['description'] !== $description;

            if ($hasChanged) {
                if ($isNew) {
                    // Create new translation
                    $menuItem->translations()->create([
                        'locale' => $locale,
                        'item_name' => $name,
                        'description' => $description
                    ]);
                } else {
                    // Update existing translation
                    $menuItem->translations()
                        ->where('locale', $locale)
                        ->update([
                            'item_name' => $name,
                            'description' => $description
                        ]);
                }
            }
        }
    }

    private function handleImageUpload(MenuItem $menuItem): void
    {
        if ($this->itemImageTemp) {
            $menuItem->update([
                'image' => Files::uploadLocalOrS3($this->itemImageTemp, 'item', width: 350, height: 350),
            ]);
        }
    }

    private function handleVariationsOrPricing(MenuItem $menuItem): void
    {
        if ($this->hasVariations) {
            $this->updateVariations($menuItem);
        } else {
            // If variations are now disabled, delete all old variations
            MenuItemVariation::where('menu_item_id', $menuItem->id)->delete();
            $this->updateItemPricing($menuItem);
        }
    }

    private function updateVariations(MenuItem $menuItem): void
    {
        $existingVariationIds = $menuItem->variations()->pluck('id')->toArray();
        $submittedVariationIds = [];

        foreach ($this->inputs as $key => $value) {
            // Check if variation data exists and is not empty
            if (
                isset($this->variationName[$key]) && isset($this->variationPrice[$key]) &&
                !empty(trim($this->variationName[$key])) && !empty(trim($this->variationPrice[$key]))
            ) {
                $variationData = [
                    'variation' => trim($this->variationName[$key]),
                    'price' => $this->variationPrice[$key],
                    'menu_item_id' => $menuItem->id
                ];

                // Check if this is an existing variation (has ID) or a new one
                if (isset($this->variationIds[$key]) && !empty($this->variationIds[$key])) {
                    // Update existing variation
                    MenuItemVariation::where('id', $this->variationIds[$key])->update($variationData);
                    $submittedVariationIds[] = $this->variationIds[$key];

                    // Refresh pricing for this variation
                    MenuItemPrices::where('menu_item_id', $menuItem->id)
                        ->where('menu_item_variation_id', $this->variationIds[$key])
                        ->delete();

                    $this->savePricingData($menuItem->id, $this->variationIds[$key], $key);
                } else {
                    // Create new variation
                    $newVariation = MenuItemVariation::create($variationData);
                    $submittedVariationIds[] = $newVariation->id;

                    // Save pricing data for new variation
                    $this->savePricingData($menuItem->id, $newVariation->id, $key);
                }
            }
        }

        // Delete variations that were removed (not in submitted list)
        $variationsToDelete = array_diff($existingVariationIds, $submittedVariationIds);
        if (!empty($variationsToDelete)) {
            MenuItemVariation::whereIn('id', $variationsToDelete)->delete();
        }
    }

    private function updateItemPricing(MenuItem $menuItem): void
    {
        // Delete existing item prices (not variation prices)
        MenuItemPrices::where('menu_item_id', $menuItem->id)
            ->whereNull('menu_item_variation_id')
            ->delete();

        // Save new pricing data
        $this->saveItemPricingData($menuItem->id);
    }

    private function handleTaxes(MenuItem $menuItem): void
    {
        // Attach taxes if tax_mode is 'item'
        if ($this->isTaxModeItem && !empty($this->selectedTaxes)) {
            $menuItem->taxes()->sync($this->selectedTaxes);
        }
    }

    private function handleSuccessfulSubmission(): void
    {
        $this->clearTranslationCache();
        $this->dispatch('hideUpdateMenuItem');
        $this->dispatch('menuItemUpdated');
        $this->dispatch('refreshCategories');

        $this->redirect(route('menu-items.index'), true);
        $this->alert('success', __('messages.menuItemUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    private function clearTranslationCache(): void
    {
        foreach (array_keys($this->languages) as $locale) {
            cache()->forget("menu_item_{$this->menuItem->id}_item_name_{$locale}");
            cache()->forget("menu_item_{$this->menuItem->id}_description_{$locale}");
        }
    }

    public function resetForm(): void
    {
        $this->itemName = '';
        $this->menu = '';
        $this->translationNames = array_fill_keys(array_keys($this->languages), '');
        $this->translationDescriptions = array_fill_keys(array_keys($this->languages), '');
        $this->originalTranslations = [];
        $this->itemCategory = '';
        $this->itemPrice = '';
        $this->itemDescription = '';
        $this->itemType = 'veg';
        $this->itemImage = null;
        $this->itemImageTemp = null;
        $this->preparationTime = null;
        $this->variationName = [];
        $this->variationPrice = [];
        $this->variationIds = [];
        $this->variationBreakdowns = [];
        $this->taxInclusivePriceDetails = null;
        $this->orderTypePrices = [];
        $this->deliveryPrices = [];
        $this->platformAvailability = [];
        $this->baseDeliveryPrice = '';
        $this->variationOrderTypePrices = [];
        $this->variationPlatformAvailability = [];
        $this->variationBaseDeliveryPrice = [];
        $this->variationDeliveryPrices = [];
    }

    public function updateTranslation(): void
    {
        $this->translationNames[$this->currentLanguage] = $this->itemName;
        $this->translationDescriptions[$this->currentLanguage] = $this->itemDescription;
    }

    public function updatedCurrentLanguage(): void
    {
        $this->itemName = $this->translationNames[$this->currentLanguage];
        $this->itemDescription = $this->translationDescriptions[$this->currentLanguage];
    }

    public function showMenuCategoryModal(): void
    {
        $this->dispatch('showMenuCategoryModal');
    }

    public function updatedTaxInclusive(): void
    {
        $this->recalculateTaxBreakdowns();
    }

    public function updatedItemPrice(): void
    {
        $this->calculateDeliveryPrices();
        $this->recalculateTaxBreakdowns();
    }

    public function updatedSelectedTaxes(): void
    {
        $this->recalculateTaxBreakdowns();
    }

    private function recalculateTaxBreakdowns(): void
    {
        if ($this->hasVariations) {
            $this->variationBreakdowns = $this->getVariationBreakdowns();
            $this->taxInclusivePriceDetails = null;
        } else {
            $this->taxInclusivePriceDetails = $this->getTaxInclusivePriceDetailsProperty();
            $this->variationBreakdowns = [];
        }
    }

    public function updatedItemImageTemp(): void
    {
        $this->itemImage = null;
        $this->validateImage();
    }

    public function removeSelectedImage(): void
    {
        $this->itemImageTemp = null;
        $this->itemImage = null;
    }

    public function validateImage(): void
    {
        if (!$this->itemImageTemp) return;

        $this->validate([
            'itemImageTemp' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Check image dimensions
        $imageInfo = getimagesize($this->itemImageTemp->getRealPath());
        if ($imageInfo) {
            $width = $imageInfo[0];
            $height = $imageInfo[1];

            // Recommend minimum dimensions
            if ($width < 200 || $height < 200) {
                $this->addError('itemImageTemp', 'Image dimensions are too small. Recommended minimum: 200x200 pixels.');
            }
        }
    }

    public function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    // TAX CALCULATIONS
    #[Computed]
    public function getTaxInclusivePriceDetailsProperty(): ?array
    {
        if (empty($this->itemPrice) || !$this->isTaxModeItem) {
            return null;
        }

        return (new MenuItem)->getTaxBreakdown(
            (float)$this->itemPrice,
            $this->selectedTaxes,
            $this->taxInclusive
        );
    }

    private function getVariationBreakdowns(): array
    {
        if (!$this->isTaxModeItem) {
            return [];
        }

        $breakdowns = [];
        foreach ($this->variationPrice as $key => $price) {
            if (!empty($price)) {
                $breakdowns[$key] = [
                    'name' => $this->variationName[$key] ?? '',
                    'breakdown' => (new MenuItem)->getTaxBreakdown(
                        (float)$price,
                        $this->selectedTaxes,
                        $this->taxInclusive
                    )
                ];
            }
        }
        return $breakdowns;
    }

    private function updateVariationBreakdowns(): void
    {
        $this->variationBreakdowns = $this->getVariationBreakdowns();
    }

    // PRICING MANAGEMENT
    public function updatedBaseDeliveryPrice(): void
    {
        $this->calculateDeliveryPrices();
    }

    public function updatedOrderTypePrices(): void
    {
        if (!$this->hasVariations) {
            // Find first non-empty price to use as base price
            // Only set baseDeliveryPrice once (don't overwrite if already set)
            $changed = false;

            if (empty($this->baseDeliveryPrice)) {
                // Use first non-empty, numeric order type price as base delivery price
                $baseFound = null;
                foreach ($this->orderTypePrices as $id => $price) {
                    if ($price !== null && $price !== '' && is_numeric($price)) {
                        $baseFound = (string)$price;
                        break;
                    }
                }

                // If no order type prices available, fallback to itemPrice (if present)
                if ($baseFound === null && !empty($this->itemPrice) && is_numeric($this->itemPrice)) {
                    $baseFound = (string)$this->itemPrice;
                }

                if ($baseFound !== null) {
                    $this->baseDeliveryPrice = $baseFound;
                    $changed = true;

                    // Also populate any empty order type prices with this base (do not overwrite existing)
                    foreach ($this->orderTypes as $orderType) {
                        $otId = $orderType->id;
                        if (!isset($this->orderTypePrices[$otId]) || $this->orderTypePrices[$otId] === '') {
                            $this->orderTypePrices[$otId] = $this->baseDeliveryPrice;
                        }
                    }
                }
            }

            // Recalculate delivery prices only if baseDeliveryPrice was set/changed
            if ($changed) {
                $this->calculateDeliveryPrices();
            }
        }
    }

    private function calculateDeliveryPrices(): void
    {
        $basePrice = !empty($this->baseDeliveryPrice)
            ? (float)$this->baseDeliveryPrice
            : (!empty($this->itemPrice) ? (float)$this->itemPrice : 0);

        foreach ($this->deliveryApps as $app) {
            $commission = $app->commission_value ?? 0;
            $finalPrice = $basePrice + ($basePrice * $commission / 100);
            $this->deliveryPrices[$app->id] = number_format($finalPrice, 2);
        }
    }

    /**
     * Save pricing data for menu item or variation
     */
    private function savePricingData(int $menuItemId, ?int $variationId = null, ?int $localIndex = null): void
    {
        if ($variationId !== null && $localIndex !== null) {
            $this->saveVariationPricingData($menuItemId, $variationId, $localIndex);
        } else {
            $this->saveItemPricingData($menuItemId);
        }
    }

    private function saveVariationPricingData(int $menuItemId, int $variationId, int $localIndex): void
    {
        if (!isset($this->variationPrice[$localIndex])) return;

        $basePrice = (float)$this->variationPrice[$localIndex];
        $orderTypePrices = $this->variationOrderTypePrices[$localIndex] ?? [];
        $baseDeliveryPrice = $this->variationBaseDeliveryPrice[$localIndex] ?? '';

        $this->createPricingRecords($menuItemId, $basePrice, $orderTypePrices, $baseDeliveryPrice, $variationId, $localIndex);
    }

    private function saveItemPricingData(int $menuItemId): void
    {
        $basePrice = (float)$this->itemPrice;
        $this->createPricingRecords($menuItemId, $basePrice, $this->orderTypePrices, $this->baseDeliveryPrice);
    }

    private function createPricingRecords(
        int $menuItemId,
        float $basePrice,
        array $orderTypePrices,
        string $baseDeliveryPrice,
        ?int $variationId = null,
        ?int $localIndex = null
    ): void {
        // Save order type pricing (excluding delivery)
        foreach ($this->orderTypes as $orderType) {

            $orderTypePrice = !empty($orderTypePrices[$orderType->id]) ? (float)$orderTypePrices[$orderType->id] : $basePrice;

            if (strtolower($orderType->slug ?? $orderType->name) === 'delivery') {
                $deliveryBase = !empty($baseDeliveryPrice) ? (float)$baseDeliveryPrice : $basePrice;
                $orderTypePrice = $deliveryBase;
            }

            MenuItemPrices::create([
                'menu_item_id' => $menuItemId,
                'order_type_id' => $orderType->id,
                'delivery_app_id' => null,
                'menu_item_variation_id' => $variationId,
                'calculated_price' => $orderTypePrice,
                'override_price' => null,
                'final_price' => $orderTypePrice,
                'status' => true,
            ]);
        }

        // Save delivery platform pricing
        $this->saveDeliveryPlatformPricing($menuItemId, $basePrice, $variationId, $baseDeliveryPrice, $localIndex);
    }

    private function saveDeliveryPlatformPricing(int $menuItemId, float $basePrice, ?int $variationId = null, string $baseDeliveryPrice = '', ?int $localIndex = null): void
    {
        $deliveryOrderType = $this->orderTypes->where('slug', 'delivery')->first();

        if (!$deliveryOrderType) return;

        foreach ($this->deliveryApps as $app) {
            // Determine availability - for variations, check variation-specific availability
            // Default to TRUE if not explicitly set to false
            $isAvailable = true;

            if ($localIndex !== null) {
                // For variations - check if the platform is available (defaults to true)
                $isAvailable = isset($this->variationPlatformAvailability[$localIndex][$app->id])
                    ? (bool)$this->variationPlatformAvailability[$localIndex][$app->id]
                    : true;
            } else {
                // For regular items - check if the platform is available (defaults to true)
                $isAvailable = isset($this->platformAvailability[$app->id])
                    ? (bool)$this->platformAvailability[$app->id]
                    : true;
            }

            // Get the base delivery price for calculation
            $deliveryBase = $basePrice; // Default to variation/item price

            if ($localIndex !== null) {
                // For variations, check if base delivery price is set
                if (!empty($this->variationBaseDeliveryPrice[$localIndex])) {
                    $deliveryBase = (float)$this->variationBaseDeliveryPrice[$localIndex];
                }
            } else {
                // For regular items
                if (!empty($baseDeliveryPrice)) {
                    $deliveryBase = (float)$baseDeliveryPrice;
                } elseif (!empty($this->baseDeliveryPrice)) {
                    $deliveryBase = (float)$this->baseDeliveryPrice;
                }
            }

            // Calculate final price with commission
            $commission = (float)($app->commission_value ?? 0);
            $calculatedPrice = $deliveryBase + ($deliveryBase * $commission / 100);

            MenuItemPrices::create([
                'menu_item_id' => $menuItemId,
                'order_type_id' => $deliveryOrderType->id,
                'delivery_app_id' => $app->id,
                'menu_item_variation_id' => $variationId,
                'calculated_price' => $deliveryBase,
                'override_price' => null,
                'final_price' => $calculatedPrice,
                'status' => $isAvailable, // Save the toggle state
            ]);
        }
    }

    public function render()
    {
        return view('livewire.forms.update-menu-item');
    }
}
