<?php

namespace App\Livewire\LandingSite;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditReviewPage extends Component
{
    use LivewireAlert;

    public $languageSettingid;
    public $reviewText;
    public $reviewDetail;
    public $reviewerName;
    public $reviewerDesignation;
    public $languageEnable;

    public function mount()
    {
        $this->languageSettingid = $this->reviewDetail->language_setting_id;
        $this->reviewText = $this->reviewDetail->reviews;
        $this->reviewerName =  $this->reviewDetail->reviewer_name;
        $this->reviewerDesignation = $this->reviewDetail->reviewer_designation;
    }

    public function editReviewPage()
    {
        $this->validate([
            'languageSettingid' => 'required',
            'reviewText' => 'required',
            'reviewerName' => 'required',
            'reviewerDesignation' => 'required',

        ]);

        $this->reviewDetail->language_setting_id = $this->languageSettingid;
        $this->reviewDetail->reviews = $this->reviewText;
        $this->reviewDetail->reviewer_name = $this->reviewerName;
        $this->reviewDetail->reviewer_designation = $this->reviewerDesignation;

        $this->reviewDetail->save();

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
        $this->dispatch('editReviewPageModal');

    }

    public function render()
    {
        return view('livewire.landing-site.edit-review-page');

    }
}
