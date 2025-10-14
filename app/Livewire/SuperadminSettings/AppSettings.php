<?php

namespace App\Livewire\SuperadminSettings;

use App\Helper\Files;
use App\Models\LanguageSetting;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\GlobalCurrency;
use App\Models\User;
use DateTimeZone;

class AppSettings extends Component
{
    use LivewireAlert, WithFileUploads;

    public $settings;
    public $appName;
    public $defaultLanguage;
    public $languageSettings;
    public $globalCurrencies;
    public $defaultCurrency;
    public $mapApiKey;
    public $privacyPolicyLink;
    public bool $showPrivacyConsentCheckbox;
    public bool $requiresApproval;
    public $sessionDriver;
    public $phoneNumber;
    public $phoneCode;
    public $phoneCodeSearch = '';
    public $phoneCodeIsOpen = false;
    public $allPhoneCodes;
    public $filteredPhoneCodes;
    public $timezone;
    public $timezones;

    public function mount()
    {
        $this->appName = $this->settings->name;
        $this->requiresApproval = $this->settings->requires_approval_after_signup;
        $this->defaultLanguage = $this->settings->locale;
        $this->languageSettings = LanguageSetting::where('active', 1)->get();
        $this->globalCurrencies = GlobalCurrency::where('status', 'enable')->get();
        $this->defaultCurrency = $this->settings->default_currency_id;
        $this->mapApiKey = $this->settings->google_map_api_key;
        $this->privacyPolicyLink = $this->settings->privacy_policy_link;
        $this->showPrivacyConsentCheckbox = $this->settings->show_privacy_consent_checkbox ?? false;
        $this->sessionDriver = $this->settings->session_driver;
        // Phone code/number
        $this->phoneNumber = user()->phone_number ?? '';
        $this->phoneCode = user()->phone_code ?? '';
        $this->allPhoneCodes = collect(\App\Models\Country::pluck('phonecode')->unique()->filter()->values());
        $this->filteredPhoneCodes = $this->allPhoneCodes;

        // Timezone
        $this->timezone = $this->settings->timezone ?? 'UTC';
        $this->timezones = DateTimeZone::listIdentifiers();
    }

    public function updatedPhoneCodeIsOpen($value)
    {
        if (!$value) {
            $this->reset(['phoneCodeSearch']);
            $this->updatedPhoneCodeSearch();
        }
    }

    public function updatedPhoneCodeSearch()
    {
        $this->filteredPhoneCodes = $this->allPhoneCodes->filter(function ($phonecode) {
            return str_contains($phonecode, $this->phoneCodeSearch);
        })->values();
    }

    public function selectPhoneCode($phonecode)
    {
        $this->phoneCode = $phonecode;
        $this->phoneCodeIsOpen = false;
        $this->phoneCodeSearch = '';
        $this->updatedPhoneCodeSearch();
    }

    public function updatedShowPrivacyConsentCheckbox($value)
    {
        // Clear privacy policy link when checkbox is unchecked
        if (!$value) {
            $this->privacyPolicyLink = null;
        }
    }

    public function submitForm()
    {
        $validationRules = [
            'appName' => 'required',
            'phoneNumber' => [
                'required',
                'regex:/^[0-9\s]{8,20}$/',
            ],
            'phoneCode' => 'required',
            'timezone' => 'required',
        ];

        // Add privacy policy link validation if consent checkbox is enabled
        if ($this->showPrivacyConsentCheckbox) {
            $validationRules['privacyPolicyLink'] = 'required|url';
        }

        $this->validate($validationRules);

        $this->settings->name = $this->appName;
        $this->settings->requires_approval_after_signup = $this->requiresApproval;
        $this->settings->locale = $this->defaultLanguage;
        $this->settings->default_currency_id = $this->defaultCurrency;
        $this->settings->google_map_api_key = $this->mapApiKey ?? null;
        $this->settings->privacy_policy_link = $this->privacyPolicyLink ?? null;
        $this->settings->show_privacy_consent_checkbox = $this->showPrivacyConsentCheckbox;
        $this->settings->session_driver = $this->sessionDriver ?? null;
        $this->settings->timezone = $this->timezone;
        // Save phone_number and phone_code to the User table for the current user
        user()->update([
            'phone_number' => $this->phoneNumber,
            'phone_code' => $this->phoneCode,
        ]);
        $this->settings->save();

        cache()->forget('languages');

        if (languages()->count() == 1) {
            User::withOutGlobalScopes()->update(['locale' => $this->defaultLanguage]);
        }

        cache()->forget('global_setting');
        session()->forget('restaurantOrGlobalSetting');
        session()->forget('timezone');

        $this->redirect(route('superadmin.superadmin-settings.index'), navigate: true);

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.superadmin-settings.app-settings', [
            'phonecodes' => $this->filteredPhoneCodes,
            'timezones' => $this->timezones,
        ]);
    }
}
