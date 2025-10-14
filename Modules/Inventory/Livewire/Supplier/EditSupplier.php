<?php

namespace Modules\Inventory\Livewire\Supplier;

use Livewire\Component;
use Modules\Inventory\Entities\Supplier;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditSupplier extends Component
{
    use LivewireAlert;

    public Supplier $supplier;

    public $name;
    public $email;
    public $phone;
    public $address;

    public function mount()
    {
        $this->name = $this->supplier->name;
        $this->email = $this->supplier->email;
        $this->phone = $this->supplier->phone;
        $this->address = $this->supplier->address;
    }

    public function submitForm()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
        ]);

        $this->supplier->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        $this->dispatch('hideEditSupplier');

        $this->alert('success', __('inventory::modules.supplier.supplierUpdated'));
    }

    public function render()
    {
        return view('inventory::livewire.supplier.edit-supplier');
    }
}
