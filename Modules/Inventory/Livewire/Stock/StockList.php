<?php

namespace Modules\Inventory\Livewire\Stock;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Modules\Inventory\Entities\InventoryItem;
use Modules\Inventory\Entities\InventoryItemCategory;
use Illuminate\Support\Facades\DB;

class StockList extends Component
{
    use WithPagination;

    public $showAddStockEntry = false;
    public $search = '';
    public $category = '';
    public $stockStatus = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'stockStatus' => ['except' => ''],
    ];

    #[On('hideAddStockEntryModal')]
    public function hideAddStockEntryModal()
    {
        $this->showAddStockEntry = false;
    }

    #[On('stockUpdated')]
    public function refreshStock()
    {
        // Will automatically refresh due to Livewire's reactive nature
    }

    public function getStockStatistics()
    {
        $items = InventoryItem::with(['stocks'])
            ->where('inventory_items.branch_id', branch()->id)
            ->get();

        $stats = [
            'available_items' => 0,
            'low_stock' => 0,
            'out_of_stock' => 0,
            'total_cost' => 0
        ];

        foreach ($items as $item) {
            if ($item->current_stock <= 0) {
                $stats['out_of_stock']++;
            } elseif ($item->current_stock <= $item->threshold_quantity) {
                $stats['low_stock']++;
            } else {
                $stats['available_items']++;
            }
            $stats['total_cost'] += $item->unit_purchase_price * $item->current_stock;
        }

        return $stats;
    }

    public function getStockItems()
    {
        // Set MySQL to non-strict mode for this query
        DB::statement("SET SESSION sql_mode=''");

        $query = InventoryItem::with(['category', 'unit', 'stocks'])
            ->where('inventory_items.branch_id', branch()->id)
            ->select('inventory_items.*')
            ->selectRaw('COALESCE(SUM(inventory_stocks.quantity), 0) as current_stock')
            ->leftJoin('inventory_stocks', function($join) {
                $join->on('inventory_items.id', '=', 'inventory_stocks.inventory_item_id')
                    ->where('inventory_stocks.branch_id', '=', branch()->id);
            })
            ->groupBy('inventory_items.id');

        // Apply search filter
        if ($this->search) {
            $query->where('inventory_items.name', 'like', '%' . $this->search . '%');
        }

        // Apply category filter
        if ($this->category) {
            $query->where('inventory_items.inventory_item_category_id', $this->category);
        }

        // Apply stock status filter
        if ($this->stockStatus) {
            switch ($this->stockStatus) {
                case 'in_stock':
                    $query->havingRaw('current_stock > inventory_items.threshold_quantity');
                    break;
                case 'low_stock':
                    $query->havingRaw('current_stock > 0 AND current_stock <= inventory_items.threshold_quantity');
                    break;
                case 'out_of_stock':
                    $query->havingRaw('current_stock <= 0');
                    break;
            }
        }

        $result = $query->paginate($this->perPage);

        // Reset SQL mode back to default after query execution
        DB::statement("SET SESSION sql_mode=(SELECT @@global.sql_mode)");

        return $result;
    }

    public function getCategories()
    {
        return InventoryItemCategory::where('inventory_item_categories.branch_id', branch()->id)->get();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'category', 'stockStatus']);
        $this->resetPage();
    }

    public function render()
    {
        return view('inventory::livewire.stock.stock-list', [
            'stats' => $this->getStockStatistics(),
            'stockItems' => $this->getStockItems(),
            'categories' => $this->getCategories(),
        ]);
    }
}
