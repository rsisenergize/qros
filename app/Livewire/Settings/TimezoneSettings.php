<?php

namespace App\Livewire\Settings;

use App\Models\Country;
use App\Models\Currency;
use DateTimeZone;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\LanguageSetting;

class TimezoneSettings extends Component
{
    use LivewireAlert;

    public $settings;
    public $restaurantCountry;
    public $restaurantTimezone;
    public $restaurantCurrency;
    public $countries;
    public $timezones;
    public $currencies;
    public $hideTodayOrders;
    public $hideNewReservation;
    public $hideNewWaiterRequest;
    public $customerLanguage;

    public function mount()
    {
        $this->restaurantCountry = $this->settings->country_id;
        $this->restaurantTimezone = $this->settings->timezone;
        $this->restaurantCurrency = $this->settings->currency_id;
        $this->hideTodayOrders = (bool)$this->settings->hide_new_orders;
        $this->hideNewReservation = (bool)$this->settings->hide_new_reservations;
        $this->hideNewWaiterRequest = (bool)$this->settings->hide_new_waiter_request;
        $this->customerLanguage = $this->settings->customer_site_language ?? 'en';
        $this->countries = Country::all();
        $this->currencies = Currency::all();
        $this->timezones = DateTimeZone::listIdentifiers();
    }

    public function submitForm()
    {
        $this->validate([
            'restaurantCountry' => 'required',
            'restaurantCurrency' => 'required',
            'restaurantTimezone' => 'required',
        ]);

        $this->settings->timezone = $this->restaurantTimezone;
        $this->settings->country_id = $this->restaurantCountry;
        $this->settings->currency_id = $this->restaurantCurrency;
        $this->settings->hide_new_orders = $this->hideTodayOrders;
        $this->settings->hide_new_reservations = $this->hideNewReservation;
        $this->settings->hide_new_waiter_request = $this->hideNewWaiterRequest;
        $this->settings->customer_site_language = $this->customerLanguage ?? 'en';
        $this->settings->save();

        session()->forget('restaurant');
        session()->forget('timezone');

        session()->forget('customer_locale');
        session()->forget('customer_site_language');
        session()->forget('customer_is_rtl');


        $this->dispatch('settingsUpdated');

        $this->js('window.location.reload()');

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.settings.timezone-settings');
    }

}
