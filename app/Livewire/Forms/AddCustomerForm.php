<?php

namespace App\Livewire\Forms;


use Livewire\Component;
use App\Models\Customer;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddCustomerForm extends Component
{
    use LivewireAlert;

    public $customerName;
    public $customerEmail;
    public $customerPhone;
    public $customerAddress;

     protected $rules = [
        'customerName' => 'required|string|max:255',
        'customerEmail' => 'nullable|email|unique:customers,email',
        'customerPhone' => 'nullable|string|unique:customers,phone',
        'customerAddress' => 'nullable|string|max:500',
     ];

     public function submitForm()
     {
             $this->validate();

             Customer::create([
            'name' => $this->customerName,
            'email' => $this->customerEmail,
            'phone' => $this->customerPhone,
            'delivery_address' => $this->customerAddress,
             ]);

             $this->dispatch('closeAddCustomer');

             $this->alert('success', __('messages.customerAdded'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
             ]);
             $this->reset();
     }

     public function render()
     {
         return view('livewire.forms.add-customer-form');
     }

}
