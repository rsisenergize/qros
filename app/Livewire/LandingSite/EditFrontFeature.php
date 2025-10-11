<?php

namespace App\Livewire\LandingSite;

use App\Helper\Files;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class EditFrontFeature extends Component
{
    use WithFileUploads, LivewireAlert, WithPagination;

    public $languageSettingid;
    public $featureTitle;
    public $featureDescription;
    public $featureImage;
    public $existingImageUrl;
    public $frontDetail;
    public $languageEnable;
    public $showEditFrontFeatureModal = false;


    public function mount()
    {
        $this->languageSettingid = $this->frontDetail->language_setting_id;
        $this->featureTitle = $this->frontDetail->title;
        $this->featureDescription = $this->frontDetail->description;
        $defaults = [
            'streamline order management'   => asset('landing/order-management.png'),
            'optimize table reservations'   => asset('landing/table-reservation.png'),
            'effortless menu management'    => asset('landing/order-management.png'),
        ];

        $titleKey = strtolower($this->frontDetail->title ?? '');

        if (!empty($this->frontDetail->image)) {
            $this->existingImageUrl = $this->frontDetail->image_url ?? null;
        } else {
            $this->existingImageUrl = $defaults[$titleKey] ?? null;
        }

        // Dispatch event to initialize Trix editor with existing content
        $this->dispatch('description-updated', $this->featureDescription);
    }

    public function updatedFeatureDescription()
    {
        // Dispatch event when description is updated to sync with Trix editor
        $this->dispatch('description-updated', $this->featureDescription);
    }

    public function editFrontFeature()
    {
        $this->validate([
            'languageSettingid' => 'required',
            'featureTitle' => 'required',
            'featureDescription' => 'required',
        ]);

        $this->frontDetail->language_setting_id = $this->languageSettingid;
        $this->frontDetail->title = $this->featureTitle;
        $this->frontDetail->description = $this->featureDescription;
        $this->frontDetail->save();

        if ($this->featureImage) {
            $this->frontDetail->update([
                'image' => Files::uploadLocalOrS3($this->featureImage, 'front_feature', width: 800),
            ]);
        }
        $this->showEditFrontFeatureModal = false;

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.landing-site.edit-front-feature');
    }
}
