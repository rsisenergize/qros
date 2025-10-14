<?php

namespace App\Livewire\Forms;


use Livewire\Component;
use App\Models\Customer;
use App\Models\Country;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddCustomerForm extends Component
{
    use LivewireAlert;

    public $customerName;
    public $customerEmail;
    public $customerPhone;
    public $customerAddress;
    public $customerPhoneCode;
    public $phoneCodeSearch = '';
    public $phoneCodeIsOpen = false;
    public $allPhoneCodes;
    public $filteredPhoneCodes;

    protected $rules = [
        'customerName' => 'required|string|max:255',
        'customerEmail' => 'nullable|email|unique:customers,email',
        'customerPhoneCode' => 'required',
        'customerPhone' => 'required|unique:customers,phone',
        'customerAddress' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->initializePhoneCodes();
    }

    private function initializePhoneCodes()
    {
        // Initialize phone codes
        $this->allPhoneCodes = collect(Country::pluck('phonecode')->unique()->filter()->values());
        $this->filteredPhoneCodes = $this->allPhoneCodes;
        
        // Set default phone code from restaurant
        $this->customerPhoneCode = restaurant()->phone_code ?? $this->allPhoneCodes->first();
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

    public function submitForm()
    {
        $this->validate();

        Customer::create([
            'name' => $this->customerName,
            'email' => $this->customerEmail ?? null,
            'phone' => $this->customerPhone,
            'phone_code' => $this->customerPhoneCode,
            'delivery_address' => $this->customerAddress,
        ]);

        $this->dispatch('closeAddCustomer');

        $this->alert('success', __('messages.customerAdded'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
        
        $this->resetForm();
    }

    private function resetForm()
    {
        // Reset form fields
        $this->customerName = '';
        $this->customerEmail = '';
        $this->customerPhone = '';
        $this->customerAddress = '';
        $this->phoneCodeSearch = '';
        $this->phoneCodeIsOpen = false;
        
        // Reinitialize phone codes to avoid null issues
        $this->initializePhoneCodes();
    }

    public function render()
    {
        return view('livewire.forms.add-customer-form', [
            'phonecodes' => $this->filteredPhoneCodes ?? collect(),
        ]);
    }
}
