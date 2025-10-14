<?php

namespace App\Livewire\Forms;

use App\Models\Customer;
use App\Models\Country;
use Livewire\Component;

class EditCustomer extends Component
{

    public $customer;
    public $customerName;
    public $customerEmail;
    public $customerPhone;
    public $customerAddress;
    public $customerPhoneCode;
    public $phoneCodeSearch = '';
    public $phoneCodeIsOpen = false;
    public $allPhoneCodes;
    public $filteredPhoneCodes;

    public function mount()
    {
        $this->customerPhone = $this->customer->phone;
        $this->customerName = $this->customer->name;
        $this->customerEmail = $this->customer->email;
        $this->customerAddress = $this->customer->delivery_address;
        $this->customerPhoneCode = $this->customer->phone_code;

        // Initialize phone codes
        $this->allPhoneCodes = collect(Country::pluck('phonecode')->unique()->filter()->values());
        $this->filteredPhoneCodes = $this->allPhoneCodes;
        
        // Set default phone code if customer doesn't have one
        if (empty($this->customerPhoneCode)) {
            $this->customerPhoneCode = restaurant()->phone_code ?? $this->allPhoneCodes->first();
        }
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
        $this->customerPhoneCode = $phonecode;
        $this->phoneCodeIsOpen = false;
        $this->phoneCodeSearch = '';
        $this->updatedPhoneCodeSearch();
    }

    public function editFrontFeature()
    {
        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerEmail' => 'nullable|email|unique:customers,email,' . $this->customer->id,
            'customerPhoneCode' => 'required',
            'customerPhone' => 'required|unique:customers,phone,' . $this->customer->id,
            'customerAddress' => 'nullable|string|max:500',
        ]);

        $this->customer->name = $this->customerName;
        $this->customer->email = $this->customerEmail ?? null;
        $this->customer->phone = $this->customerPhone;
        $this->customer->phone_code = $this->customerPhoneCode;
        $this->customer->delivery_address = $this->customerAddress;

        $this->customer->save();

        $this->dispatch('refreshCustomers');
        $this->dispatch('hideEditCustomer');
    }

    public function render()
    {
        return view('livewire.forms.edit-customer', [
            'phonecodes' => $this->filteredPhoneCodes ?? collect(),
        ]);
    }
}
