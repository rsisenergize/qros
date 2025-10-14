<?php

namespace Modules\Inventory\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Modules\Inventory\Entities\InventoryMovement;
use Modules\Inventory\Entities\Recipe;
use App\Models\OrderItem;
use App\Models\OrderItemModifierOption;

#[Layout('layouts.app')]
class UsageReport extends Component
{
    use WithPagination;

    public $period = 'weekly';
    public $startDate;
    public $endDate;
    public $searchTerm = '';
    public $chartOptions = [];

    public function mount()
    {
        $this->period = request('period', 'weekly');
        $this->startDate = request('startDate', Carbon::now()->startOfWeek()->format('Y-m-d H:i:s'));
        $this->endDate = request('endDate', Carbon::now()->endOfDay()->format('Y-m-d H:i:s'));
        $this->searchTerm = request('search', '');
        
        $this->loadReportData();
    }

    public function updatedPeriod()
    {
        $this->startDate = match($this->period) {
            'daily' => Carbon::now()->format('Y-m-d'),
            'monthly' => Carbon::now()->startOfMonth()->format('Y-m-d'),
            default => Carbon::now()->startOfWeek()->format('Y-m-d')
        };
        
        $this->endDate = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
        
        return $this->redirect(route('inventory.reports.usage', [
            'period' => $this->period,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'search' => $this->searchTerm
        ]));
    }

    public function updatedStartDate()
    {
        return $this->redirect(route('inventory.reports.usage', [
            'period' => $this->period,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'search' => $this->searchTerm
        ]));
    }

    public function updatedEndDate()
    {
        return $this->redirect(route('inventory.reports.usage', [
            'period' => $this->period,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'search' => $this->searchTerm
        ]));
    }

    public function updatedSearchTerm()
    {
        return $this->redirect(route('inventory.reports.usage', [
            'period' => $this->period,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'search' => $this->searchTerm
        ]));
    }

    public function loadReportData()
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);
        
        // Get current period data
        $currentData = $this->getMovementData($startDate, $endDate);
        $currentExpectedData = $this->getExpectedUsageData($startDate, $endDate);
        
        // Get previous period data for comparison
        $daysDiff = $startDate->diffInDays($endDate);
        $previousStart = $startDate->copy()->subDays($daysDiff);
        $previousEnd = $startDate->copy()->subDay();
        $previousData = $this->getMovementData($previousStart, $previousEnd);

        $chartOptions = [
            'chart' => [
                'height' => 420,
                'type' => 'area',
                'fontFamily' => 'Inter, sans-serif',
                'toolbar' => ['show' => false]
            ],
            'series' => [
                [
                    'name' => __('inventory::modules.reports.usage.actual_usage'),
                    'data' => $currentData['values'],
                    'color' => '#1A56DB'
                ],
                [
                    'name' => __('inventory::modules.reports.usage.expected_usage'),
                    'data' => $currentExpectedData['values'],
                    'color' => '#10B981'
                ],
                [
                    'name' => __('inventory::modules.reports.usage.previous_period'),
                    'data' => $previousData['values'],
                    'color' => '#FDBA8C'
                ]
            ],
            'xaxis' => [
                'categories' => $currentData['labels'],
                'labels' => [
                    'style' => [
                        'colors' => '#6B7280',
                        'fontSize' => '12px',
                        'fontWeight' => 500
                    ]
                ]
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'colors' => '#6B7280',
                        'fontSize' => '12px',
                        'fontWeight' => 500
                    ]
                ]
            ],
            'colors' => ['#1A56DB', '#10B981', '#FDBA8C'],
            'dataLabels' => ['enabled' => false],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'enabled' => true,
                    'opacityFrom' => 0.45,
                    'opacityTo' => 0.05
                ]
            ],
            'tooltip' => ['theme' => 'dark']
        ];

        $this->dispatch('updateChart', options: $chartOptions);
    }

    private function getMovementData($startDate, $endDate)
    {
        $query = InventoryMovement::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('transaction_type', '!=', InventoryMovement::TRANSACTION_TYPE_STOCK_ADDED);

        if ($this->searchTerm) {
            $query->whereHas('item', function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%');
            });
        }

        $periodFormat = match($this->period) {
            'weekly' => 'DATE(DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY))',
            'monthly' => 'DATE_FORMAT(created_at, "%Y-%m-01")',
            default => 'DATE(created_at)'
        };

        $movements = $query->selectRaw($periodFormat . ' as date, SUM(quantity) as total')
            ->groupByRaw($periodFormat)
            ->orderByRaw('date')
            ->get();

        $labels = [];
        $values = [];

        foreach ($movements as $movement) {
            $date = Carbon::parse($movement->date);
            $labels[] = match($this->period) {
                'weekly' => 'Week ' . $date->week . ' (' . $date->format('M d') . ')',
                'monthly' => $date->format('M Y'),
                default => $date->format('M d')
            };
            $values[] = (float) $movement->total;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function getExpectedUsageData($startDate, $endDate)
    {
        $periodFormat = match($this->period) {
            'weekly' => 'DATE(DATE_SUB(orders.date_time, INTERVAL WEEKDAY(orders.date_time) DAY))',
            'monthly' => 'DATE_FORMAT(orders.date_time, "%Y-%m-01")',
            default => 'DATE(orders.date_time)'
        };

        // Get order dates and group by period
        $orderDates = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.date_time', [$startDate, $endDate])
            ->where('orders.status', 'paid')
            ->selectRaw($periodFormat . ' as date')
            ->distinct()
            ->orderByRaw('date')
            ->get();

        $labels = [];
        $values = [];

        foreach ($orderDates as $orderDate) {
            $date = Carbon::parse($orderDate->date);
            $labels[] = match($this->period) {
                'weekly' => 'Week ' . $date->week . ' (' . $date->format('M d') . ')',
                'monthly' => $date->format('M Y'),
                default => $date->format('M d')
            };
            
            $periodStart = $date->copy();
            $periodEnd = match($this->period) {
                'weekly' => $date->copy()->endOfWeek(),
                'monthly' => $date->copy()->endOfMonth(),
                default => $date->copy()->endOfDay()
            };
            
            $periodExpectedUsage = $this->getExpectedUsageFromRecipes($periodStart, $periodEnd);
            $values[] = $periodExpectedUsage->sum();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function render()
    {
        // Get paginated movements
        $movements = $this->getPaginatedMovements();
        
        // Get expected usage data efficiently
        $expectedUsage = $this->getExpectedUsageFromRecipes($this->startDate, $this->endDate);
        
        // Get previous period data
        $previousPeriodData = $this->getPreviousPeriodData();

        return view('inventory::livewire.reports.usage-report', [
            'movements' => $movements,
            'totalUsage' => $this->getTotalUsage(),
            'previousPeriodUsage' => $previousPeriodData['usage'],
            'expectedUsage' => $expectedUsage,
            'previousExpectedUsage' => $previousPeriodData['expected'],
            'transactionTypes' => [
                'STOCK_ADDED' => InventoryMovement::TRANSACTION_TYPE_STOCK_ADDED,
                'ORDER_USED' => InventoryMovement::TRANSACTION_TYPE_ORDER_USED,
                'WASTE' => InventoryMovement::TRANSACTION_TYPE_WASTE,
                'TRANSFER' => InventoryMovement::TRANSACTION_TYPE_TRANSFER,
            ],
        ]);
    }

    private function getPaginatedMovements()
    {
        $query = InventoryMovement::query()
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('transaction_type', '!=', InventoryMovement::TRANSACTION_TYPE_STOCK_ADDED);

        $periodFormat = match($this->period) {
            'weekly' => 'DATE(DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY))',
            'monthly' => 'DATE_FORMAT(created_at, "%Y-%m-01")',
            default => 'DATE(created_at)'
        };

        return $query->select('inventory_movements.*')
            ->selectRaw($periodFormat . ' as period_date')
            ->with(['item.unit'])
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    private function getTotalUsage()
    {
        return InventoryMovement::query()
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('transaction_type', '!=', InventoryMovement::TRANSACTION_TYPE_STOCK_ADDED)
            ->sum('quantity');
    }

    private function getPreviousPeriodData()
    {
        $currentStart = Carbon::parse($this->startDate);
        $currentEnd = Carbon::parse($this->endDate);
        $daysDiff = $currentStart->diffInDays($currentEnd);

        $previousStart = $currentStart->copy()->subDays($daysDiff);
        $previousEnd = $currentStart->copy()->subDay();

        $usage = InventoryMovement::query()
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->where('transaction_type', '!=', InventoryMovement::TRANSACTION_TYPE_STOCK_ADDED)
            ->sum('quantity');

        $expected = $this->getExpectedUsageFromRecipes($previousStart, $previousEnd);

        return ['usage' => $usage, 'expected' => $expected];
    }

    private function getExpectedUsageFromRecipes($startDate, $endDate)
    {
        // Get all order data in one query
        $orderData = $this->getOrderData($startDate, $endDate);
        
        // Get all recipe data efficiently
        $recipeData = $this->getRecipeData($orderData);
        
        // Calculate expected usage
        return $this->calculateExpectedUsage($orderData, $recipeData);
    }

    private function getOrderData($startDate, $endDate)
    {
        // Get menu item orders
        $menuItemOrders = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('menu_item_variations', 'order_items.menu_item_variation_id', '=', 'menu_item_variations.id')
            ->whereBetween('orders.date_time', [$startDate, $endDate])
            ->where('orders.status', 'paid')
            ->select([
                'order_items.id as order_item_id',
                'order_items.menu_item_id',
                'order_items.menu_item_variation_id',
                'order_items.quantity'
            ])
            ->get();

        // Get modifier option orders
        $modifierOrders = OrderItemModifierOption::query()
            ->join('order_items', 'order_item_modifier_options.order_item_id', '=', 'order_items.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.date_time', [$startDate, $endDate])
            ->where('orders.status', 'paid')
            ->select([
                'order_item_modifier_options.modifier_option_id',
                'order_items.quantity'
            ])
            ->get();

        return [
            'menu_items' => $menuItemOrders,
            'modifiers' => $modifierOrders
        ];
    }

    private function getRecipeData($orderData)
    {
        // Get unique menu items and variations
        $menuItemIds = $orderData['menu_items']->pluck('menu_item_id')->unique();
        $variationIds = $orderData['menu_items']->pluck('menu_item_variation_id')->filter()->unique();
        $modifierOptionIds = $orderData['modifiers']->pluck('modifier_option_id')->unique();

        // Fetch all recipes in one query
        $recipes = Recipe::query()
            ->where(function($query) use ($menuItemIds, $variationIds, $modifierOptionIds) {
                // Menu item recipes
                $query->where(function($q) use ($menuItemIds) {
                    $q->whereIn('menu_item_id', $menuItemIds)
                      ->whereNull('modifier_option_id');
                })
                // Modifier option recipes
                ->orWhere(function($q) use ($modifierOptionIds) {
                    $q->whereIn('modifier_option_id', $modifierOptionIds)
                      ->whereNull('menu_item_id');
                });
            })
            ->with(['inventoryItem'])
            ->get();

        // Group recipes by type and ID
        $recipeGroups = [
            'menu_items' => $recipes->whereNotNull('menu_item_id')->groupBy(function($recipe) {
                return $recipe->menu_item_id . '_' . ($recipe->menu_item_variation_id ?? 'base');
            }),
            'modifiers' => $recipes->whereNotNull('modifier_option_id')->groupBy('modifier_option_id')
        ];

        return $recipeGroups;
    }

    private function calculateExpectedUsage($orderData, $recipeData)
    {
        $usage = collect();

        // Calculate menu item usage
        foreach ($orderData['menu_items'] as $orderItem) {
            $key = $orderItem->menu_item_id . '_' . ($orderItem->menu_item_variation_id ?? 'base');
            $recipes = $recipeData['menu_items']->get($key, collect());

            foreach ($recipes as $recipe) {
                $usage->push([
                    'inventory_item_id' => $recipe->inventory_item_id,
                    'quantity' => $recipe->quantity * $orderItem->quantity
                ]);
            }
        }

        // Calculate modifier usage
        foreach ($orderData['modifiers'] as $modifierOrder) {
            $recipes = $recipeData['modifiers']->get($modifierOrder->modifier_option_id, collect());

            foreach ($recipes as $recipe) {
                $usage->push([
                    'inventory_item_id' => $recipe->inventory_item_id,
                    'quantity' => $recipe->quantity * $modifierOrder->quantity
                ]);
            }
        }

        // Group and sum by inventory item
        return $usage->groupBy('inventory_item_id')
            ->map(function ($items) {
                return $items->sum('quantity');
            });
    }
} 