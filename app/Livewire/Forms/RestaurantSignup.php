<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Country;
use Livewire\Component;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Notifications\NewRestaurantSignup;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WelcomeRestaurantEmail;
use Spatie\Permission\Models\Permission;

class RestaurantSignup extends Component
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
    public $showUserForm = true;
    public $showBranchForm = false;
    public $phone;
    public $phoneCode;
    public $restaurantPhoneCode;
    public $restaurantPhoneNumber;
    public $fullNumber;
    public $phoneCodeSearch = '';
    public $phoneCodeIsOpen = false;
    public $allPhoneCodes;
    public $filteredPhoneCodes;
    public $phoneCodeDetected = false;

    public function mount()
    {
        if (user()) {
            return redirect('dashboard');
        }

        // Load all countries once with eager loading of needed attributes
        $allCountries = Country::select(['id', 'countries_name', 'countries_code', 'phonecode'])->get();
        $this->countries = $allCountries;

        // Get country from IP and set default phone code
        $user = new User();
        $ipCountry = $user->getCountryFromIp();
        $defaultPhoneCode = $user->getPhoneCodeFromIp();

        // Use collection methods to find default country instead of another query
        $defaultCountry = $allCountries->where('countries_code', $ipCountry)->first();

        if ($defaultCountry && $defaultPhoneCode) {
            $this->country = $defaultCountry->id;
            $this->restaurantPhoneCode = $defaultPhoneCode;
            $this->phoneCodeDetected = true;
        } else {
            // Fallback to first country if IP detection fails
            $this->country = $this->countries->first()->id;
            $this->restaurantPhoneCode = $this->countries->first()->phonecode;
            $this->phoneCodeDetected = false;
        }

        $this->allPhoneCodes = $allCountries->pluck('phonecode')->unique()->filter()->values();
        $this->filteredPhoneCodes = $this->allPhoneCodes;
    }

    public function submitForm()
    {

        if (module_enabled('Subdomain')) {

            $this->validate([
                'sub_domain' => module_enabled('Subdomain') ? 'regex:/^[a-z0-9-_]{2,20}$/|required|banned_sub_domain|min:3|max:50' : '',
            ]);

            $restaurant = Restaurant::where('sub_domain', strtolower($this->sub_domain . '.' . getDomain()))->exists();

            if ($restaurant) {
                $this->addError('sub_domain', __('subdomain::app.messages.subdomainAlreadyExists'));
                return;
            }
        }

        $this->validate([
            'restaurantName' => 'required',
            'fullName' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'restaurantPhoneCode' => 'required',
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


        $requiresApproval = global_setting()->requires_approval_after_signup;
        $restaurant = new Restaurant();
        $restaurant->name = $this->restaurantName;

        if (module_enabled('Subdomain')) {
            $restaurant->sub_domain = strtolower(trim($this->sub_domain, '.') . '.' . getDomain());
        }

        // $fullPhone = '+' . trim($this->restaurantPhoneCode) . ' ' . trim($this->restaurantPhoneNumber);

        $restaurant->hash = md5(microtime() . rand(1, 99999999));
        $restaurant->address = $this->address;
        $restaurant->timezone = $timezone ?? 'UTC';
        $restaurant->theme_hex = global_setting()->theme_hex;
        $restaurant->theme_rgb = global_setting()->theme_rgb;
        $restaurant->email = $this->email;
        $restaurant->phone_number = $this->restaurantPhoneNumber;
        $restaurant->phone_code = $this->restaurantPhoneCode;
        $restaurant->approval_status = $requiresApproval ? 'Pending' : 'Approved';
        $restaurant->is_active = true;
        $restaurant->country_id = $this->country;
        $restaurant->about_us = Restaurant::ABOUT_US_DEFAULT_TEXT;
        $restaurant->customer_site_language = 'en';
        $restaurant->save();

        $branch = Branch::create([
            'name' => $this->branchName,
            'restaurant_id' => $restaurant->id,
            'address' => $this->address,
        ]);
        // dd($fullPhone);
        $user = User::create([
            'name' => $this->fullName,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branch->id,

            'phone_number' => $this->restaurantPhoneNumber,
            'phone_code' => $this->restaurantPhoneCode,
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

        $superadmins = User::withoutGlobalScopes()->role('Super Admin')->get();
        try {
            Notification::send($superadmins, new NewRestaurantSignup($restaurant));
        } catch (\Exception $e) {
            \Log::error('Error sending new restaurant signup notification: ' . $e->getMessage());
        }

        if (module_enabled('Subdomain')) {
            $hash = encrypt($user->id);
            cache(['quick_login_' . $user->id => $hash], now()->addMinutes(2));
            return redirect('https://' . $restaurant->sub_domain . '/quick-login/' . $hash);
        }

        $this->authLogin($user);

        return redirect(RouteServiceProvider::ONBOARDING_STEPS);
    }

    public function updatedCountry($value)
    {
        // Use the already loaded countries collection to find the country
        $country = $this->countries->firstWhere('id', $value);
        $this->phoneCode = $country->phonecode ?? null;
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

    public function render()
    {
        return view('livewire.forms.restaurant-signup', [
            'phonecodes' => $this->filteredPhoneCodes,
        ]);
    }

    public function authLogin($user)
    {
        Auth::loginUsingId($user->id);

        $restaurant = $user->restaurant;
        $branch = $user->branch;

        session(['user' => auth()->user()]);
        session(['restaurant' => $restaurant->fresh()]);
        session(['branch' => $branch]);
    }
}
