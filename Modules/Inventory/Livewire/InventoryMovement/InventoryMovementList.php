<?php

namespace Modules\Inventory\Livewire\InventoryMovement;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Modules\Inventory\Entities\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\InventoryItemCategory;
use Livewire\Attributes\On;

class InventoryMovementList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';
    public $category = '';
    public $dateRange = 'month';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $showAddStockEntry = false;
    public $showViewModal = false;
    public $showEditModal = false;
    public $selectedMovement;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'category' => ['except' => ''],
        'dateRange' => ['except' => 'month'],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function viewDetails($movementId)
    {
        $this->selectedMovement = InventoryMovement::findOrFail($movementId);
        $this->showViewModal = true;
    }

    #[On('showEditMovementModal')]
    public function edit($movementId)
    {
        $this->selectedMovement = InventoryMovement::findOrFail($movementId);
        $this->showViewModal = false;
        $this->showEditModal = true;
    }

    #[On('hideAddStockEntryModal')]
    public function hideAddStockEntryModal()
    {
        $this->showAddStockEntry = false;
    }

    #[On('hideEditMovementModal')]
    public function hideEditModal()
    {
        $this->showEditModal = false;
        $this->selectedMovement = null;
    }

    private function getDateRangeFilter()
    {
        return match($this->dateRange) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'quarter' => Carbon::now()->startOfQuarter(),
            default => Carbon::now()->startOfMonth(),
        };
    }

    private function getMovementsQuery()
    {
        $dateFilter = $this->getDateRangeFilter();

        $query = InventoryMovement::with(['item', 'item.unit', 'item.category', 'addedBy'])
            ->where('branch_id', branch()->id)
            ->where('created_at', '>=', $dateFilter);

        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function($q) use ($search) {
                $q->whereHas('item', function($q) use ($search) {
                    $q->where('name', 'like', $search);
                })
                ->orWhereHas('addedBy', function($q) use ($search) {
                    $q->where('name', 'like', $search);
                })
                ->orWhereHas('item.category', function($q) use ($search) {
                    $q->where('name', 'like', $search);
                });
            });
        }

        if ($this->filterType) {
            $query->where('transaction_type', $this->filterType);
        }

        if ($this->category) {
            $query->whereHas('item', function($q) {
                $q->where('inventory_item_category_id', $this->category);
            });
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    private function calculateStats($query)
    {
        return [
            'totalStockIn' => $query->clone()->where('transaction_type', 'in')->sum('quantity'),
            'totalStockOut' => $query->clone()->where('transaction_type', 'out')->sum('quantity'),
            'totalWaste' => $query->clone()->where('transaction_type', 'waste')->sum('quantity'),
            'totalTransfers' => $query->clone()->where('transaction_type', 'transfer')->sum('quantity'),
            'totalMovements' => $query->clone()->count(),
        ];
    }

    public function render()
    {
        $query = $this->getMovementsQuery();
        $stats = $this->calculateStats($query);
        
        $movements = $query->paginate(10);
        
        // Fetch categories for the current branch
        $categories = InventoryItemCategory::where('branch_id', branch()->id)->get();

        return view('inventory::livewire.inventory-movement.inventory-movement-list', [
            'movements' => $movements,
            'categories' => $categories,
            'totalStockIn' => $stats['totalStockIn'],
            'totalStockOut' => $stats['totalStockOut'],
            'totalWaste' => $stats['totalWaste'],
            'totalTransfers' => $stats['totalTransfers'],
            'totalMovements' => $stats['totalMovements'],
        ]);
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingDateRange()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterType', 'category', 'dateRange']);
        $this->dateRange = 'month'; // Reset to default value
        $this->resetPage();
    }
}
