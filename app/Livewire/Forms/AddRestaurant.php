<?php

namespace App\Livewire\Forms;

use App\Enums\PackageType;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Package;
use Livewire\Component;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Http;
use App\Notifications\WelcomeRestaurantEmail;
use Spatie\Permission\Models\Permission;

class AddRestaurant extends Component
{

    public $restaurantName;
    public $sub_domain;
    public $fullName;
    public $email;
    public $password;
    public $branchName;
    public $address;
    public $country;
    public $countries;
    public $status;
    public $facebook;
    public $instagram;
    public $twitter;
    public $licenseType = 'free';
    public $showUserForm = true;
    public $showBranchForm = false;
    public $domain;
    public $phoneCode;
    public $phone;
    public $restaurantPhoneNumber;
    public $restaurantPhoneCode;
    public $phoneCodeSearch = '';
    public $phoneCodeIsOpen = false;
    public $allPhoneCodes;
    public $filteredPhoneCodes;

    public function mount()
    {
        $this->countries = Country::all();
        $this->domain = '.' . getDomain();

        $ipCountry = (new User)->getCountryFromIp();

        $defaultCountry = Country::where('countries_code', $ipCountry)->first();
        if (!$defaultCountry) {
            $defaultCountry = Country::first();
        }
        $this->country = $defaultCountry->id;
        $this->phoneCode = user()?->phone_code ?? $defaultCountry->phonecode;
        $this->restaurantPhoneCode = $this->phoneCode; // Set default for select box
        $this->status = 1; // Set default status to active

        // Initialize phone codes
        $this->allPhoneCodes = collect(Country::pluck('phonecode')->unique()->filter()->values());
        $this->filteredPhoneCodes = $this->allPhoneCodes;
    }

    public function updatedCountry($value)
    {
        $country = Country::find($value);
        $this->phoneCode = $country->phonecode;
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
        $this->restaurantPhoneCode = $phonecode;
        $this->phoneCodeIsOpen = false;
        $this->phoneCodeSearch = '';
        $this->updatedPhoneCodeSearch();
    }

    public function submitForm()
    {
        if (!$this->validateSubdomain()) {
            return;
        }

        $this->validate([
            'restaurantName' => 'required',
            'fullName' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'restaurantPhoneNumber' => [
                'required',
                'regex:/^[0-9\s]{8,20}$/',
            ],
        ]);

        $this->showUserForm = false;
        $this->showBranchForm = true;
    }

    public function submitForm2()
    {

        $timezone = (new User)->getTimezoneFromIp();

        $this->validate([
            'address' => 'required',
            'branchName' => 'required',
        ]);

        $restaurant = new Restaurant();
        $restaurant->name = $this->restaurantName;
        $package = Package::firstWhere('package_type', PackageType::DEFAULT);

        if (module_enabled('Subdomain')) {
            $restaurant->sub_domain = strtolower($this->sub_domain . $this->domain);
        }

        $restaurant->hash = md5(microtime() . rand(1, 99999999));
        $restaurant->address = $this->address;
        $restaurant->timezone = $timezone ?? 'UTC';
        $restaurant->theme_hex = global_setting()->theme_hex;
        $restaurant->theme_rgb = global_setting()->theme_rgb;
        $restaurant->email = $this->email;
        $restaurant->country_id = $this->country;
        $restaurant->license_type = $this->licenseType;
        $restaurant->phone_number = $this->restaurantPhoneNumber;
        $restaurant->phone_code = $this->restaurantPhoneCode;
        $restaurant->is_active = (bool)$this->status;
        $restaurant->facebook_link = $this->facebook;
        $restaurant->instagram_link = $this->instagram;
        $restaurant->twitter_link = $this->twitter;
        $restaurant->customer_site_language = 'en';
        $restaurant->save();

        $branch = Branch::create([
            'name' => $this->branchName,
            'restaurant_id' => $restaurant->id,
            'address' => $this->address,
        ]);

        $user = User::create([
            'name' => $this->fullName,
            'email' => $this->email,
            'phone_number' => $this->restaurantPhoneNumber,
            'phone_code' => $this->restaurantPhoneCode,
            'password' => bcrypt($this->password),
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'twitter' => $this->twitter,
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branch->id,
        ]);

        $adminRole = Role::create(['name' => 'Admin_' . $restaurant->id, 'display_name' => 'Admin', 'guard_name' => 'web', 'restaurant_id' => $restaurant->id]);
        $branchHeadRole = Role::create(['name' => 'Branch Head_' . $restaurant->id, 'display_name' => 'Branch Head', 'guard_name' => 'web', 'restaurant_id' => $restaurant->id]);

        Role::create(['name' => 'Waiter_' . $restaurant->id, 'display_name' => 'Waiter', 'guard_name' => 'web', 'restaurant_id' => $restaurant->id]);
        Role::create(['name' => 'Chef_' . $restaurant->id, 'display_name' => 'Chef', 'guard_name' => 'web', 'restaurant_id' => $restaurant->id]);

        $allPermissions = Permission::get()->pluck('name')->toArray();

        $adminRole->syncPermissions($allPermissions);
        $branchHeadRole->syncPermissions($allPermissions);

        $user->assignRole('Admin_' . $restaurant->id);


        try {
            $user->notify(new WelcomeRestaurantEmail($restaurant, $this->password));
        } catch (\Exception $e) {
            \Log::error('Error sending restaurant welcome email: ' . $e->getMessage());
        }

        return $this->redirect(route('superadmin.restaurants.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.forms.add-restaurant', [
            'phonecodes' => $this->filteredPhoneCodes,
        ]);
    }

    /**
     * Validate the subdomain input
     *
     * @return bool Returns true if validation passes, false otherwise
     */
    private function validateSubdomain()
    {
        // Skip validation if Subdomain module is not enabled
        if (!module_enabled('Subdomain')) {
            return true;
        }


        // Validate domain or subdomain based on input
        if (empty($this->domain)) {
            $this->validate([
                'sub_domain' => 'required|string'
            ]);
            // For custom domains, we don't need to validate the domain field
            // as it's intentionally empty for custom domains
            // Just continue with the subdomain validation below
        } else {
            $this->validate([
                'sub_domain' => 'required|min:3|max:50|regex:/^[a-z0-9\-_]{2,20}$/|banned_sub_domain',
            ]);
        }


        // Check if subdomain is already in use
        $fullSubdomain = strtolower($this->sub_domain . $this->domain);
        if (Restaurant::where('sub_domain', $fullSubdomain)->exists()) {
            $this->addError('sub_domain', __('subdomain::app.messages.subdomainAlreadyExists'));
            return false;
        }

        return true;
    }

}
