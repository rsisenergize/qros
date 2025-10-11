<?php

namespace App\Livewire\LandingSite;

use App\Helper\Files;
use App\Models\FrontFeature;
use App\Models\LanguageSetting;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class FrontFeaturePage extends Component
{
    use WithFileUploads, LivewireAlert, WithPagination;

    public $language;
    public $featureTitle;
    public $featureDescription;
    public $featureImage;
    public $frontDetail;
    public $existingImageUrl;
    public $addFeatureModal = false;
    public $featureIdToDelete = null;
    public $showEditFrontFeatureModal = false;
    public $activeTab = 'features';


    public function deleteFrontFeature($id)
    {
        $feature = FrontFeature::findOrFail($id);
        $feature->delete();
        $this->alert('success', __('messages.featureDeleted'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function editFeature($id)
    {
        $this->frontDetail = FrontFeature::findOrFail($id);
        $this->showEditFrontFeatureModal = true;
    }

    #[On('hideEditFeature')]
    public function hideEditFeature()
    {
        $this->showEditFrontFeatureModal = false;
        $this->addFeatureModal = false;
    }

    public function saveFeature()
    {
        $this->validate([
            'language' => 'required',
            'featureTitle' => 'required',
            'featureDescription' => 'required',
            'featureImage' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $frontDetail = FrontFeature::create(
            [
                'language_setting_id' => $this->language,
                'title' => $this->featureTitle,
                'description' => $this->featureDescription,
            ]
        );

        if ($this->featureImage) {
            $imageName = Files::uploadLocalOrS3($this->featureImage, 'front_feature', width: 350);
            $frontDetail->update([
                'image' => $imageName,
            ]);
        }
        $this->language = '';
        $this->featureTitle = '';
        $this->featureDescription = '';
        $this->featureImage = null;
        $this->addFeatureModal = false;

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
        $currentLocale = auth()->user()?->locale;
        $languageSetting = LanguageSetting::where('language_code', $currentLocale)->where('active', 1)->first();
        $frontDetails = FrontFeature::where('type', 'image');

        if ($languageSetting) {
            $frontDetails = $frontDetails->where('language_setting_id', $languageSetting->id);
        }

        $frontDetails = $frontDetails->paginate(10);
        return view('livewire.landing-site.front-feature-page', [
            'languageEnable' => $languageEnable,
            'frontDetails' => $frontDetails,

        ]);
    }
}
