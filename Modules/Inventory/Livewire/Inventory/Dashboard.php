<?php

namespace Modules\Inventory\Livewire\Inventory;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\MenuItem;
use Modules\Inventory\Entities\InventoryItem;
use Modules\Inventory\Entities\InventoryItemCategory;
use Carbon\Carbon;
use Modules\Inventory\Entities\InventoryMovement;

class Dashboard extends Component
{
    public $selectedCategory = 'all';
    public $selectedPeriod = 'daily';
    public $stockLevels = [];
    public $topMovingItems = [];
    public $lowStockItems = [];
    public $stockStatus = [];
    public $salesStockCorrelation = [];
    public $expiringStockItems = [];

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->stockLevels = $this->getCurrentStockLevels();
        $this->topMovingItems = $this->getTopMovingItems();
        $this->lowStockItems = $this->getLowStockItems();
        $this->stockStatus = $this->getStockStatus();
        $this->salesStockCorrelation = $this->getSalesStockCorrelation();
        $this->expiringStockItems = $this->getExpiringStockItems();
    }

    private function getExpiringStockItems()
    {
        return InventoryMovement::query()
            ->with(['item'])
            ->where('transaction_type', InventoryMovement::TRANSACTION_TYPE_STOCK_ADDED)
            ->where('expiration_date', '<=', now()->addDays(7))
            ->orderBy('expiration_date', 'asc')
            ->get();
    }

    private function getCurrentStockLevels()
    {
        return InventoryItem::query()
            ->when($this->selectedCategory !== 'all', function ($query) {
                $query->where('inventory_item_category_id', $this->selectedCategory);
            })
            ->with(['category', 'stocks'])
            ->get()
            ->groupBy('inventory_item_category_id')
            ->map(function ($items) {
                $firstItem = $items->first();
                $itemCount = $items->count();

                // Fix low stock count calculation
                $lowStockCount = $items->filter(function ($item) {
                    $itemStock = $item->stocks->sum('quantity');
                    return $itemStock <= $item->threshold_quantity && $itemStock > 0;
                })->count();

                // Add out of stock count
                $outOfStockCount = $items->filter(function ($item) {
                    return $item->stocks->sum('quantity') <= 0;
                })->count();

                return [
                    'category' => $firstItem->category?->name,
                    'stock' => $itemCount,
                    'low_stock_count' => $lowStockCount,
                    'out_of_stock_count' => $outOfStockCount,
                    'status' => $this->getItemStockStatus($itemCount, $lowStockCount, $outOfStockCount)
                ];
            })->values();
    }

    private function getTopMovingItems()
    {
        $query = InventoryItem::query()
            ->with(['stocks', 'category', 'unit'])
            ->withSum(['movements as total_movement' => function ($query) {
                $query->where('transaction_type', InventoryMovement::TRANSACTION_TYPE_ORDER_USED);

                if ($this->selectedPeriod === 'daily') {
                    $query->whereDate('created_at', Carbon::today());
                } elseif ($this->selectedPeriod === 'weekly') {
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                } elseif ($this->selectedPeriod === 'monthly') {
                    $query->whereMonth('created_at', Carbon::now()->month);
                }
            }], 'quantity')
            ->withSum(['movements as total_waste' => function ($query) {
                $query->where('transaction_type', InventoryMovement::TRANSACTION_TYPE_WASTE)
                    ->when($this->selectedPeriod === 'daily', function ($query) {
                        $query->whereDate('created_at', Carbon::today());
                    })
                    ->when($this->selectedPeriod === 'weekly', function ($query) {
                        $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    })
                    ->when($this->selectedPeriod === 'monthly', function ($query) {
                        $query->whereMonth('created_at', Carbon::now()->month);
                    });
            }], 'quantity');

        if ($this->selectedCategory !== 'all') {
            $query->where('inventory_item_category_id', $this->selectedCategory);
        }

        return $query->orderByDesc('total_movement')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'category' => $item->category?->name,
                    'usage' => abs($item->total_movement ?? 0),
                    'waste' => abs($item->total_waste ?? 0),
                    'current_stock' => $item->stocks->sum('quantity'),
                    'unit' => $item->unit->symbol ?? ''
                ];
            });
    }

    private function getLowStockItems()
    {
        return InventoryItem::query()
            ->with(['stocks', 'category', 'unit'])
            ->get()
            ->filter(function ($item) {
                return $item->stocks->sum('quantity') <= $item->threshold_quantity;
            })
            ->map(function ($item) {
                $currentStock = $item->stocks->sum('quantity');
                $status = $this->calculateStockStatus($currentStock, $item->threshold_quantity);
                return [
                    'name' => $item->name,
                    'current_stock' => $currentStock,
                    'threshold' => $item->threshold_quantity,
                    'category' => $item->category?->name,
                    'status' => $status['status'],
                    'status_class' => $status['class'],
                    'unit' => $item->unit->symbol ?? ''
                ];
            });
    }

    private function getStockStatus()
    {
        return InventoryItem::query()
            ->with(['stocks', 'movements'])
            ->get()
            ->map(function ($item) {
                $status = $item->getStockStatus();
                return [
                    'name' => $item->name,
                    'current_stock' => $item->stocks->sum('quantity'),
                    'movements_count' => $item->movements->count(),
                    'status' => $status['status'],
                    'status_class' => $status['class']
                ];
            })
            ->take(10);
    }

    private function calculateStockStatus($currentStock, $threshold)
    {
        if ($currentStock <= 0) {
            return [
                'status' => 'Out of Stock',
                'class' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
            ];
        } elseif ($currentStock <= $threshold) {
            return [
                'status' => 'Low Stock',
                'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
            ];
        }
        return [
            'status' => 'In Stock',
            'class' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
        ];
    }

    private function getSalesStockCorrelation()
    {
        $query = InventoryItem::query()
            ->with(['stocks', 'category', 'unit'])
            ->withSum(['movements as usage' => function ($query) {
                $query->where('transaction_type', InventoryMovement::TRANSACTION_TYPE_ORDER_USED);

                if ($this->selectedPeriod === 'daily') {
                    $query->whereDate('created_at', Carbon::today());
                } elseif ($this->selectedPeriod === 'weekly') {
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                } elseif ($this->selectedPeriod === 'monthly') {
                    $query->whereMonth('created_at', Carbon::now()->month);
                }
            }], 'quantity')
            ->withSum(['movements as stock_added' => function ($query) {
                $query->where('transaction_type', InventoryMovement::TRANSACTION_TYPE_STOCK_ADDED);
            }], 'quantity');

        if ($this->selectedCategory !== 'all') {
            $query->where('inventory_item_category_id', $this->selectedCategory);
        }

        return $query->orderByDesc('usage')
            ->take(10)
            ->get()
            ->map(function ($item) {
                $currentStock = $item->stocks->sum('quantity');
                return [
                    'name' => $item->name,
                    'category' => $item->category?->name,
                    'usage' => abs($item->usage ?? 0),
                    'stock_added' => $item->stock_added ?? 0,
                    'current_stock' => $currentStock,
                    'status' => $this->calculateStockStatus($currentStock, $item->threshold_quantity),
                    'unit' => $item->unit->symbol ?? ''
                ];
            });
    }

    private function getItemStockStatus($itemCount, $lowStockCount, $outOfStockCount)
    {
        if ($outOfStockCount > 0) return 'out-of-stock';
        if ($lowStockCount > 0) return 'low-stock';
        return 'adequate';
    }

    public function updatedSelectedCategory()
    {
        $this->loadDashboardData();
    }

    public function updatedSelectedPeriod()
    {
        $this->loadDashboardData();
    }

    public function render()
    {
        $categories = InventoryItemCategory::all();

        return view('inventory::livewire.inventory.dashboard', [
            'categories' => $categories
        ]);
    }
}
