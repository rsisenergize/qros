<?php

namespace Modules\Inventory\Livewire\Supplier;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Inventory\Entities\Supplier;

class SupplierTable extends Component
{
    use WithPagination;

    public $search = '';
    public $showAddSupplierModal = false;
    public $showEditSupplierModal = false;
    public $confirmDeleteSupplierModal = false;
    public $supplier;

    protected $listeners = ['refreshSupplierTable' => '$refresh'];

    public function mount($search)
    {
        $this->search = $search;
    }

    public function editSupplier($id)
    {
        $this->supplier = Supplier::find($id);
        $this->showEditSupplierModal = true;
    }

    public function deleteSupplier($id)
    {
        if ($this->confirmDeleteSupplierModal) {
            $supplier = Supplier::find($id);
            if ($supplier && $supplier->orders_count == 0) {
                $supplier->delete();
                $this->confirmDeleteSupplierModal = false;
                $this->supplier = null;
            }
        } else {
            $this->supplier = Supplier::find($id);
            $this->confirmDeleteSupplierModal = true;
        }
    }

    public function render()
    {
        $suppliers = Supplier::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('address', 'like', '%' . $this->search . '%');
            })
            ->withCount('orders')
            ->paginate(10);

        return view('inventory::livewire.supplier.supplier-table', [
            'suppliers' => $suppliers
        ]);
    }
}
