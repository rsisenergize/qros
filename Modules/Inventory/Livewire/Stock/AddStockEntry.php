<?php

namespace Modules\Inventory\Livewire\Stock;

use Livewire\Component;
use Modules\Inventory\Entities\InventoryItem;
use Modules\Inventory\Entities\InventoryMovement;
use Modules\Inventory\Entities\Supplier;
use App\Models\Branch;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Modules\Inventory\Entities\InventoryStock;

class AddStockEntry extends Component
{
    use LivewireAlert;
    public $transactionType;
    public $inventoryItem;
    public $quantity;
    public $supplier = null;
    public $expiryDate;
    public $inventoryItems;
    public $suppliers;
    public $wasteReason;
    public $branches;
    public $branch;
    public $search = '';
    public $showDropdown = false;
    public $selectedItem = null;
    public $unitPurchasePrice = 0;
    public $expirationDate;
    public $destinationInventoryItem;
    public $destinationInventoryItems = [];

    protected $listeners = [
        'item-selected' => 'onItemSelected',
        'supplier-selected' => 'onSupplierSelected'
    ];

    public function mount()
    {
        $this->inventoryItems = InventoryItem::with('category')->get();
        $this->suppliers = Supplier::all();
        $this->branches = Branch::where('id', '!=', branch()->id)->get();
        $this->transactionType = 'IN';
    }

    public function rules()
    {
        return [
            'inventoryItem' => 'required',
            'quantity' => 'required|numeric',
            'supplier' => 'required_if:transactionType,IN',
            'wasteReason' => 'required_if:transactionType,WASTE',
            'branch' => 'required_if:transactionType,TRANSFER',
            'unitPurchasePrice' => 'required_if:transactionType,IN|numeric',
            'expirationDate' => 'required_if:transactionType,IN|date',
        ];
    }

    public function submitForm()
    {
        $this->validate(
            [
                'inventoryItem' => 'required',
                'quantity' => 'required|numeric',
                'supplier' => 'required_if:transactionType,IN',
                'wasteReason' => 'required_if:transactionType,WASTE',
                'branch' => 'required_if:transactionType,TRANSFER',
                'destinationInventoryItem' => 'required_if:transactionType,TRANSFER',
            ]
        );

        $stockEntry = new InventoryMovement();
        $stockEntry->branch_id = branch()->id;
        $stockEntry->inventory_item_id = $this->inventoryItem;
        $stockEntry->quantity = $this->quantity;
        $stockEntry->transaction_type = $this->transactionType;
        $stockEntry->supplier_id = ($this->transactionType == 'IN') ? $this->supplier : null;
        $stockEntry->waste_reason = ($this->transactionType == 'WASTE') ? $this->wasteReason : null;
        $stockEntry->transfer_branch_id = ($this->transactionType == 'TRANSFER') ? $this->branch : null;
        $stockEntry->unit_purchase_price = $this->unitPurchasePrice;
        $stockEntry->added_by = user()->id;
        $stockEntry->expiration_date = $this->expirationDate;
        $stockEntry->save();

        $updatedStock = InventoryStock::where('inventory_item_id', $this->inventoryItem)
            ->firstOrCreate([
                'inventory_item_id' => $this->inventoryItem,
                'branch_id' => branch()->id
            ]);

        if ($this->transactionType == 'IN') {
            $updatedStock->quantity += $this->quantity;

            $inventoryItem = InventoryItem::where('id', $this->inventoryItem)->first();
            $inventoryItem->menuItems()->update([
                'in_stock' => 1
            ]);

        } else {
            $updatedStock->quantity -= $this->quantity;
        }

        if ($this->transactionType == 'TRANSFER') {
            $destinationStock = InventoryStock::where('inventory_item_id', $this->destinationInventoryItem)
                ->where('branch_id', $this->branch)
                ->firstOrCreate([
                    'inventory_item_id' => $this->destinationInventoryItem,
                    'branch_id' => $this->branch
                ]);
            $destinationStock->quantity += $this->quantity;
            $destinationStock->saveQuietly();

            $destinationStockEntry = new InventoryMovement();
            $destinationStockEntry->branch_id = $this->branch;
            $destinationStockEntry->inventory_item_id = $this->destinationInventoryItem;
            $destinationStockEntry->quantity = $this->quantity;
            $destinationStockEntry->transaction_type = 'IN';
            $destinationStockEntry->supplier_id = ($this->transactionType == 'IN') ? $this->supplier : null;
            $destinationStockEntry->waste_reason = ($this->transactionType == 'WASTE') ? $this->wasteReason : null;
            $destinationStockEntry->transfer_branch_id = ($this->transactionType == 'TRANSFER') ? $this->branch : null;
            $destinationStockEntry->unit_purchase_price = $this->unitPurchasePrice;
            $destinationStockEntry->added_by = user()->id;
            $destinationStockEntry->expiration_date = $this->expirationDate;
            $destinationStockEntry->saveQuietly();
    
        }

        $updatedStock->save();

        $this->alert('success', __('inventory::modules.stock.stockEntryAddedSuccessfully'));
        $this->dispatch('hideAddStockEntryModal');
    }

    public function updatedSearch()
    {
        $this->showDropdown = strlen($this->search) > 0;
    }

    public function selectItem($itemId)
    {
        $this->inventoryItem = $itemId;
        $this->selectedItem = InventoryItem::find($itemId);
        $this->search = $this->selectedItem->name;
        $this->showDropdown = false;
    }

    public function clearSelection()
    {
        $this->inventoryItem = null;
        $this->selectedItem = null;
        $this->search = '';
        $this->showDropdown = false;
    }

    public function onItemSelected($itemId)
    {
        $this->inventoryItem = $itemId;
        $inventoryItem = InventoryItem::find($itemId);
        $this->unitPurchasePrice = $inventoryItem->unit_purchase_price;
        $this->supplier = $inventoryItem->preferred_supplier_id;
    }

    public function onSupplierSelected($supplierId)
    {
        $this->supplier = $supplierId;
    }

    public function updatedBranch($value)
    {
        if ($this->transactionType === 'TRANSFER' && $value) {
            $this->destinationInventoryItems = InventoryItem::withoutGlobalScopes()->where('branch_id', $value)->get();
        } else {
            $this->destinationInventoryItems = [];
        }
    }

    public function updatedTransactionType($value)
    {
        if ($value !== 'TRANSFER') {
            $this->destinationInventoryItems = [];
            $this->branch = null;
        }
    }

    public function render()
    {
        $searchResults = [];
        if (strlen($this->search) > 0) {
            $searchResults = InventoryItem::where('name', 'like', '%' . $this->search . '%')
                ->orWhereHas('category', function($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })
                ->take(5)
                ->get();
        }

        return view('inventory::livewire.stock.add-stock-entry', [
            'searchResults' => $searchResults
        ]);
    }
}
