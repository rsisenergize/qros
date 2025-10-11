<?php

namespace App\Livewire\LandingSite;

use App\Helper\Files;
use App\Models\FrontDetail;
use App\Models\LanguageSetting;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithFileUploads;

class HeaderPage extends Component
{
    use WithFileUploads, LivewireAlert;

    public $languageSettingid;
    public $headerTitle;
    public $headerDescription;
    public $headerImage;
    public $frontDetail;
    public $existingImageUrl;

    public $contents = [];

    public function mount()
    {
        $this->loadLanguageContents();

        $userLocale = auth()->user()?->locale;

        if ($userLocale) {
            $language = LanguageSetting::where('language_code', $userLocale)->where('active', 1)->first();
            if ($language) {
                $this->languageSettingid = $language->id;
            }
        }

        $this->loadSelectedLanguageContent();
    }

    public function loadLanguageContents()
    {
        $frontDetails = FrontDetail::all();
        foreach ($frontDetails as $detail) {
            $this->contents[$detail->language_setting_id] = [
                'header_title' => $detail->header_title,
                'header_description' => $detail->header_description,
            ];
        }
    }

    public function updatedLanguageSettingid()
    {
        $this->loadSelectedLanguageContent();
    }

    public function loadSelectedLanguageContent()
    {
        $frontDetail = FrontDetail::where('language_setting_id', $this->languageSettingid)->first();

        if ($frontDetail) {
            $this->headerTitle = $frontDetail->header_title ?? '';
            $this->headerDescription = $frontDetail->header_description ?? '';
            $this->existingImageUrl = $frontDetail->image_url ?? null;
        } else {
            $this->headerTitle = '';
            $this->headerDescription = '';
            $this->existingImageUrl = null;
        }
    }

    public function saveHeader()
    {
        $this->validate([
            'languageSettingid' => 'required',
            'headerTitle' => 'required',
            'headerDescription' => 'required',
            'headerImage' => $this->existingImageUrl ? '' : 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        $frontDetail = FrontDetail::updateOrCreate(
            [
                'language_setting_id' => $this->languageSettingid,
            ],
            [
                'header_title' => $this->headerTitle,
                'header_description' => $this->headerDescription,
                'image' => $this->headerImage ? Files::uploadLocalOrS3($this->headerImage, 'header') : null,
            ]
        );

        if ($this->headerImage) {
            $imageName = Files::uploadLocalOrS3($this->headerImage, 'header');
            $frontDetail->update([
                'image' => $imageName,
            ]);
            $this->headerImage = null;
        }

        $this->existingImageUrl = $frontDetail ? $frontDetail->image_url : null;

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function removeImage()
    {
        $frontDetail = FrontDetail::where('language_setting_id', $this->languageSettingid)->first();

        if ($frontDetail && $frontDetail->image) {
            Files::deleteFile($frontDetail->image, 'header');
            $frontDetail->update(['image' => null]);
        }

        $this->existingImageUrl = null;

        $this->alert('success', __('messages.imageRemoved'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        $languageEnable = LanguageSetting::where('active', 1)->get();
        return view('livewire.landing-site.header-page', [
            'languageEnable' => $languageEnable
        ]);
    }
}
