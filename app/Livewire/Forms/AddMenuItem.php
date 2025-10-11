<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use App\Models\ItemCategory;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemVariation;
use App\Models\KotPlace;
use App\Models\Tax;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddMenuItem extends Component
{

    use WithFileUploads, LivewireAlert;

    protected $listeners = ['refreshCategories'];

    public $inputs = [];
    public $i = 0;
    public $showItemPrice = true;
    public $showMenuCategoryModal = false;
    public $hasVariations = false;
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
    public $preparationTime;
    public bool $isAvailable = true;
    public $translationNames = [];
    public $translationDescriptions = [];
    public $currentLanguage;
    public $languages = [];
    public $globalLocale;
    public $kitchenTypes;
    public $kitchenType;
    public $taxes = [];
    public $selectedTaxes = [];
    public $taxInclusive = false;
    public $taxInclusivePriceDetails = null;
    public $isTaxModeItem = false;
    public $variationBreakdowns = [];

    public function mount()
    {
        $this->languages = languages()->pluck('language_name', 'language_code')->toArray();
        $this->translationNames = array_fill_keys(array_keys($this->languages), '');
        $this->translationDescriptions = array_fill_keys(array_keys($this->languages), '');
        $this->globalLocale = global_setting()->locale;
        $this->currentLanguage = $this->globalLocale;
        $this->categoryList = ItemCategory::all();
        $this->menus = Menu::all();
        $this->kitchenTypes = KotPlace::where('is_active', true)->get();

        $this->taxes = Tax::all();
        $this->taxInclusive = (bool)(restaurant()->tax_inclusive ?? false);
        $this->isTaxModeItem = (restaurant()->tax_mode === 'item');
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
        // Reindex all arrays so keys match
        $this->inputs = array_values($this->inputs);
        $this->variationName = array_values($this->variationName);
        $this->variationPrice = array_values($this->variationPrice);
    }

    public function cleanupEmptyVariations()
    {
        // Remove any variations that have empty names or prices
        foreach ($this->variationName as $key => $value) {
            if (empty($value) || empty($this->variationPrice[$key])) {
                unset($this->inputs[$key]);
                unset($this->variationName[$key]);
                unset($this->variationPrice[$key]);
            }
        }
        // Reindex arrays
        $this->inputs = array_values($this->inputs);
        $this->variationName = array_values($this->variationName);
        $this->variationPrice = array_values($this->variationPrice);
    }

    public function checkVariations()
    {
        if ($this->hasVariations) {
            $this->showItemPrice = false;
            $this->itemPrice = 0;
            $this->taxInclusivePriceDetails = null;
            $this->variationBreakdowns = $this->getVariationBreakdowns();
            if (count($this->inputs) == 0 && $this->hasVariations) {
                $this->addMoreField($this->i);
            }
        } else {
            $this->showItemPrice = true;
            // Clear variations when switching back to single price
            $this->variationName = [];
            $this->variationPrice = [];
            $this->inputs = [];
            $this->i = 0;
        }
    }

    public function refreshCategories()
    {
        $this->categoryList = ItemCategory::all();
    }

    public function submitForm()
    {
        // Clean up any empty variations before validation
        $this->cleanupEmptyVariations();

        // Check if variations are enabled but no valid variations exist
        if ($this->hasVariations && empty($this->variationName)) {
            $this->addError('variationName.0', __('validation.atLeastOneVariationRequired'));
            return;
        }

        // Validate image if present
        if ($this->itemImageTemp) {
            $this->validateImage();
        }

        $this->validate([
            'translationNames.' . $this->globalLocale => 'required',
            'itemPrice' => 'required_if:hasVariations,false',
            'itemCategory' => 'required',
            'menu' => 'required',
            'isAvailable' => 'required|boolean',
        ], [
            'translationNames.' . $this->globalLocale . '.required' => __('validation.itemNameRequired', ['language' => $this->languages[$this->globalLocale]]),
        ]);


        $menuItem = MenuItem::create([
            'item_name' => $this->translationNames[$this->globalLocale],
            'price' => (!$this->hasVariations) ? $this->itemPrice : 0,
            'item_category_id' => $this->itemCategory,
            'description' => $this->translationDescriptions[$this->globalLocale],
            'is_available' => (bool)$this->isAvailable,
            'type' => $this->itemType,
            'menu_id' => $this->menu,
            'preparation_time' => $this->preparationTime,
            'kot_place_id' => $this->kitchenType,
            'tax_inclusive' => ($this->isTaxModeItem) ? $this->taxInclusive : false,
        ]);

        $translations = collect($this->translationNames)
            ->filter(fn($name, $locale) => !empty($name) || !empty($this->translationDescriptions[$locale]))
            ->map(fn($name, $locale) => [
                'locale' => $locale,
                'item_name' => $name,
                'description' => $this->translationDescriptions[$locale]
            ])->values()->all();

        $menuItem->translations()->createMany($translations);

        if ($this->itemImageTemp) {
            $menuItem->update([
                'image' => Files::uploadLocalOrS3($this->itemImageTemp, 'item', width: 350),
            ]);
        }

        if ($this->hasVariations) {
            // Ensure we have at least one valid variation
            $validVariations = 0;
            foreach ($this->variationName as $key => $value) {
                if (!empty($value) && isset($this->variationPrice[$key]) && !empty($this->variationPrice[$key])) {
                    $validVariations++;
                    $this->validate([
                        'variationPrice.' . $key => 'required|numeric'
                    ], [
                        'variationPrice.' . $key . '.required' => __('validation.variationPriceRequired'),
                    ]);

                    MenuItemVariation::create([
                        'variation' => $value,
                        'price' => $this->variationPrice[$key],
                        'menu_item_id' => $menuItem->id
                    ]);
                }
            }

            // If no valid variations, throw an error
            if ($validVariations === 0) {
                $this->addError('variationName.0', __('validation.atLeastOneVariationRequired'));
                return;
            }
        }

        // Attach taxes if tax_mode is 'item'
        if ($this->isTaxModeItem && !empty($this->selectedTaxes)) {
            $menuItem->taxes()->sync($this->selectedTaxes);
        }

        $this->resetForm();
        $this->dispatch('hideAddMenuItem');
        $this->dispatch('menuItemAdded');
        $this->dispatch('refreshCategories');

        $this->alert('success', __('messages.menuItemAdded'), [
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
        $this->itemCategory = '';
        $this->itemPrice = '';
        $this->itemDescription = '';
        $this->itemType = 'veg';
        $this->itemImage = null;
        $this->itemImageTemp = null;
        $this->preparationTime = null;
        $this->variationName = [];
        $this->variationPrice = [];
        $this->variationBreakdowns = [];
        $this->taxInclusivePriceDetails = null;
        $this->inputs = [];
        $this->i = 0;
        $this->showItemPrice = true;
        $this->hasVariations = false;
        $this->selectedTaxes = [];
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
            $this->taxInclusivePriceDetails = null;
        } else {
            $this->taxInclusivePriceDetails = $this->getTaxInclusivePriceDetailsProperty();
            $this->variationBreakdowns = [];
        }
    }

    public function updatedItemPrice()
    {
        if ($this->hasVariations) {
            $this->variationBreakdowns = $this->getVariationBreakdowns();
            $this->taxInclusivePriceDetails = null;
        } else {
            $this->taxInclusivePriceDetails = $this->getTaxInclusivePriceDetailsProperty();
            $this->variationBreakdowns = [];
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

    public function updatedSelectedTaxes()
    {
        if ($this->hasVariations) {
            $this->variationBreakdowns = $this->getVariationBreakdowns();
            $this->taxInclusivePriceDetails = null;
        } else {
            $this->taxInclusivePriceDetails = $this->getTaxInclusivePriceDetailsProperty();
            $this->variationBreakdowns = [];
        }
    }

    public function getTaxInclusivePriceDetailsProperty()
    {
        return (new \App\Models\MenuItem)->getTaxBreakdown(
            $this->itemPrice,
            $this->selectedTaxes,
            $this->taxInclusive
        );
    }

    public function updatedVariationPrice($value, $key)
    {
        if ($this->hasVariations) {
            $this->itemPrice = 0;
        }
        // Also update breakdowns
        $this->variationBreakdowns = $this->getVariationBreakdowns();
        $this->taxInclusivePriceDetails = null;
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
        return view('livewire.forms.add-menu-item');
    }
}
