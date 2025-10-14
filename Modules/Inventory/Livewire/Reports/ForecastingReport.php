<?php

namespace Modules\Inventory\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Modules\Inventory\Entities\InventoryMovement;
use Modules\Inventory\Entities\InventoryItem;

#[Layout('layouts.app')]
class ForecastingReport extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $chartOptions = [];
    public $period = 30; // Default to 30 days forecast
    public $selectedItem = null;
    public $startDate;
    public $endDate;

    public function mount()
    {
        // Get values from query parameters
        $this->period = (int) request('period', 7);
        $this->selectedItem = request('item');
        $this->searchTerm = request('search', '');
        
        // Calculate dates based on period
        $this->endDate = Carbon::now()->endOfDay();
        $this->startDate = $this->endDate->copy()->subDays($this->period);
        
        $this->loadReportData();
    }

    public function updatedPeriod()
    {
        return $this->redirect(route('inventory.reports.forecasting', [
            'period' => $this->period,
            'item' => $this->selectedItem,
            'search' => $this->searchTerm
        ]));
    }

    public function updatedSelectedItem()
    {
        return $this->redirect(route('inventory.reports.forecasting', [
            'period' => $this->period,
            'item' => $this->selectedItem,
            'search' => $this->searchTerm
        ]));
    }

    public function updatedSearchTerm()
    {
        return $this->redirect(route('inventory.reports.forecasting', [
            'period' => $this->period,
            'item' => $this->selectedItem,
            'search' => $this->searchTerm
        ]));
    }

    public function loadReportData()
    {
        $forecastData = $this->getForecastData();

        $this->chartOptions = [
            'chart' => [
                'height' => 420,
                'type' => 'line',
                'fontFamily' => 'Inter, sans-serif',
                'toolbar' => ['show' => false]
            ],
            'series' => [
                [
                    'name' => __('inventory::modules.reports.forecasting.historical_usage'),
                    'data' => $forecastData['historical'],
                    'color' => '#1A56DB'
                ],
                [
                    'name' => __('inventory::modules.reports.forecasting.forecasted_usage'),
                    'data' => $forecastData['forecast'],
                    'color' => '#FDBA8C',
                    'dashArray' => 5
                ]
            ],
            'xaxis' => [
                'categories' => $forecastData['labels'],
                'labels' => ['style' => ['fontSize' => '14px', 'fontWeight' => 500]]
            ],
            'yaxis' => [
                'labels' => ['style' => ['fontSize' => '14px', 'fontWeight' => 500]]
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => [2, 2]
            ],
            'markers' => [
                'size' => 4
            ]
        ];

        $this->dispatch('updateForecastChart', options: $this->chartOptions);
    }

    private function getForecastData()
    {
        // Convert period to number of days
        $periodDays = (int) $this->period;

        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($periodDays);
        
        // Get historical data
        $query = InventoryMovement::query()
            ->where('transaction_type', InventoryMovement::TRANSACTION_TYPE_ORDER_USED)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($this->selectedItem) {
            $query->where('inventory_item_id', $this->selectedItem);
        }

        $historicalData = $query->get()
            ->groupBy(function($movement) {
                return $movement->created_at->format('Y-m-d');
            })
            ->map->sum('quantity');

        // Calculate moving average for forecast
        $values = $historicalData->values()->toArray();
        $movingAverage = $this->calculateMovingAverage($values, 7);
        
        // Project forward
        $forecastDates = [];
        $forecastValues = [];
        $currentDate = $endDate->copy();
        
        for ($i = 0; $i < $this->period; $i++) {
            $currentDate->addDay();
            $forecastDates[] = $currentDate->format('Y-m-d');
            $forecastValues[] = end($movingAverage);
        }

        return [
            'labels' => array_merge($historicalData->keys()->toArray(), $forecastDates),
            'historical' => array_merge($values, array_fill(0, count($forecastDates), null)),
            'forecast' => array_merge(array_fill(0, count($values), null), $forecastValues)
        ];
    }

    private function calculateMovingAverage($data, $window)
    {
        $result = [];
        $count = count($data);
        
        for ($i = 0; $i < $count; $i++) {
            $start = max(0, $i - $window + 1);
            $values = array_slice($data, $start, min($window, $i + 1));
            $result[] = array_sum($values) / count($values);
        }
        
        return $result;
    }

    public function render()
    {
        // Convert period to number of days
        $periodDays = (int) $this->period;

        $this->endDate = Carbon::now()->endOfDay();
        $this->startDate = $this->endDate->copy()->subDays($periodDays);
        
        $query = InventoryItem::query()
            ->when($this->searchTerm, function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->when($this->selectedItem, function ($query) {
                $query->where('id', $this->selectedItem);
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
            
            // Calculate days left based on current stock and daily usage
            if ($item->daily_usage > 0) {
                $item->days_left = round($item->current_stock / $item->daily_usage);
                // Cap maximum days to show at 999
                $item->days_left = min($item->days_left, 999);
            } else {
                $item->days_left = 999; // If no usage, show maximum days
            }
            
            return $item;
        });

        return view('inventory::livewire.reports.forecasting-report', [
            'items' => $items,
            'itemsList' => InventoryItem::orderBy('name')->get(),
            'transactionTypes' => [
                'STOCK_ADDED' => InventoryMovement::TRANSACTION_TYPE_STOCK_ADDED,
                'ORDER_USED' => InventoryMovement::TRANSACTION_TYPE_ORDER_USED,
                'WASTE' => InventoryMovement::TRANSACTION_TYPE_WASTE,
                'TRANSFER' => InventoryMovement::TRANSACTION_TYPE_TRANSFER,
            ],
        ]);
    }
} 