<?php

namespace App\Livewire\Forms;

use App\Models\Customer;
use Livewire\Component;

class EditCustomer extends Component
{

    public $customer;
    public $customerName;
    public $customerEmail;
    public $customerPhone;
    public $customerAddress;

    public function mount()
    {
        $this->customerPhone = $this->customer->phone;
        $this->customerName = $this->customer->name;
        $this->customerEmail = $this->customer->email;
        $this->customerAddress = $this->customer->delivery_address;

    }

    public function editFrontFeature()
    {
        $this->validate([
            'customerEmail' => 'required|email'
        ]);

        $this->customer->name = $this->customerName;
        $this->customer->email = $this->customerEmail;
        $this->customer->phone = $this->customerPhone;
        $this->customer->delivery_address = $this->customerAddress;

        $this->customer->save();

        $this->dispatch('refreshCustomers');
        $this->dispatch('hideEditCustomer');
    }

    public function render()
    {
        return view('livewire.forms.edit-customer');
    }

}
