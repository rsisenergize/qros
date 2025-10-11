<?php

namespace App\Livewire\LandingSite;

use App\Helper\Files;
use App\Models\Contact;
use App\Models\FrontDetail;
use App\Models\LanguageSetting;
use Livewire\WithFileUploads;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ContactPage extends Component
{
    use LivewireAlert, WithFileUploads;

    public $languageSettingid;
    public $email;
    public $address;
    public $contactDetails;
    public $contactHeading;
    public $contactCompany;
    public $contactImage;
    public $existingImageUrl;
    public $tempImageUrl;

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
        $contacts = Contact::where('language_setting_id', $this->languageSettingid)->first();
        $frontDetail = FrontDetail::where('language_setting_id', $this->languageSettingid)->first();
        $this->contactHeading = $frontDetail ? $frontDetail->contact_heading : '';
        $this->email = $contacts ? $contacts->email : '';
        $this->address = $contacts ? $contacts->address : '';
        $this->contactCompany = $contacts ? $contacts->contact_company : '';
        $this->existingImageUrl = $contacts ? $contacts->image_url : null;
    }

    public function saveContact()
    {
        $this->validate([
            'email' => 'required',
            'address' => 'required',
            'contactCompany' => 'required',
            'contactImage' => $this->existingImageUrl ? '' : 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        $contact = Contact::updateOrCreate(
            ['language_setting_id' => $this->languageSettingid],
            [
                'email' => $this->email,
                'contact_company' => $this->contactCompany,
                'address' => $this->address,
            ]
        );

        if ($this->contactImage) {
            $imageName = Files::uploadLocalOrS3($this->contactImage, 'contact_image', width: 350);
            $contact->update([
                'image' => $imageName,
            ]);
        }
        $this->alert('success', __('messages.contactSetting'), [
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

    public function updatedContactImage()
    {
        $this->validate([
            'contactImage' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($this->contactImage) {
            $this->tempImageUrl = $this->contactImage->temporaryUrl();
        }
    }

    public function saveContactHeading()
    {
        $this->validate([
            'languageSettingid' => 'required',
            'contactHeading' => 'required',
            'contactCompany' => 'required',
        ]);
        FrontDetail::updateOrCreate(
            ['language_setting_id' => $this->languageSettingid],
            ['contact_heading' => $this->contactHeading],
            ['contact_company' => $this->contactCompany]
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
        return view('livewire.landing-site.contact-page', [
            'languageEnable' => $languageEnable,
        ]);
    }
}
