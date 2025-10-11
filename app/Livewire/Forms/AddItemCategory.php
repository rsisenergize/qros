<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\ItemCategory;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddItemCategory extends Component
{
    use LivewireAlert;

    public $categoryName = '';
    public $translations = [];
    public $languages = [];
    public $currentLanguage;
    public $globalLocale;

    public function mount()
    {
        $this->languages = languages()->pluck('language_name', 'language_code')->toArray();
        $this->translations = array_fill_keys(array_keys($this->languages), '');
        $this->globalLocale = global_setting()->locale;
        $this->currentLanguage = $this->globalLocale;
    }

    public function updateTranslation()
    {
        $this->translations[$this->currentLanguage] = $this->categoryName;
    }

    public function updatedCurrentLanguage()
    {
        $this->categoryName = $this->translations[$this->currentLanguage] ?? '';
    }

    public function submitForm()
    {
        $this->validate([
            'translations.' . $this->globalLocale => [
                'required',
                Rule::unique('item_categories', "category_name->{$this->globalLocale}")
                    ->where('branch_id', branch()->id),
            ],
        ], [
            'translations.' . $this->globalLocale . '.required' => __('validation.categoryNameRequired', ['language' => $this->languages[$this->globalLocale]]),
            'translations.' . $this->globalLocale . '.unique' => __('validation.categoryNameUnique', ['language' => $this->languages[$this->globalLocale]]),
        ]);

        $filteredTranslations = array_filter($this->translations, 'trim');

        ItemCategory::create([
            'category_name' => $filteredTranslations
        ]);

        // Reset the value
        $this->categoryName = '';
        $this->translations = array_fill_keys(array_keys($this->translations), '');

        $this->dispatch('refreshCategories');
        $this->dispatch('hideCategoryModal');

        $this->alert('success', __('messages.categoryAdded'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.forms.add-item-category');
    }

}
