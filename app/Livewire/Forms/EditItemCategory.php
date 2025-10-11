<?php

namespace App\Livewire\Forms;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Validation\Rule;
class EditItemCategory extends Component
{
    use LivewireAlert;

    public $categoryName;
    public $itemCategory;
    public $translations = [];
    public $currentLanguage;
    public $languages = [];
    public $globalLocale;

    public function mount()
    {
        $this->languages = collect(languages())->pluck('language_name', 'language_code')->toArray();
        $this->globalLocale = global_setting()->locale;
        $this->currentLanguage = $this->globalLocale;
        // Load existing translations
        $this->translations = $this->itemCategory->getTranslations('category_name') ?? [];

        // Ensure all languages are available
        foreach ($this->languages as $code => $name) {
            if (!isset($this->translations[$code])) {
                $this->translations[$code] = '';
            }
        }

        $this->categoryName = $this->translations[$this->globalLocale] ?? '';
    }

    public function submitForm()
    {
        $this->validate([
            'translations.' . $this->globalLocale => [
                'required',
                Rule::unique('item_categories', "category_name->{$this->globalLocale}")
                    ->ignore($this->itemCategory->id) // Ignore the current category ID when updating
                    ->where('branch_id', branch()->id),
            ],
        ], [
            'translations.' . $this->globalLocale . '.required' => __('validation.categoryNameRequired', ['language' => $this->languages[$this->globalLocale]]),
            'translations.' . $this->globalLocale . '.unique' => __('validation.categoryNameUnique', ['language' => $this->languages[$this->globalLocale]]),
        ]);

        collect($this->translations)->each(function ($translation, $code) {
            empty(trim($translation))
                ? $this->itemCategory->forgetTranslation('category_name', $code)
                : $this->itemCategory->setTranslation('category_name', $code, $translation);
        });

        $this->itemCategory->save();

        $this->dispatch('refreshCategories');
        $this->dispatch('hideCategoryModal');

        $this->alert('success', __('messages.categoryUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function removeTranslation($languageCode)
    {
        if (in_array($languageCode, [$this->currentLanguage])) {
            $this->alert('error', __('validation.cannotRemoveTranslation', ['language' => $this->languages[$languageCode]]));
            return;
        }
        $this->translations[$languageCode] = null;
        $this->categoryName = $this->translations[$this->currentLanguage] ?? '';
    }

    public function updateTranslation()
    {
        $this->translations[$this->currentLanguage] = $this->categoryName;
    }

    public function updatedCurrentLanguage()
    {
        $this->categoryName = $this->translations[$this->currentLanguage] ?? '';
    }

    public function render()
    {
        return view('livewire.forms.edit-item-category');
    }

}
