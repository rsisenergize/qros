<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\Models\Country;
use Livewire\Component;
use App\Models\Restaurant;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditRestaurant extends Component
{

    use LivewireAlert;

    public $restaurantName;
    public $fullName;
    public $email;
    public $phone;
    public $phoneCode;
    public $address;
    public $country;
    public $facebook;
    public $instagram;
    public $twitter;
    public $countries;
    public $restaurant;
    public $isActive;
    public $sub_domain;
    public $domain;
    public $phoneCodeSearch = '';
    public $phoneCodeIsOpen = false;
    public $allPhoneCodes;
    public $filteredPhoneCodes;

    public function mount()
    {
        if (module_enabled('Subdomain')) {
            $this->sub_domain = str_replace('.' . getDomain(), '', $this->restaurant->sub_domain);
            $this->domain = str($this->restaurant->sub_domain)->endsWith(getDomain()) ? '.' . getDomain() : '';
        }

        $ipCountry = (new User)->getCountryFromIp();

        $defaultCountry = Country::where('countries_code', $ipCountry)->first();

        $this->countries = Country::all();

        $this->restaurantName = $this->restaurant->name;
        $this->email = $this->restaurant->email;
        $this->phone = $this->restaurant->phone_number;
        $this->phoneCode = $this->restaurant->phone_code;
        $this->address = $this->restaurant->address;
        $this->country = $this->restaurant->country_id;
        $this->facebook = $this->restaurant->facebook_link;
        $this->instagram = $this->restaurant->instagram_link;
        $this->twitter = $this->restaurant->twitter_link;
        $this->isActive = (int)$this->restaurant->is_active;
        
        // Initialize phone codes
        $this->allPhoneCodes = collect(Country::pluck('phonecode')->unique()->filter()->values());
        $this->filteredPhoneCodes = $this->allPhoneCodes;
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

    public function submitForm()
    {
        $rules = [
            'restaurantName' => 'required',
            'email' => 'required',
            'isActive' => 'required|in:0,1',
        ];

        if (module_enabled('Subdomain')) {
            // Validate domain or subdomain based on input
            if (empty($this->domain)) {
                $rules['sub_domain'] = 'required|string';
            } else {
                $rules['sub_domain'] = 'required|min:3|max:50|regex:/^[a-z0-9\-_]{2,20}$/|banned_sub_domain';
            }
        }

        $this->validate($rules);

        if (module_enabled('Subdomain')) {
            $restaurant = Restaurant::where('id', '!=', $this->restaurant->id)
                ->where('sub_domain', strtolower($this->sub_domain . $this->domain))
                ->exists();

            if ($restaurant) {
                $this->addError('sub_domain', __('subdomain::app.messages.subdomainAlreadyExists'));
                return;
            }

            $this->restaurant->sub_domain = strtolower($this->sub_domain . $this->domain);
        }

        $this->restaurant->name = $this->restaurantName;
        $this->restaurant->address = $this->address;
        $this->restaurant->email = $this->email;
        $this->restaurant->phone_number = $this->phone;
        $this->restaurant->phone_code = $this->phoneCode;
        $this->restaurant->country_id = $this->country;
        $this->restaurant->facebook_link = $this->facebook;
        $this->restaurant->instagram_link = $this->instagram;
        $this->restaurant->twitter_link = $this->twitter;
        $this->restaurant->is_active = (bool)$this->isActive;
        $this->restaurant->save();

        $this->dispatch('hideEditStaff');

        $this->alert('success', __('messages.restaurantUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.forms.edit-restaurant', [
            'phonecodes' => $this->filteredPhoneCodes,
        ]);
    }

}
