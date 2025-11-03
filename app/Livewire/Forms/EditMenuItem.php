<?php

namespace App\Livewire\Forms;

use App\Models\Menu;
use App\Helper\Files;
use Livewire\Component;
use App\Models\MenuItem;
use App\Models\KotPlace;
use App\Models\ItemCategory;
use Livewire\WithFileUploads;
use App\Models\MenuItemVariation;
use App\Scopes\AvailableMenuItemScope;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Tax;

class EditMenuItem extends Component
{
    use WithFileUploads, LivewireAlert;

    protected $listeners = ['refreshCategories'];

    public $inputs = [];
    public int $i = 0;
    public bool $showItemPrice = true;
    public bool $hasVariations = false;
    public $menu;
    public $itemName;
    public $itemCategory;
    public $itemPrice;
    public $itemDescription;
    public $itemType = 'veg';
    public $itemImage;
    public $itemImageTemp; // Add temporary image property
    public $categoryList = [];
    public $menus = [];
    public $variationName = [];
    public $variationPrice = [];
    public $variationId = []; // Track variation IDs for existing variations
    public $menuItem;
    public $preparationTime;
    public $isAvailable;
    public $showMenuCategoryModal = false;
    public $translationNames = [];
    public $translationDescriptions = [];
    public $originalTranslations = [];
    public $currentLanguage;
    public $languages = [];
    public $globalLocale;
    public $kitchenTypes;
    public $kitchenType;
    public bool $showOnCustomerSite;
    public $taxes = [];
    public $selectedTaxes = [];
    public $taxInclusive = false;
    public $taxInclusivePrice = null;
    public $variationBreakdowns = []; // <-- Add this property
    public $inStock = false;

    public function mount()
    {
        $this->languages = languages()->pluck('language_name', 'language_code')->toArray();
        $this->translationNames = array_fill_keys(array_keys($this->languages), '');
        $this->translationDescriptions = array_fill_keys(array_keys($this->languages), '');
        $this->globalLocale = auth()->user()->locale;
        $this->currentLanguage = $this->globalLocale;
        $this->categoryList = ItemCategory::all();
        $this->menus = Menu::all();
        $this->menu = $this->menuItem->menu_id;
        $this->itemCategory = $this->menuItem->item_category_id;
        $this->itemPrice = $this->menuItem->price;
        $this->preparationTime = $this->menuItem->preparation_time;
        $this->itemType = $this->menuItem->type;
        $this->hasVariations = ($this->menuItem->variations->count() > 0);
        $this->showItemPrice = ($this->menuItem->variations->count() == 0);
        $this->isAvailable = $this->menuItem->is_available;
        $this->inStock = $this->menuItem->in_stock;
        $this->kitchenTypes = KotPlace::where('is_active', true)->get();
        $this->kitchenType = $this->menuItem->kot_place_id;
        $this->showOnCustomerSite = $this->menuItem->show_on_customer_site;

        foreach ($this->menuItem->translations as $translation) {
            $this->translationNames[$translation->locale] = $translation->item_name;
            $this->translationDescriptions[$translation->locale] = $translation->description;

            $this->originalTranslations[$translation->locale] = [
                'item_name' => $translation->item_name,
                'description' => $translation->description
            ];
        }

        $this->translationNames[$this->globalLocale] = $this->itemName ?: $this->menuItem->item_name;
        $this->translationDescriptions[$this->globalLocale] = $this->itemDescription ?: $this->menuItem->description;

        foreach ($this->menuItem->variations as $key => $value) {
            $this->variationName[$key] = $value->variation;
            $this->variationPrice[$key] = $value->price;
            $this->variationId[$key] = $value->id; // Track existing variation IDs
            $this->i = $key + 1;
            array_push($this->inputs, $this->i);
        }

        $this->updatedCurrentLanguage();
        $this->updateTranslation();

        $this->taxes = Tax::where('restaurant_id', restaurant()->id)->get();
        $this->selectedTaxes = $this->menuItem->taxes->pluck('id')->toArray();
        $this->taxInclusive = (bool) (restaurant()->tax_inclusive ?? false);


        // Calculate tax breakdown for initial display
        if ($this->hasVariations) {
            $this->variationBreakdowns = $this->getVariationBreakdowns();
            $this->taxInclusivePrice = null;
        } else {
            $this->taxInclusivePrice = $this->getTaxInclusivePriceProperty();
            $this->variationBreakdowns = [];
        }
    }

    public function addMoreField($i)
    {
        $i = $i + 1;
        $this->i = $i;
        array_push($this->inputs, $i);

        if (count($this->inputs) > 0) {
            $this->showItemPrice = false;
        }
    }

    public function removeField($i)
    {
        unset($this->inputs[$i]);
        unset($this->variationName[$i]);
        unset($this->variationPrice[$i]);
        unset($this->variationId[$i]); // Also remove variation ID
        unset($this->variationBreakdowns[$i]);
    }

    public function updatedHasVariations($value)
    {
        if ($value) {
            $this->showItemPrice = false;
            if (count($this->inputs) == 0) {
                $this->addMoreField($this->i);
            }
            $this->variationBreakdowns = $this->getVariationBreakdowns();
            $this->itemPrice = 0;
            $this->taxInclusivePrice = null;
        } else {
            $this->showItemPrice = true;
            $this->taxInclusivePrice = $this->getTaxInclusivePriceProperty();
            $this->variationBreakdowns = [];
        }
    }

    public function refreshCategories()
    {
        $this->categoryList = ItemCategory::all();
    }

    public function validateImage()
    {
        if ($this->itemImageTemp) {
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
    }

    public function updatedItemImageTemp()
    {
        // Clear any existing image when a new one is selected
        $this->itemImage = null;
    }

    public function removeSelectedImage()
    {
        $this->itemImageTemp = null;
        $this->itemImage = null;
    }

    public function formatFileSize($bytes)
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

    public function submitForm()
    {
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

        // Validate image if present
        if ($this->itemImageTemp) {
            $this->validateImage();
        }

        $rules = [
            'translationNames.' . $this->globalLocale => 'required',
            'itemPrice' => 'required_if:hasVariations,false',
            'itemCategory' => 'required',
            'menu' => 'required',
            'isAvailable' => 'required|boolean',
            'showOnCustomerSite' => 'required|boolean',
        ];

        // Add validation for variations if hasVariations is true
        if ($this->hasVariations) {
            foreach ($this->inputs as $key => $value) {
                if (isset($this->variationName[$key]) || isset($this->variationPrice[$key])) {
                    $rules['variationName.' . $key] = 'required';
                    $rules['variationPrice.' . $key] = 'required|numeric|min:0';
                }
            }
        }

        $this->validate($rules, [
            'translationNames.' . $this->globalLocale . '.required' => __('validation.itemNameRequired', ['language' => $this->languages[$this->globalLocale]]),
        ]);


        MenuItem::withoutGlobalScope(AvailableMenuItemScope::class)->where('id', $this->menuItem->id)->update([
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
            'tax_inclusive' => (restaurant()->tax_mode === 'item') ? $this->taxInclusive : (restaurant()->tax_inclusive ?? false),
        ]);

        if (in_array('Inventory', restaurant_modules())) {
            MenuItem::withoutGlobalScope(AvailableMenuItemScope::class)->where('id', $this->menuItem->id)->update([
                'in_stock' => $this->inStock,
            ]);
        }

        // Sync taxes if tax_mode is 'item'
        if (restaurant()->tax_mode === 'item') {
            $this->menuItem->taxes()->sync($this->selectedTaxes);
        }

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
                    $this->menuItem->translations()->create([
                        'locale' => $locale,
                        'item_name' => $name,
                        'description' => $description
                    ]);
                } else {
                    // Update existing translation
                    $this->menuItem->translations()
                        ->where('locale', $locale)
                        ->update([
                            'item_name' => $name,
                            'description' => $description
                        ]);
                }
            }
        }

        if ($this->itemImageTemp) {
            $this->menuItem->update([
                'image' => Files::uploadLocalOrS3($this->itemImageTemp, 'item', width: 350, height: 350),
            ]);
        }

        if ($this->hasVariations) {
            // Get all existing variation IDs
            $existingVariationIds = $this->menuItem->variations()->pluck('id')->toArray();
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
                        'menu_item_id' => $this->menuItem->id
                    ];

                    // Check if this is an existing variation (has ID) or a new one
                    if (isset($this->variationId[$key]) && !empty($this->variationId[$key])) {
                        // Update existing variation
                        MenuItemVariation::where('id', $this->variationId[$key])->update($variationData);
                        $submittedVariationIds[] = $this->variationId[$key];
                    } else {
                        // Create new variation
                        $newVariation = MenuItemVariation::create($variationData);
                        $submittedVariationIds[] = $newVariation->id;
                    }
                }
            }

            // Delete variations that were removed (not in submitted list)
            $variationsToDelete = array_diff($existingVariationIds, $submittedVariationIds);
            if (!empty($variationsToDelete)) {
                MenuItemVariation::whereIn('id', $variationsToDelete)->delete();
            }
        } else {
            // If variations are now disabled, delete all old variations
            MenuItemVariation::where('menu_item_id', $this->menuItem->id)->delete();
        }

        $this->dispatch('hideEditMenuItem');
        $this->resetForm();
        $this->clearTranslationCache();

        $this->alert('success', __('messages.menuItemUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function resetForm()
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
        $this->variationId = [];
        $this->variationBreakdowns = [];
        $this->taxInclusivePrice = null;
    }

    public function clearTranslationCache()
    {
        foreach (array_keys($this->languages) as $locale) {
            cache()->forget("menu_item_{$this->menuItem->id}_item_name_{$locale}");
            cache()->forget("menu_item_{$this->menuItem->id}_description_{$locale}");
        }
    }


    public function updateTranslation()
    {
        $this->translationNames[$this->currentLanguage] = $this->itemName;
        $this->translationDescriptions[$this->currentLanguage] = $this->itemDescription;
    }

    public function updatedCurrentLanguage()
    {
        $this->itemName = $this->translationNames[$this->currentLanguage];
        $this->itemDescription = $this->translationDescriptions[$this->currentLanguage];
    }

    public function showMenuCategoryModal()
    {
        $this->dispatch('showMenuCategoryModal');
    }

    public function updatedTaxInclusive()
    {
        if ($this->hasVariations) {
            $this->variationBreakdowns = $this->getVariationBreakdowns();
            $this->taxInclusivePrice = null;
        } else {
            $this->taxInclusivePrice = $this->getTaxInclusivePriceProperty();
            $this->variationBreakdowns = [];
        }
    }

    public function getTaxInclusivePriceProperty()
    {
        // Use the MenuItem model's method for tax breakdown
        return (new \App\Models\MenuItem)->getTaxBreakdown(
            $this->itemPrice,
            $this->selectedTaxes,
            $this->taxInclusive
        );
    }

    public function updatedItemPrice()
    {
        if ($this->hasVariations) {
            $this->variationBreakdowns = $this->getVariationBreakdowns();
            $this->taxInclusivePrice = null;
        } else {
            $this->taxInclusivePrice = $this->getTaxInclusivePriceProperty();
            $this->variationBreakdowns = [];
        }
    }

    public function updatedSelectedTaxes()
    {
        if ($this->hasVariations) {
            $this->variationBreakdowns = $this->getVariationBreakdowns();
            $this->taxInclusivePrice = null;
        } else {
            $this->taxInclusivePrice = $this->getTaxInclusivePriceProperty();
            $this->variationBreakdowns = [];
        }
    }

    public function updatedVariationPrice($value = null, $key = null)
    {
        if ($this->hasVariations) {
            $this->variationBreakdowns = $this->getVariationBreakdowns();
            $this->taxInclusivePrice = null;
        }
    }

    public function getVariationBreakdowns()
    {
        $breakdowns = [];
        foreach ($this->variationPrice as $key => $price) {
            if (!empty($price)) {
                $breakdowns[$key] = [
                    'name' => $this->variationName[$key] ?? '',
                    'breakdown' => (new \App\Models\MenuItem)->getTaxBreakdown(
                        $price,
                        $this->selectedTaxes,
                        $this->taxInclusive
                    )
                ];
            }
        }
        return $breakdowns;
    }

    public function render()
    {
        return view('livewire.forms.edit-menu-item');
    }
}
