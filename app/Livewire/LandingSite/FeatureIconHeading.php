<?php

namespace App\Livewire\LandingSite;

use App\Models\Contact;
use App\Models\FrontDetail;
use App\Models\LanguageSetting;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class FeatureIconHeading extends Component
{
    use LivewireAlert;

    public $languageSettingid;
    public $featureWithIconHeading;


    public function mount()
    {
        if (!$this->languageSettingid) {
            $userLocale = auth()->user()?->locale;

            if ($userLocale) {
                $userLanguage = LanguageSetting::where('language_code', $userLocale)
                    ->where('active', 1)
                    ->first();

                if ($userLanguage) {
                    $this->languageSettingid = $userLanguage->id;
                }
            }

            if (!$this->languageSettingid) {
                $defaultLanguage = LanguageSetting::where('active', 1)->first();
                $this->languageSettingid = $defaultLanguage ? $defaultLanguage->id : null;

                if (!$this->languageSettingid) {
                    $this->alert('error', __('messages.languageNotFound'), [
                        'toast' => true,
                        'position' => 'top-end',
                        'showCancelButton' => false,
                        'cancelButtonText' => __('app.close')
                    ]);
                }
            }

            $this->loadLanguageContents();
        }
    }

    public function loadLanguageContents()
    {
        $frontDetail = FrontDetail::where('language_setting_id', $this->languageSettingid)->first();
        $this->featureWithIconHeading = $frontDetail ? $frontDetail->feature_with_icon_heading : '';

    }

    public function saveFeatureIconHeading()
    {
        $this->validate([
            'featureWithIconHeading' => 'required',
        ]);
            FrontDetail::updateOrCreate(
            ['language_setting_id' => $this->languageSettingid],
            [
            'feature_with_icon_heading' => $this->featureWithIconHeading,
            ]
        );
        $this->alert('success', __('messages.headerSetting'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function updatedLanguageSettingid()
    {
        $this->loadLanguageContents();
    }


    public function render()
    {
        $languageEnable = LanguageSetting::where('active', 1)->get();
        return view('livewire.landing-site.feature-icon-heading', [
            'languageEnable' => $languageEnable,
        ]);
    }

}
