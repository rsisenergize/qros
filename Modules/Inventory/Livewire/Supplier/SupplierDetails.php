<?php

namespace Modules\Inventory\Livewire\Supplier;

use Livewire\Component;

class SupplierDetails extends Component
{
    public $supplier;
    public $viewPurchaseOrder = false;
    public function mount($supplier)
    {
        $this->supplier = $supplier;
    }

    public function viewOrder($orderId)
    {
        $this->viewPurchaseOrder = true;
    }

    public function render()
    {
        return view('inventory::livewire.supplier.supplier-details', [
            'statuses' => [
                'draft' => trans('inventory::modules.purchaseOrder.status.draft'),
                'sent' => trans('inventory::modules.purchaseOrder.status.sent'),
                'received' => trans('inventory::modules.purchaseOrder.status.received'),
                'partially_received' => trans('inventory::modules.purchaseOrder.status.partially_received'),
                'cancelled' => trans('inventory::modules.purchaseOrder.status.cancelled'),
            ],
        ]);
    }
}
