<?php

namespace Modules\Inventory\Livewire\Supplier;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Inventory\Entities\Supplier;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SupplierList extends Component
{
    use WithPagination;
    use LivewireAlert;

    public $search = '';
    public $supplier;
    public $showEditSupplierModal = false;
    public $confirmDeleteSupplierModal = false;
    public $showAddSupplierModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function createSupplier()
    {
        $this->showAddSupplierModal = true;
    }

    public function editSupplier($supplierId)
    {
        $this->supplier = Supplier::find($supplierId);
        $this->showEditSupplierModal = true;
    }

    #[On('hideEditSupplier')]
    public function hideEditSupplier()
    {
        $this->showEditSupplierModal = false;
    }

    #[On('hideAddSupplier')]
    public function hideAddSupplier()
    {
        $this->showAddSupplierModal = false;
    }

    public function deleteSupplier($supplierId)
    {
        if ($this->confirmDeleteSupplierModal) {
            Supplier::destroy($supplierId);
            $this->alert('success', __('inventory::modules.supplier.supplierDeleted'));
            $this->confirmDeleteSupplierModal = false;
            $this->supplier = null;
        } else {
            $this->supplier = Supplier::find($supplierId);
            $this->confirmDeleteSupplierModal = true;
        }
    }

    public function render()
    {
        return view('inventory::livewire.supplier.supplier-list');
    }
}
