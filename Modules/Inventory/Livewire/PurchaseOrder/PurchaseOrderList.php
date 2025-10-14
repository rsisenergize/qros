<?php

namespace Modules\Inventory\Livewire\PurchaseOrder;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Inventory\Entities\PurchaseOrder;
use Modules\Inventory\Entities\Supplier;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Modules\Inventory\Notifications\SendPurchaseOrder;

class PurchaseOrderList extends Component
{
    use WithPagination;

    public $search = '';
    public $supplierId;
    public $status = '';
    public $confirmingDeletion = false;
    public $purchaseOrderToDelete;
    public $confirmingSend = false;
    public $purchaseOrderToSend;
    public $confirmingCancel = false;
    public $purchaseOrderToCancel;

    protected $listeners = [
        'purchaseOrderSaved' => '$refresh',
        'purchaseOrderSent' => '$refresh',
        'purchaseOrderCancelled' => '$refresh',
    ];

    public function mount()
    {
        // Initialize with last 30 days by default
        $this->dateRange = now()->subDays(30)->format('Y-m-d') . ' to ' . now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSupplierId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'supplierId', 'status']);
        $this->resetPage();
    }

    public function confirmDelete(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrderToDelete = $purchaseOrder;
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        if ($this->purchaseOrderToDelete) {
            $this->purchaseOrderToDelete->delete();
            $this->dispatch('notify-success', trans('inventory::modules.purchaseOrder.deleted_successfully'));
        }

        $this->confirmingDeletion = false;
        $this->purchaseOrderToDelete = null;
    }

    public function confirmSend(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrderToSend = $purchaseOrder;
        $this->confirmingSend = true;
    }

    public function send()
    {
        if ($this->purchaseOrderToSend) {
            $this->purchaseOrderToSend->update(['status' => 'sent']);
            $this->dispatch('notify-success', trans('inventory::modules.purchaseOrder.sent_successfully'));
            $this->purchaseOrderToSend->supplier->notify(new SendPurchaseOrder($this->purchaseOrderToSend));
        }

        $this->confirmingSend = false;
        $this->purchaseOrderToSend = null;
    }

    public function confirmCancel(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrderToCancel = $purchaseOrder;
        $this->confirmingCancel = true;
    }

    public function cancel()
    {
        if ($this->purchaseOrderToCancel) {
            $this->purchaseOrderToCancel->update(['status' => 'cancelled']);
            $this->dispatch('notify-success', trans('inventory::modules.purchaseOrder.cancelled_successfully'));
        }

        $this->confirmingCancel = false;
        $this->purchaseOrderToCancel = null;
    }

    public function downloadPdf(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.inventoryItem.unit']);
        
        // Configure PDF
        $pdf = PDF::loadView('inventory::pdfs.purchase-order', [
            'purchaseOrder' => $purchaseOrder
        ])->setPaper('a4');
        
        // Set additional PDF options for better font handling
        $pdf->getDomPDF()->set_option('defaultFont', 'Arial');
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('isPhpEnabled', true);

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, "PO-{$purchaseOrder->po_number}.pdf");
    }

    protected function getStats()
    {
        return [
            'total_orders' => PurchaseOrder::where('branch_id', branch()->id)->count(),
            'pending_orders' => PurchaseOrder::where('branch_id', branch()->id)
                ->whereIn('status', ['draft', 'sent', 'partially_received'])
                ->count(),
            'completed_orders' => PurchaseOrder::where('branch_id', branch()->id)
                ->where('status', 'received')
                ->count()
        ];
    }

    public function render()
    {
        $query = PurchaseOrder::query()
            ->where('branch_id', branch()->id)
            ->with(['supplier', 'items.inventoryItem'])
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('po_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('supplier', function ($query) {
                            $query->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->supplierId, function ($query) {
                $query->where('supplier_id', $this->supplierId);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->latest();

        return view('inventory::livewire.purchase-order.purchase-order-list', [
            'purchaseOrders' => $query->paginate(10),
            'suppliers' => Supplier::where('restaurant_id', restaurant()->id)
                ->orderBy('name')
                ->get(),
            'statuses' => [
                'draft' => trans('inventory::modules.purchaseOrder.status.draft'),
                'sent' => trans('inventory::modules.purchaseOrder.status.sent'),
                'received' => trans('inventory::modules.purchaseOrder.status.received'),
                'partially_received' => trans('inventory::modules.purchaseOrder.status.partially_received'),
                'cancelled' => trans('inventory::modules.purchaseOrder.status.cancelled'),
            ],
            'stats' => $this->getStats(),
        ]);
    }
} 