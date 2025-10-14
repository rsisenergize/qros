<?php

namespace Modules\Inventory\Livewire\PurchaseOrder;

use Livewire\Component;
use Modules\Inventory\Entities\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ReceivePurchaseOrder extends Component
{
    use LivewireAlert;

    public $showModal = false;
    public $purchaseOrder;
    public $items = [];
    
    protected $listeners = ['showReceiveModal' => 'showModal'];

    public function showModal(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
        $this->items = $purchaseOrder->items->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->inventoryItem->name,
                'quantity' => $item->quantity,
                'received_quantity' => $item->received_quantity,
                'receiving_quantity' => 0,
            ];
        })->toArray();
        
        $this->showModal = true;
    }

    public function receive()
    {
        $this->validate([
            'items.*.receiving_quantity' => 'required|numeric|min:0',
        ], [], [
            'items.*.receiving_quantity' => trans('inventory::modules.purchaseOrder.receiving_quantity'),
        ]);

        DB::transaction(function () {
            $allReceived = true;
            
            foreach ($this->items as $item) {
                if ($item['receiving_quantity'] > 0) {
                    $poItem = $this->purchaseOrder->items()->find($item['id']);
                    $newReceivedQuantity = $poItem->received_quantity + $item['receiving_quantity'];
                    
                    if ($newReceivedQuantity < $poItem->quantity) {
                        $allReceived = false;
                    }

                    $poItem->update(['received_quantity' => $newReceivedQuantity]);

                    // Create inventory movement
                    $poItem->inventoryItem->movements()->create([
                        'branch_id' => branch()->id,
                        'quantity' => $item['receiving_quantity'],
                        'transaction_type' => 'in',
                        'supplier_id' => $this->purchaseOrder->supplier_id,
                        'added_by' => user()->id,
                    ]);

                    // Update or create inventory stock
                    $poItem->inventoryItem->stocks()->updateOrCreate(
                        ['branch_id' => branch()->id],
                        [
                            'quantity' => DB::raw('quantity + ' . $item['receiving_quantity'])
                        ]
                    );

                    // Update menu item in_stock status
                    $poItem->inventoryItem->menuItems()->update([
                        'in_stock' => 1
                    ]);
                }
            }

            $this->purchaseOrder->update([
                'status' => $allReceived ? 'received' : 'partially_received'
            ]);
        });

        $this->showModal = false;
        $this->dispatch('purchaseOrderSaved');
        $this->alert('success', trans('inventory::modules.purchaseOrder.items_received'));
    }

    public function render()
    {
        return view('inventory::livewire.purchase-order.receive-purchase-order');
    }
} 