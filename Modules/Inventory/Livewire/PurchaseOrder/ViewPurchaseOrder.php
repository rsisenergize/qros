<?php

namespace Modules\Inventory\Livewire\PurchaseOrder;

use Livewire\Component;
use Modules\Inventory\Entities\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class ViewPurchaseOrder extends Component
{
    public $showModal = false;
    public $purchaseOrder;

    protected $listeners = ['viewPurchaseOrder' => 'show'];

    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder->load(['supplier', 'items.inventoryItem.unit']);
        $this->showModal = true;
    }

    public function downloadPdf()
    {
        $pdf = PDF::loadView('inventory::pdfs.purchase-order', [
            'purchaseOrder' => $this->purchaseOrder
        ]);

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, "PO-{$this->purchaseOrder->po_number}.pdf");
    }

    public function render()
    {
        return view('inventory::livewire.purchase-order.view-purchase-order');
    }
} 