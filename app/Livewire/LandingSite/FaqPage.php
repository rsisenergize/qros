<?php

namespace App\Livewire\LandingSite;

use App\Models\FrontDetail;
use App\Models\FrontFaq;
use App\Models\LanguageSetting;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;

class FaqPage extends Component
{
    use LivewireAlert;

    public $languageSettingid;
    public $faqAnswer;
    public $faqQuestion;
    public $contents = [];
    public $editFaqPageModal = false;
    public $faqDetail;
    public $showAddFaqModal = false;
    public $faqHeading;
    public $faqDescription;


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

    public function addFaqModal()
    {
        $this->showAddFaqModal = true;
    }

    public function loadLanguageContents()
    {
        $frontDetail = FrontDetail::where('language_setting_id', $this->languageSettingid)->first();

        $this->faqHeading = $frontDetail ? $frontDetail->faq_heading : '';
        $this->faqDescription = $frontDetail ? $frontDetail->faq_description : '';
    }

    public function editFaqPage($id)
    {
        $this->faqDetail = FrontFaq::find($id);
        $this->editFaqPageModal = true;

    }

    #[On('hideEditFaqModal')]
    public function hideEditFaqModal()
    {
        $this->editFaqPageModal = false;
        $this->faqDetail = null;
        $this->showAddFaqModal = false;

    }


    public function saveFaq()
    {
        $this->validate([
            'faqQuestion' => 'required',
            'faqAnswer' => 'required',
        ]);

        FrontFaq::create(
            [
                'language_setting_id' => $this->languageSettingid,
                'question' => $this->faqQuestion,
                'answer' => $this->faqAnswer,
            ]
        );

        $this->reset(['faqQuestion']); // Reset the form fields
        $this->dispatch('reset-trix-editor');
        $this->dispatch('hideEditFaqModal');

        $this->alert('success', __('messages.faqAdd'), [
        'toast' => true,
        'position' => 'top-end',
        'showCancelButton' => false,
        'cancelButtonText' => __('app.close')
        ]);

    }

    public function saveHeading()
    {
        $this->validate([
            'languageSettingid' => 'required',
            'faqHeading' => 'required',
            'faqDescription' => 'required',
        ]);
        FrontDetail::updateOrCreate(
            ['language_setting_id' => $this->languageSettingid],
            [
            'faq_heading' => $this->faqHeading,
            'faq_description' => $this->faqDescription,
            ]
        );
        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function deleteFaq($id)
    {
        $feature = FrontFaq::findOrFail($id);
        $feature->delete();
        $this->alert('success', __('messages.faqDeleted'), [
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
        $faqDetails = FrontFaq::where('language_setting_id', $this->languageSettingid)->get();

        return view('livewire.landing-site.faq-page', [
            'languageEnable' => $languageEnable,
            'faqDetails' => $faqDetails,
            'languageSettingid' => $this->languageSettingid,
        ]);
    }

}
