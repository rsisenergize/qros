<?php

namespace App\Livewire\Settings;

use App\Models\LanguageSetting;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class LanguageSettings extends Component
{

    use LivewireAlert;

    public $languageSettings;
    public $languageActive = [];
    public $languageRtl = [];
    public $languageID = [];
    public $language;
    public $showAddLanguage = false;
    public $showEditLanguageModal = false;
    public $confirmDeleteLanguageModal = false;
    public $langPath;

    protected $listeners = ['hideAddLanguage' => '$refresh', 'hideDeleteLanguage' => '$refresh', 'hideEditLanguage' => '$refresh'];

    public function mount()
    {
        $this->languageSettings = LanguageSetting::all();
        $this->langPath = base_path() . '/lang';

        foreach ($this->languageSettings as $value) {
            $this->languageID[] = $value->id;
            $this->languageActive[] = (bool)$value->active;
            $this->languageRtl[] = (bool)$value->is_rtl;
        }
    }

    #[On('hideAddLanguage')]
    public function hideAddLanguage()
    {
        $this->showAddLanguage = false;
        cache()->forget('languages');
    }

    #[On('hideEditLanguage')]
    public function hideEditLanguage()
    {
        $this->showEditLanguageModal = false;
        cache()->forget('languages');
    }

    public function showEditLanguage($id)
    {
        $this->language = LanguageSetting::findOrFail($id);
        $this->showEditLanguageModal = true;
    }

    public function showDeleteLanguage($id)
    {
        $this->language = LanguageSetting::findOrFail($id);
        $this->confirmDeleteLanguageModal = true;
    }

    public function deleteLanguage($id)
    {
        $this->language = LanguageSetting::findOrFail($id);
        $this->language->delete();

        $langExists = File::exists($this->langPath . '/' . $this->language->language_code);

        if ($langExists) {
            File::deleteDirectory($this->langPath . '/' . $this->language->language_code);
        }

        if (Schema::hasTable('ltm_translations')) {
            DB::statement('DELETE FROM ltm_translations where locale = "' . $this->language->language_code . '"');
        }


        cache()->forget('languages');

        if (languages()->count() == 1) {
            User::withOutGlobalScopes()->update(['locale' => global_setting()->locale]);
        }


        $this->dispatch('hideDeleteLanguage');

        $this->confirmDeleteLanguageModal = false;
    }

    public function submitForm()
    {
        foreach ($this->languageID as $key => $value) {
            $language = LanguageSetting::find($value);

            if ($language) {
                $language->active = $this->languageActive[$key];
                $language->is_rtl = $this->languageRtl[$key];
                $language->save();
            }
        }


        cache()->forget('languages');
        session()->forget('isRtl');

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.settings.language-settings');
    }
}
