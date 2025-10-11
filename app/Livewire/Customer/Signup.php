<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Notifications\CustomerEmailVerify;
use Illuminate\Support\Facades\Mail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

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
    public $showSignUpProcess = false;

    public function mount()
    {
        $this->customer = customer();
    }

    #[On('showSignup')]
    public function showSignup()
    {
        $this->showSignupModal = true;
    }

    public function submitForm()
    {
        $this->validate([
            'email' => 'required|email'
        ]);

        $customer = Customer::where('email', $this->email)->first();

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
                'phone' => 'required|string|unique:customers,phone'
            ]);

            $customer = new Customer();
            $customer->email = $this->email;
            $customer->restaurant_id = $this->restaurant->id;
            $customer->name = $this->name;
            $customer->phone = $this->phone;
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

        $customer = Customer::where('email', $this->email)->first();

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
            $this->customer->notify(new CustomerEmailVerify());
        } catch (\Exception $e) {
            \Log::error('Error sending email verification notification: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.customer.signup');
    }

}
