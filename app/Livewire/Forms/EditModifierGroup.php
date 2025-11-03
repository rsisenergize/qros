<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\MenuItem;
use App\Models\ItemModifier;
use App\Models\ModifierGroup;
use App\Models\ModifierOption;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditModifierGroup extends Component
{
    use LivewireAlert;

    public $name;
    public $description;
    public $modifierOptions = [];
    public $modifierOptionInput = [];
    public $modifierOptionName = [];
    public $modifierGroupId;
    public $menuItems;
    public $selectedMenuItems = [];
    public $search = '';
    public $isOpen = false;
    public $allMenuItems;

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
        ];

        // Add dynamic validation rules for the global locale
        $baseRules['translationNames.' . $this->globalLocale] = 'required|max:255';
        $baseRules['modifierOptions.*.name.' . $this->globalLocale] = 'required|max:255';

        return $baseRules;
    }

    protected function messages()
    {
        return [
            'name.*.max' => 'The name may not be greater than 255 characters.',
            'modifierOptions.*.price.required' => 'Modifier option price must have a price.',
            'modifierOptions.*.price.numeric' => 'Modifier option price must be a number.',
            'modifierOptions.*.price.min' => 'Modifier option price must be at least 0.',
            'translationNames.' . $this->globalLocale . '.required' => __('validation.modifierGroupNameRequired', ['language' => $this->languages[$this->globalLocale]]),
            'modifierOptions.*.name.' . $this->globalLocale . '.required' => __('validation.modifierOptionNameRequired', ['language' => $this->languages[$this->globalLocale]]),
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

        $modifierGroup = ModifierGroup::with(['options', 'translations'])->findOrFail($id);
        $this->modifierGroupId = $modifierGroup->id;

        // Load translations
        foreach ($modifierGroup->translations as $translation) {
            $this->translationNames[$translation->locale] = $translation->name;
            $this->translationDescriptions[$translation->locale] = $translation->description;
        }

        // Set default name and description based on current language
        $this->name = $this->translationNames[$this->currentLanguage] ?: $modifierGroup->name;
        $this->description = $this->translationDescriptions[$this->currentLanguage] ?: $modifierGroup->description;

        // Load modifier options with translations using Spatie Translatable
        $this->modifierOptions = $modifierGroup->options->map(function ($option, $optIndex) {
            $this->modifierOptionInput[$optIndex] = [];

            // Use Spatie's getTranslations to get all translations as array
            $optionNames = $option->getTranslations('name');

            foreach (array_keys($this->languages) as $lang) {
                $this->modifierOptionInput[$optIndex][$lang] = $optionNames[$lang] ?? '';
            }

            $this->modifierOptionName[$optIndex] = $optionNames[$this->currentLanguage] ?? '';

            return [
                'id' => $option->id,
                'name' => $optionNames,
                'price' => $option->price,
                'is_available' => (bool) $option->is_available,
                'sort_order' => $option->sort_order,
            ];
        })->toArray();

        // Load menu items and selected menu items
        $this->menuItems = $this->allMenuItems = MenuItem::all();
        
        // Load currently associated menu items
        $this->selectedMenuItems = $modifierGroup->itemModifiers()->pluck('menu_item_id')->toArray();

        $this->updateTranslation();
        $this->syncModifierOptions();
    }

    /**
     * Check if a string is valid JSON
     */
    private function isJson($string) {
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function newModifierOption()
    {
        $langs = array_keys($this->languages);
        return [
            'id' => uniqid(),
            'name' => array_fill_keys($langs, ''),
            'price' => 0,
            'is_available' => true,
            'sort_order' => 0,
        ];
    }

    protected function syncModifierOptions()
    {
        // Sync the translations first
        $this->name = $this->translationNames[$this->currentLanguage] ?? '';
        $this->description = $this->translationDescriptions[$this->currentLanguage] ?? '';

        // Then sync modifier options
        foreach ($this->modifierOptions as $index => $option) {
            if (!isset($this->modifierOptionInput[$index])) {
                $this->modifierOptionInput[$index] = [];
            }

            // Sync inputs for all languages
            foreach (array_keys($this->languages) as $lang) {
                $this->modifierOptionInput[$index][$lang] = $option['name'][$lang] ?? '';
            }

            // Set the current language name
            $this->modifierOptionName[$index] = $this->modifierOptionInput[$index][$this->currentLanguage] ?? '';
        }
    }


    public function updateTranslation()
    {
        $this->translationNames[$this->currentLanguage] = $this->name;
        $this->translationDescriptions[$this->currentLanguage] = $this->description;
    }


    public function updateModifierOptionTranslation($index)
    {
        if (!isset($this->modifierOptionInput[$index])) {
            $this->modifierOptionInput[$index] = [];
        }

        $lang = $this->currentLanguage;
        $this->modifierOptionInput[$index][$lang] = $this->modifierOptionName[$index];
        $this->modifierOptions[$index]['name'][$lang] = $this->modifierOptionName[$index];
    }

    public function updatedModifierOptionName($value, $index)
    {
        $lang = $this->currentLanguage;
        $this->modifierOptionInput[$index][$lang] = $value;
        $this->modifierOptions[$index]['name'][$lang] = $value;
    }

    public function addModifierOption()
    {
        $this->modifierOptions[] = $this->newModifierOption();
        $this->syncModifierOptions();
    }

    public function removeModifierOption($index)
    {
        unset($this->modifierOptions[$index], $this->modifierOptionInput[$index], $this->modifierOptionName[$index]);
        $this->modifierOptions = array_values($this->modifierOptions);
        $this->modifierOptionInput = array_values($this->modifierOptionInput);
        $this->modifierOptionName = array_values($this->modifierOptionName);
    }

    public function updateModifierOptionOrder($orderedIds)
    {
        $this->modifierOptions = collect($orderedIds)->map(function ($id) {
            return collect($this->modifierOptions)->firstWhere('id', $id['value']);
        })->toArray();
        $this->syncModifierOptions();
    }

    public function updatedIsOpen($value)
    {
        if (!$value) {
            $this->reset(['search']);
            $this->updatedSearch();
        }
    }

    public function updatedSearch()
    {
        $this->menuItems = $this->search
            ? MenuItem::where('item_name', 'like', '%' . $this->search . '%')->get()
            : $this->allMenuItems;
    }

    public function toggleSelectItem($item)
    {
        $itemId = $item['id'];
        if (($key = array_search($itemId, $this->selectedMenuItems)) !== false) {
            unset($this->selectedMenuItems[$key]);
        } else {
            $this->selectedMenuItems[] = $itemId;
        }
        $this->selectedMenuItems = array_values($this->selectedMenuItems);
    }

    public function submitForm()
    {
        $this->validate($this->rules(), $this->messages());

            // Update the modifier group
        $modifierGroup = ModifierGroup::findOrFail($this->modifierGroupId);
        $modifierGroup->update([
            'name' => $this->translationNames[$this->globalLocale],
            'description' => $this->translationDescriptions[$this->globalLocale],
        ]);

        // Update translations (sync instead of delete + create)
        $existingTranslations = $modifierGroup->translations()->pluck('locale')->toArray();
        $newTranslations = collect($this->translationNames)
            ->filter(fn($name, $locale) => !empty($name) || !empty($this->translationDescriptions[$locale]))
            ->map(fn($name, $locale) => [
                'locale' => $locale,
                'name' => $name,
                'description' => $this->translationDescriptions[$locale]
            ]);

        // Update existing translations and create new ones
        foreach ($newTranslations as $translation) {
            $modifierGroup->translations()->updateOrCreate(
                ['locale' => $translation['locale']],
                [
                    'name' => $translation['name'],
                    'description' => $translation['description']
                ]
            );
        }

        // Delete translations that are no longer needed
        $newLocales = $newTranslations->pluck('locale')->toArray();
        $modifierGroup->translations()->whereNotIn('locale', $newLocales)->delete();

        // Update modifier options (UPDATE existing, CREATE new, DELETE removed)
        $existingOptionIds = $modifierGroup->options()->pluck('id')->toArray();
        $submittedOptionIds = collect($this->modifierOptions)
            ->filter(fn($option) => is_numeric($option['id']))
            ->pluck('id')
            ->toArray();

        // Delete removed options (only those not in submitted list)
        $optionsToDelete = array_diff($existingOptionIds, $submittedOptionIds);
        if (!empty($optionsToDelete)) {
            ModifierOption::whereIn('id', $optionsToDelete)->delete();
        }

        // Update existing or create new options
        foreach ($this->modifierOptions as $index => $option) {
            // Prepare translations for this option
            $optionNames = [];
            foreach (array_keys($this->languages) as $lang) {
                $translatedName = $this->modifierOptionInput[$index][$lang] ?? '';
                if (!empty(trim($translatedName))) {
                    $optionNames[$lang] = trim($translatedName);
                }
            }

            $optionData = [
                'name' => $optionNames, // Spatie will cast this as array
                'price' => $option['price'],
                'is_available' => $option['is_available'],
                'sort_order' => $option['sort_order'],
            ];

            if (is_numeric($option['id']) && in_array($option['id'], $existingOptionIds)) {
                // Update existing option
                ModifierOption::where('id', $option['id'])->update($optionData);
            } else {
                // Create new option
                $modifierGroup->options()->create($optionData);
            }
        }

        // Update menu item associations (sync instead of delete + create)
        $existingMenuItems = $modifierGroup->itemModifiers()->pluck('menu_item_id')->toArray();
        $newMenuItems = $this->selectedMenuItems;

        // Delete removed associations
        $itemsToRemove = array_diff($existingMenuItems, $newMenuItems);
        if (!empty($itemsToRemove)) {
            $modifierGroup->itemModifiers()->whereIn('menu_item_id', $itemsToRemove)->delete();
        }

        // Add new associations
        $itemsToAdd = array_diff($newMenuItems, $existingMenuItems);
        if (!empty($itemsToAdd)) {
            $itemModifiers = collect($itemsToAdd)->map(function($menuItemId) use ($modifierGroup) {
                return [
                    'menu_item_id' => $menuItemId,
                    'modifier_group_id' => $modifierGroup->id,
                ];
            })->all();

            ItemModifier::insert($itemModifiers);
        }

        $this->dispatch('hideEditModifierGroupModal');

        $this->alert('success', __('messages.ModifierGroupUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        $this->syncModifierOptions();
        return view('livewire.forms.edit-modifier-group');
    }
}
