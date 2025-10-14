<?php

namespace Modules\Inventory\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Modules\Inventory\Entities\InventoryItem;
use Modules\Inventory\Entities\InventoryMovement;

#[Layout('layouts.app')]
class TurnoverReport extends Component
{
    use WithPagination;

    public $period = 'weekly';
    public $startDate;
    public $endDate;
    public $searchTerm = '';
    public $chartOptions = [];

    public function mount()
    {
        // Get values from query parameters
        $this->period = request('period', 'weekly');
        $this->startDate = request('startDate', Carbon::now()->startOfWeek()->format('Y-m-d H:i:s'));
        $this->endDate = request('endDate', Carbon::now()->endOfDay()->format('Y-m-d H:i:s'));
        $this->searchTerm = request('search', '');
        
        $this->loadReportData();
    }

    public function updatedPeriod()
    {
        // Update date range based on selected period
        $this->startDate = match($this->period) {
            'daily' => Carbon::now()->format('Y-m-d'),
            'monthly' => Carbon::now()->startOfMonth()->format('Y-m-d'),
            default => Carbon::now()->startOfWeek()->format('Y-m-d') // weekly is default
        };
        
        $this->endDate = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
        
        return $this->redirect(route('inventory.reports.turnover', [
            'period' => $this->period,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'search' => $this->searchTerm
        ]));
    }

    public function updatedStartDate()
    {
        return $this->redirect(route('inventory.reports.turnover', [
            'period' => $this->period,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'search' => $this->searchTerm
        ]));
    }

    public function updatedEndDate()
    {
        return $this->redirect(route('inventory.reports.turnover', [
            'period' => $this->period,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'search' => $this->searchTerm
        ]));
    }

    public function updatedSearchTerm()
    {
        return $this->redirect(route('inventory.reports.turnover', [
            'period' => $this->period,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'search' => $this->searchTerm
        ]));
    }

    public function loadReportData()
    {
        $items = InventoryItem::query()
            ->withSum('stocks', 'quantity')
            ->withSum(['movements as total_usage' => function($query) {
                $query->where('transaction_type', InventoryMovement::TRANSACTION_TYPE_ORDER_USED)
                    ->when($this->startDate && $this->endDate, function($query) {
                        $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
                    });
            }], 'quantity')
            ->orderByDesc('total_usage')
            ->take(10)
            ->get();

        // Calculate turnover rates
        $items->transform(function($item) {
            $daysDiff = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) ?: 1;
            $usage = (float) $item->total_usage;
            $stock = (float) $item->stocks_sum_quantity ?: 1;
            
            // Monthly turnover rate
            $item->turnover_rate = ($usage / $stock) * (30 / $daysDiff);
            return $item;
        });

        $this->chartOptions = [
            'chart' => [
                'height' => 420,
                'type' => 'bar',
                'fontFamily' => 'Inter, sans-serif',
                'foreColor' => '#6B7280',
                'toolbar' => ['show' => false]
            ],
            'series' => [[
                'name' => __('inventory::modules.reports.turnover.turnover_rate'),
                'data' => $items->pluck('turnover_rate')->map(function($rate) {
                    return round($rate, 2);
                })->toArray()
            ]],
            'xaxis' => [
                'categories' => $items->pluck('name')->toArray(),
                'labels' => [
                    'style' => [
                        'colors' => ['#6B7280'],
                        'fontSize' => '14px',
                        'fontWeight' => 500,
                    ]
                ]
            ],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 4,
                    'horizontal' => true,
                    'barHeight' => '80%'
                ]
            ]
        ];

        $this->dispatch('updateTurnoverChart', options: $this->chartOptions);
    }

    public function render()
    {
        $query = InventoryItem::query()
            ->when($this->searchTerm, function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->withSum('stocks', 'quantity')
            ->withSum(['movements as total_usage' => function($query) {
                $query->where('transaction_type', InventoryMovement::TRANSACTION_TYPE_ORDER_USED)
                    ->when($this->startDate && $this->endDate, function($query) {
                        $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
                    });
            }], 'quantity')
            ->withCount(['movements as movement_count' => function($query) {
                $query->where('transaction_type', InventoryMovement::TRANSACTION_TYPE_ORDER_USED)
                    ->when($this->startDate && $this->endDate, function($query) {
                        $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
                    });
            }]);

        $items = $query->paginate(10);

        // Convert string values to float/int where needed
        $items->getCollection()->transform(function ($item) {
            $item->current_stock = (float) $item->stocks_sum_quantity;
            $item->usage_count = (float) $item->total_usage; // Total quantity used
            $item->movement_count = (int) $item->movement_count; // Number of transactions
            
            // Calculate daily usage (ensure we don't divide by zero)
            $daysDiff = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) ?: 1;
            $item->daily_usage = $item->usage_count / $daysDiff;
            
            // Calculate turnover rate (usage / average stock)
            $item->turnover_rate = $item->current_stock > 0 
                ? ($item->usage_count / $item->current_stock) * (30 / $daysDiff) // Monthly turnover rate
                : 0;
            
            return $item;
        });

        return view('inventory::livewire.reports.turnover-report', [
            'items' => $items,
            'transactionTypes' => [
                'STOCK_ADDED' => InventoryMovement::TRANSACTION_TYPE_STOCK_ADDED,
                'ORDER_USED' => InventoryMovement::TRANSACTION_TYPE_ORDER_USED,
                'WASTE' => InventoryMovement::TRANSACTION_TYPE_WASTE,
                'TRANSFER' => InventoryMovement::TRANSACTION_TYPE_TRANSFER,
            ],
        ]);
    }
} 