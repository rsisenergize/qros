<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Models\Country;
use App\Notifications\CustomerEmailVerify;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Sms\Entities\SmsNotificationSetting;
use Modules\Sms\Notifications\SendCustomerVerifyOtp;

class Signup extends Component
{

    use LivewireAlert;

    public $showSignupModal = false;
    public $showVerifcationCode = false;
    public $email;
    public $customer;
    public $verificationCode;
    public $restaurant;
    public $name;
    public $phone;
    public $phoneCode;
    public $phoneCodeSearch = '';
    public $phoneCodeIsOpen = false;
    public $allPhoneCodes;
    public $filteredPhoneCodes;
    public $showSignUpProcess = false;

    public function mount()
    {
        $this->customer = customer();
        
        // Initialize phone codes
        $this->allPhoneCodes = collect(Country::pluck('phonecode')->unique()->filter()->values());
        $this->filteredPhoneCodes = $this->allPhoneCodes;
        
        // Set default phone code from restaurant
        $this->phoneCode = restaurant()->phone_code ?? $this->allPhoneCodes->first();
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

    /**
     * Check if SMS login is enabled for this restaurant
     */
    protected function isSmsLoginEnabled()
    {
        if (!in_array('Sms', restaurant_modules($this->restaurant))) {
            return false;
        }

        $notificationSetting = SmsNotificationSetting::where('restaurant_id', $this->restaurant->id)
            ->where('type', 'send_otp')
            ->where('send_sms', 'yes')
            ->first();

        return $notificationSetting && (sms_setting()->vonage_status || sms_setting()->msg91_status);
    }

    #[On('showSignup')]
    public function showSignup()
    {
        $this->showSignupModal = true;
    }

    public function submitForm()
    {
        if ($this->isSmsLoginEnabled()) {
            $this->validate([
                'phoneCode' => 'required',
                'phone' => 'required|string',
            ]);

            // Find customer by phone number when SMS is enabled
            $customer = Customer::where('phone_code', $this->phoneCode)->where('phone', $this->phone)->first();
        } else {
            $this->validate([
                'email' => 'required|email'
            ]);

            $customer = Customer::where('email', $this->email)->first();
        }

        if (!$customer && !$this->showSignUpProcess) {
            $this->showSignUpProcess = true;
            return;
        }


        if ($customer) {
            $this->customer = $customer;

            if ($this->restaurant->customer_login_required) {
                $this->sendVerification();
            } else {
                $this->setCustomerDetail($customer);
            }
        } else {
            // If customer does not exist, ask for additional details
            $this->validate([
                'name' => 'required|string',
                'phoneCode' => 'required',
                'phone' => 'required|string|unique:customers,phone',
                'email' => $this->isSmsLoginEnabled() ? 'nullable|email|unique:customers,email' : 'required|email|unique:customers,email',            
                ]);

            $customer = new Customer();
            $customer->email = $this->email;
            $customer->restaurant_id = $this->restaurant->id;
            $customer->name = $this->name;
            $customer->phone = $this->phone;
            $customer->phone_code = $this->phoneCode;
            $customer->save();

            $this->customer = $customer;

            if ($this->restaurant->customer_login_required) {
                $this->sendVerification();
            } else {
                $this->setCustomerDetail($customer);
            }
        }
    }

    public function submitVerification()
    {
        $this->validate([
            'verificationCode' => 'required'
        ]);

        // Use phone or email lookup based on SMS setting
        if ($this->isSmsLoginEnabled()) {
            $customer = Customer::where('phone_code', $this->phoneCode)
                ->where('phone', $this->phone)
                ->first();
        } else {
            $customer = Customer::where('email', $this->email)->first();
        }

        if ($customer->email_otp != $this->verificationCode) {
            $this->alert('error', __('messages.invalidVerificationCode'), [
                'toast' => false,
                'position' => 'center',
                'showCancelButton' => true,
                'cancelButtonText' => __('app.close')
            ]);

        } else {
            $this->setCustomerDetail($customer);
        }
    }

    public function setCustomerDetail($customer)
    {
        session(['customer' => $customer]);
        $this->dispatch('setCustomer', customer: $customer);

        $this->showSignupModal = false;
    }

    public function sendVerification()
    {
        $this->customer->email_otp = random_int(100000, 999999);
        $this->customer->save();

        $this->alert('success', __('messages.verificationCodeSent'), [
            'position' => 'center'
        ]);

        $this->showVerifcationCode = true;
        try {
            if ($this->isSmsLoginEnabled()){
                $this->customer->notify(new SendCustomerVerifyOtp($this->customer->email_otp));
                return;  
            }
            $this->customer->notify(new CustomerEmailVerify());
        } catch (\Exception $e) {
            Log::error('Error sending email verification notification: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.customer.signup', [
            'phonecodes' => $this->filteredPhoneCodes ?? collect(),
            'useSmsLogin' => $this->isSmsLoginEnabled(),
        ]);
    }
}
