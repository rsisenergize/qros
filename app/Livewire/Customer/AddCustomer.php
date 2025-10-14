<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Livewire\Component;
use App\Models\Customer;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Country;
use App\Helper\Common;

class AddCustomer extends Component
{
    use LivewireAlert;

    public $order;
    public $customerName;
    public $customerPhone;
    public $customerEmail;
    public $availableResults = [];
    public $customerAddress;
    public $showAddCustomerModal = false;
    public $fromPos;
    public $selectedCustomerId = null;
    public $customerPhoneCode;
    public $phoneCodeSearch = '';
    public $phoneCodeIsOpen = false;
    public $allPhoneCodes;
    public $filteredPhoneCodes;
    public $searchQuery = '';
    public $editingFields = [
        'name' => false,
        'phone' => false,
        'email' => false,
        'address' => false
    ];


    public function mount()
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
    
    #[On('showAddCustomerModal')]
    public function showAddCustomer($id = null, $customerId = null, $fromPos = false)
    {
        if (!is_null($id)) {
            $this->order = Order::find($id);
        }

        if (!is_null($customerId)) {
            $customer = Customer::find($customerId);
            if ($customer) {
                $this->customerName = $customer->name;
                $this->customerPhone = $customer->phone;
                $this->customerPhoneCode = $customer->phone_code;
                $this->customerEmail = $customer->email;
                $this->customerAddress = $customer->delivery_address;
            }
        }
        $this->fromPos = $fromPos ?? false;
        $this->showAddCustomerModal = true;
    }

    public function updatedSearchQuery()
    {
        if (strlen($this->searchQuery) >= 2) {
            $this->availableResults = $this->fetchSearchResults();
        } else {
            $this->availableResults = [];
        }
    }

    public function updatedCustomerPhone()
    {
        if (strlen($this->customerPhone) >= 2) {
            $this->availableResults = $this->fetchSearchResults();
        } else {
            $this->availableResults = [];
        }
    }

    public function fetchSearchResults()
    {
        $searchTerm = $this->searchQuery ?: $this->customerPhone;

        if (empty($searchTerm)) {
            return collect();
        }

        $results = Customer::where('restaurant_id', restaurant()->id)
            ->where(function ($query) use ($searchTerm) {
                $safeTerm = Common::safeString($searchTerm);
                $query->where('name', 'like', '%' . $safeTerm . '%')
                    ->orWhere('phone', 'like', '%' . $safeTerm . '%')
                    ->orWhere('email', 'like', '%' . $safeTerm . '%');
            })
            ->orderBy('name')
            ->limit(10)
            ->get();

        return $results;
    }

    public function selectCustomer($customerId)
    {
        $customer = Customer::find($customerId);

        if ($customer) {
            $this->selectedCustomerId = $customer->id;
            $this->customerName = $customer->name;
            $this->customerPhone = $customer->phone;
            $this->customerPhoneCode = $customer->phone_code;
            $this->customerEmail = $customer->email;
            $this->customerAddress = $customer->delivery_address;
            $this->searchQuery = ''; // Clear the search query
            $this->availableResults = []; // Clear the results

            // Reset all fields to readonly when selecting a customer
            $this->editingFields = [
                'name' => false,
                'phone' => false,
                'email' => false,
                'address' => false
            ];
        }
    }

    public function createNewCustomer()
    {
        // Store the search query before clearing it
        $searchTerm = $this->searchQuery;

        // Clear the search and focus on creating a new customer
        $this->searchQuery = '';
        $this->availableResults = [];
        $this->selectedCustomerId = null;

        // Make all fields editable when creating new customer
        $this->editingFields = [
            'name' => true,
            'phone' => true,
            'email' => true,
            'address' => true
        ];

        // Pre-fill the name field with the search term if it looks like a name
        if (!empty($searchTerm) && !preg_match('/\d/', $searchTerm)) {
            $this->customerName = $searchTerm;
        }
    }

    public function clearSelection()
    {
        // Clear the selected customer but keep the form data
        $this->selectedCustomerId = null;
        $this->searchQuery = '';
        $this->availableResults = [];

        // Make all fields editable when creating new customer
        $this->editingFields = [
            'name' => true,
            'phone' => true,
            'email' => true,
            'address' => true
        ];
    }

    public function toggleFieldEdit($field)
    {
        if (isset($this->editingFields[$field])) {
            $this->editingFields[$field] = !$this->editingFields[$field];
        }
    }

    public function submitForm()
    {
        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerPhoneCode' => 'required',
            'customerPhone' => 'required',
            'customerEmail' => 'nullable|email',
            'customerAddress' => 'nullable|string|max:500',
        ]);

        // Check for existing customer by email or phone
        $existingCustomer = null;
        
        if (!empty($this->customerEmail)) {
            $existingCustomer = Customer::where('restaurant_id', restaurant()->id)
                ->where('email', $this->customerEmail)
                ->first();
        }
        
        if (!$existingCustomer && !empty($this->customerPhone)) {
            $existingCustomer = Customer::where('restaurant_id', restaurant()->id)
                ->where('phone', $this->customerPhone)
                ->first();
        }

        $customerData = [
            'name' => $this->customerName,
            'phone' => $this->customerPhone,
            'phone_code' => $this->customerPhoneCode,
        ];

        foreach (
            [
                'phone' => $this->customerPhone,
                'email' => $this->customerEmail,
                'delivery_address' => $this->customerAddress
            ] as $field => $value
        ) {
            if (!empty($value)) {
                $customerData[$field] = $value;
            }
        }

        if (!empty($this->customerAddress)) {
            $customerData['delivery_address'] = $this->customerAddress;
        }

        // Update existing customer or create new one
        if ($existingCustomer) {
            $customer = tap($existingCustomer)->update($customerData);
        } else {
            $customerData['restaurant_id'] = restaurant()->id;
            $customer = Customer::create($customerData);
        }

        if (!is_null($this->order)) {
            $this->order->customer_id = $customer->id;
            $this->order->delivery_address = $this->customerAddress;
            $this->order->save();

            if (!$this->fromPos) {
                $this->dispatch('showOrderDetail', id: $this->order->id);
            }
            $this->dispatch('refreshOrders');
            $this->dispatch('refreshPos');
        }
        // Case 2: From POS (before order creation)
        else {
            $this->dispatch('customerSelected', $customer->id);
        }

        $this->resetForm();
    }

    public function resetSearch()
    {
        $this->availableResults = [];
        $this->searchQuery = '';
    }

    public function resetForm()
    {
        $this->customerName = '';
        $this->customerPhone = '';
        $this->customerPhoneCode = restaurant()->phone_code ?? $this->allPhoneCodes->first();
        $this->customerEmail = '';
        $this->customerAddress = '';
        $this->searchQuery = '';
        $this->availableResults = [];
        $this->selectedCustomerId = null;
        $this->editingFields = [
            'name' => false,
            'phone' => false,
            'email' => false,
            'address' => false
        ];
        $this->showAddCustomerModal = false;
    }

    public function render()
    {
        return view('livewire.customer.add-customer', [
            'phonecodes' => $this->filteredPhoneCodes,
        ]   );
    }
}
