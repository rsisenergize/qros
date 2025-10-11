<?php

namespace App\Livewire\LandingSite;

use Livewire\Component;
use App\Models\FrontDetail;
use Livewire\Attributes\On;
use App\Models\LanguageSetting;
use App\Models\FrontReviewSetting;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ReviewPage extends Component
{
    use LivewireAlert;

    public $languageSettingid;
    public $reviews;
    public $reviewText;
    public $contents = [];
    public $editReviewPageModal = false;
    public $reviewDetail;
    public $reviewHeading;
    public $reviewerName;
    public $reviewerDesignation;
    public $showAddReviewModal = false;


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
            }

            if (!$this->languageSettingid) {
                $this->alert('error', __('messages.languageNotFound'), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
                ]);
            }

            $this->loadLanguageContents();
        }
    }

    public function addReviewModal()
    {
        $this->showAddReviewModal = true;
    }

    public function loadLanguageContents()
    {
        $reviewDetails = FrontReviewSetting::all();
        $frontDetail = FrontDetail::where('language_setting_id', $this->languageSettingid)->first();
        $this->reviewHeading = $frontDetail ? $frontDetail->review_heading : '';
        
        foreach ($reviewDetails as $detail) {
            $this->contents[$detail->language_setting_id] = [
                'review' => $detail->header_title,
            ];
        }
    }

    public function updatedLanguageSettingid()
    {
        $this->loadLanguageContents();
    }

    public function editReviewPage($id)
    {
        $this->reviewDetail = FrontReviewSetting::find($id);
        $this->editReviewPageModal = true;
    }

    public function saveReview()
    {
        $this->validate([
            'languageSettingid' => 'required',
            'reviewerName'  => 'required',
            'reviewerDesignation' => 'required',
            'reviewText' => 'required',
        ]);

        FrontReviewSetting::create(
            [
                'language_setting_id' => $this->languageSettingid,
                'reviews' => $this->reviewText,
                'reviewer_name' => $this->reviewerName,
                'reviewer_designation' => $this->reviewerDesignation,

            ]
        );

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
        $this->reset(['reviewText','reviewerName','reviewerDesignation']);

    }
    public function deleteReview($id)
    {
        $feature = FrontReviewSetting::findOrFail($id);
        $feature->delete();
        $this->alert('success', __('messages.reviewDeleted'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

     #[On('hideEditReviewModal')]
    public function hideEditReviewModal()
    {
        $this->editReviewPageModal = false;
        $this->reviewDetail = null;
        $this->showAddReviewModal = null;

    }

    public function saveReviewHeading()
    {
        $this->validate([
            'languageSettingid' => 'required',
            'reviewHeading' => 'required',
        ]);
        FrontDetail::updateOrCreate(
            ['language_setting_id' => $this->languageSettingid],
            ['review_heading' => $this->reviewHeading],
        );

        $this->alert('success', __('messages.contactSetting'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        $languageEnable = LanguageSetting::where('active', 1)->get();
        $reviewsDetails = FrontReviewSetting::where('language_setting_id', $this->languageSettingid)->get();
        return view('livewire.landing-site.review-page', [
            'languageEnable' => $languageEnable,
            'reviewsDetails' => $reviewsDetails,
            'languageSettingid' => $this->languageSettingid,
        ]);
    }

}
