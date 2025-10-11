<?php

namespace App\Livewire\LandingSite;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditFaqPage extends Component
{
    use LivewireAlert;

    public $languageSettingid;
    public $faqAnswer;
    public $faqQuestion;
    public $faqDetail;
    public $languageEnable;

    public function mount()
    {
        $this->languageSettingid = $this->faqDetail->language_setting_id;
        $this->faqQuestion = $this->faqDetail->question;
        $this->faqAnswer = $this->faqDetail->answer;

    }

    public function editFaqPage()
    {
        $this->validate([
            'languageSettingid' => 'required',
            'faqQuestion' => 'required',
            'faqAnswer' => 'required',

        ]);

        $this->faqDetail->language_setting_id = $this->languageSettingid;
        $this->faqDetail->question = $this->faqQuestion;
        $this->faqDetail->answer = $this->faqAnswer;
        $this->faqDetail->save();

        $this->alert('success', __('messages.faqUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
        $this->dispatch('hideEditFaqModal');
    }

    public function render()
    {
        return view('livewire.landing-site.edit-faq-page');

    }
}
