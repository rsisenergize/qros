<?php

namespace App\Livewire\LandingSite;

use App\Models\FrontDetail;
use App\Models\LanguageSetting;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class PriceSetting extends Component
{
    use LivewireAlert;

    public $languageSettingid;
    public $priceTitle;
    public $priceDescription;
    public $frontDetail;


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
                $this->languageSettingid = $defaultLanguage?->id;
            }
        }

        $this->loadLanguageContents();
    }

    public function loadLanguageContents()
    {
        $frontDetail = FrontDetail::where('language_setting_id', $this->languageSettingid)->first();
        $this->priceTitle = $frontDetail ? $frontDetail->price_heading : '';
        $this->priceDescription = $frontDetail ? $frontDetail->price_description : '';
    }

    public function updatedLanguageSettingid()
    {
        $this->loadLanguageContents();
    }

    public function priceSettingSave()
    {
        $this->validate([
            'languageSettingid' => 'required',
            'priceTitle' => 'required',
            'priceDescription' => 'required',
        ]);

        FrontDetail::updateOrCreate(
            [
                'language_setting_id' => $this->languageSettingid,
            ],
            [
                'price_heading' => $this->priceTitle,
                'price_description' => $this->priceDescription,
            ]
        );

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }


    public function render()
    {
        $languageEnable = LanguageSetting::where('active', 1)->get();
        return view('livewire.landing-site.price-setting', [
            'languageEnable' => $languageEnable
        ]);
    }

}
