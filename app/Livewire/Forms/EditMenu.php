<?php
namespace App\Livewire\Forms;

use App\Models\Menu;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditMenu extends Component
{
    use LivewireAlert;

    public $menuName;
    public $activeMenu;
    public $translations = [];
    public $currentLanguage;
    public $globalLocale;
    public $languages = [];

    public function mount()
    {
        $this->languages = collect(languages())->pluck('language_name', 'language_code')->toArray();
        $this->globalLocale = global_setting()->locale;
        $this->currentLanguage = $this->globalLocale;
        // Load existing translations
        $this->translations = $this->activeMenu->getTranslations('menu_name') ?? [];

        // Ensure all languages are available
        foreach ($this->languages as $code => $name) {
            if (!isset($this->translations[$code])) {
                $this->translations[$code] = '';
            }
        }

        $this->menuName = $this->translations[$this->currentLanguage] ?? '';
    }

    public function submitForm()
    {
        $this->validate([
            'translations.' . $this->globalLocale => 'required',
        ], [
            'translations.' . $this->globalLocale . '.required' => __('validation.menuNameRequired', ['language' => $this->languages[$this->globalLocale]]),
        ]);

        // Save translations using Spatie
        collect($this->translations)
            ->each(function ($translation, $languageCode) {
            empty(trim($translation))
                ? $this->activeMenu->forgetTranslation('menu_name', $languageCode)
                : $this->activeMenu->setTranslation('menu_name', $languageCode, $translation);
            });

        $this->activeMenu->save();

        $this->alert('success', __('messages.menuUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        $this->dispatch('hideEditMenu');
    }

    public function updateTranslation()
    {
        $this->translations[$this->currentLanguage] = $this->menuName;
    }

    public function removeTranslation($languageCode)
    {
        if (in_array($languageCode, [$this->globalLocale])) {
            $this->alert('error', __('validation.cannotRemoveTranslation', ['language' => $this->languages[$this->globalLocale]]));
            return;
        }

        $this->translations[$languageCode] = null;
        $this->menuName = $this->translations[$this->currentLanguage] ?? '';
    }

    public function updatedCurrentLanguage()
    {
        $this->menuName = $this->translations[$this->currentLanguage] ?? '';
    }

    public function render()
    {
        return view('livewire.forms.edit-menu');
    }
}
