<?php

namespace Modules\Inventory\Livewire\Supplier;

use Livewire\Component;
use Modules\Inventory\Entities\Supplier;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddSupplier extends Component
{
    use LivewireAlert;

    public $name;
    public $email;
    public $phone;
    public $address;

    public function submitForm()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
        ]);

        Supplier::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        $this->dispatch('hideAddSupplier');

        $this->alert('success', __('inventory::modules.supplier.supplierAdded'));
    }
    
    public function render()
    {
        return view('inventory::livewire.supplier.add-supplier');
    }
}
