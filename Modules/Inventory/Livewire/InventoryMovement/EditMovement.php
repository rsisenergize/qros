<?php

namespace Modules\Inventory\Livewire\InventoryMovement;

use Livewire\Component;
use Modules\Inventory\Entities\InventoryMovement;
use Modules\Inventory\Entities\Supplier;
use App\Models\Branch;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Modules\Inventory\Entities\InventoryStock;

class EditMovement extends Component
{
    use LivewireAlert;

    public $movement;
    public $quantity;
    public $supplier;
    public $wasteReason;
    public $branch;
    public $suppliers;
    public $branches;
    public $unitPurchasePrice;
    public $expirationDate;

    public function mount(InventoryMovement $movement)
    {
        $this->movement = $movement;
        $this->quantity = $movement->quantity;
        $this->supplier = $movement->supplier_id;
        $this->wasteReason = $movement->waste_reason;
        $this->branch = $movement->transfer_branch_id;
        $this->unitPurchasePrice = $movement->unit_purchase_price;
        $this->expirationDate = $movement->expiration_date ? $movement->expiration_date->format('Y-m-d') : null;
        $this->suppliers = Supplier::all();
        $this->branches = Branch::where('id', '!=', branch()->id)->get();
    }

    public function rules()
    {
        return [
            'quantity' => 'required|numeric',
            'supplier' => 'required_if:movement.transaction_type,in',
            'wasteReason' => 'required_if:movement.transaction_type,waste',
            'branch' => 'required_if:movement.transaction_type,transfer',
            'unitPurchasePrice' => 'required_if:movement.transaction_type,in|numeric',
            'expirationDate' => 'required_if:movement.transaction_type,in|date',
        ];
    }

    public function update()
    {
        $this->validate();

        $inventoryStock = InventoryStock::where('inventory_item_id', $this->movement->inventory_item_id)->firstOrCreate([
            'inventory_item_id' => $this->movement->inventory_item_id
        ]);

        if ($this->movement->transaction_type === 'in' && $this->quantity > $this->movement->quantity){
            $inventoryStock->increment('quantity', $this->quantity - $this->movement->quantity);
        }else if ($this->movement->transaction_type === 'in' && $this->quantity < $this->movement->quantity){
            $inventoryStock->decrement('quantity', $this->movement->quantity - $this->quantity);
        }else if ($this->movement->transaction_type === 'out' && $this->quantity > $this->movement->quantity){
            $inventoryStock->decrement('quantity', $this->quantity - $this->movement->quantity);
        }else if ($this->movement->transaction_type === 'out' && $this->quantity < $this->movement->quantity){
            $inventoryStock->increment('quantity', $this->movement->quantity - $this->quantity);
        }

        $this->movement->update([
            'quantity' => $this->quantity,
            'supplier_id' => $this->supplier,
            'waste_reason' => $this->wasteReason,
            'transfer_branch_id' => $this->branch,
            'unit_purchase_price' => $this->unitPurchasePrice,
            'expiration_date' => $this->expirationDate,
        ]);
      

        $this->alert('success', __('inventory::modules.movements.movementUpdatedSuccessfully'));
        $this->dispatch('hideEditMovementModal');
        $this->dispatch('movementUpdated');
    }

    public function render()
    {
        return view('inventory::livewire.inventory-movement.edit-movement');
    }
} 