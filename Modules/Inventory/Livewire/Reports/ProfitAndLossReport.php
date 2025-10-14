<?php

namespace Modules\Inventory\Livewire\Reports;

use Livewire\Component;
use Modules\Inventory\Entities\InventoryItem;
use Modules\Inventory\Entities\InventoryMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\MenuItemVariation;
use App\Models\ItemCategory;
use App\Models\OrderItemModifierOption;
use Modules\Inventory\Entities\Recipe;

class ProfitAndLossReport extends Component
{
    public $startDate;
    public $endDate;
    public $selectedCategory = 'all';
    public $reportData = [];
    public $totalProfit = 0;
    public $totalSales = 0;
    public $totalCost = 0;
    public $categories = [];

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $this->endDate = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
        $this->categories = ItemCategory::all();
        $this->generateReport();
    }

    public function generateReport()
    {
        // Get aggregated order data
        $orderData = $this->getOrderData();
        
        // Get recipe costs
        $recipeCosts = $this->getRecipeCosts($orderData);
        
        // Get modifier option costs
        $modifierCosts = $this->getModifierCosts($orderData);
        
        // Build report data
        $this->buildReportData($orderData, $recipeCosts, $modifierCosts);
        
        // Sort by profit (highest first)
        usort($this->reportData, fn($a, $b) => $b['profit'] <=> $a['profit']);
    }

    private function getOrderData()
    {
        $query = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('item_categories', 'menu_items.item_category_id', '=', 'item_categories.id')
            ->leftJoin('menu_item_variations', 'order_items.menu_item_variation_id', '=', 'menu_item_variations.id')
            ->whereBetween('orders.date_time', [$this->startDate, $this->endDate])
            ->where('orders.status', 'paid')
            ->where('menu_items.branch_id', branch()->id);

        if ($this->selectedCategory !== 'all') {
            $query->where('item_categories.id', $this->selectedCategory);
        }

        return $query->select([
            'order_items.menu_item_id',
            'order_items.menu_item_variation_id',
            'menu_items.item_name',
            'menu_item_variations.variation',
            'item_categories.id as category_id',
            DB::raw('SUM(order_items.quantity) as total_quantity_sold'),
            DB::raw('SUM(order_items.amount) as total_sales_amount'),
            DB::raw('AVG(order_items.price) as avg_unit_price')
        ])
        ->groupBy('order_items.menu_item_id', 'order_items.menu_item_variation_id', 'menu_items.item_name', 'menu_item_variations.variation', 'item_categories.id')
        ->get();
    }

    private function getRecipeCosts($orderData)
    {
        $costs = [];
        
        // Get unique menu items and variations
        $menuItemIds = $orderData->pluck('menu_item_id')->unique();
        $variationIds = $orderData->pluck('menu_item_variation_id')->filter()->unique();
        
        // Fetch all recipes in one query
        $recipes = Recipe::whereIn('menu_item_id', $menuItemIds)
            ->whereNull('modifier_option_id')
            ->with(['inventoryItem'])
            ->get();
        
        // Group recipes by menu item and variation
        $recipeGroups = $recipes->groupBy(function($recipe) {
            return $recipe->menu_item_id . '_' . ($recipe->menu_item_variation_id ?? 'base');
        });
        
        // Calculate costs
        foreach ($orderData as $item) {
            $key = $item->menu_item_id . '_' . ($item->menu_item_variation_id ?? 'base');
            $itemRecipes = $recipeGroups->get($key, collect());
            
            $totalCost = 0;
            foreach ($itemRecipes as $recipe) {
                $totalCost += $recipe->quantity * $recipe->inventoryItem->unit_purchase_price;
            }
            
            $costs[$key] = $totalCost;
        }
        
        return $costs;
    }

    private function getModifierCosts($orderData)
    {
        $costs = [];
        
        // Get unique menu items
        $menuItemIds = $orderData->pluck('menu_item_id')->unique();
        
        // Get all order items with modifier options for the date range
        $orderItemsWithModifiers = OrderItem::whereIn('menu_item_id', $menuItemIds)
            ->with(['modifierOptions', 'order'])
            ->whereHas('order', function($query) {
                $query->whereBetween('date_time', [$this->startDate, $this->endDate])
                      ->where('status', 'paid');
            })
            ->get();
        
        // Count modifier options by menu item and variation
        $modifierCounts = [];
        foreach ($orderItemsWithModifiers as $orderItem) {
            $key = $orderItem->menu_item_id . '_' . ($orderItem->menu_item_variation_id ?? 'base');
            
            foreach ($orderItem->modifierOptions as $modifierOption) {
                if (!isset($modifierCounts[$key][$modifierOption->id])) {
                    $modifierCounts[$key][$modifierOption->id] = 0;
                }
                $modifierCounts[$key][$modifierOption->id] += $orderItem->quantity;
            }
        }
        
        // Get all unique modifier option IDs
        $modifierOptionIds = collect($modifierCounts)
            ->flatten(1)
            ->keys()
            ->unique()
            ->values();
        
        // Fetch all modifier option recipes in one query
        $modifierRecipes = Recipe::whereIn('modifier_option_id', $modifierOptionIds)
            ->with(['inventoryItem'])
            ->get()
            ->groupBy('modifier_option_id');
        
        // Calculate costs
        foreach ($modifierCounts as $key => $modifiers) {
            $totalCost = 0;
            
            foreach ($modifiers as $modifierOptionId => $count) {
                $recipes = $modifierRecipes->get($modifierOptionId, collect());
                
                foreach ($recipes as $recipe) {
                    $totalCost += $recipe->quantity * $recipe->inventoryItem->unit_purchase_price * $count;
                }
            }
            
            $costs[$key] = $totalCost;
        }
        
        return $costs;
    }

    private function buildReportData($orderData, $recipeCosts, $modifierCosts)
    {
        $this->reportData = [];
        $this->totalSales = 0;
        $this->totalCost = 0;
        $this->totalProfit = 0;
        
        // Get categories lookup
        $categoryIds = $orderData->pluck('category_id')->unique()->filter();
        $categories = ItemCategory::whereIn('id', $categoryIds)->get()->keyBy('id');
        
        foreach ($orderData as $item) {
            $key = $item->menu_item_id . '_' . ($item->menu_item_variation_id ?? 'base');
            
            // Calculate costs
            $recipeCost = ($recipeCosts[$key] ?? 0) * $item->total_quantity_sold;
            $modifierCost = $modifierCosts[$key] ?? 0;
            $totalCost = $recipeCost + $modifierCost;
            
            $profit = $item->total_sales_amount - $totalCost;
            
            // Get category name
            $category = $categories->get($item->category_id);
            $categoryName = $category ? $category->getTranslation('category_name', app()->getLocale()) : '';
            
            // Build item name
            $itemName = $item->item_name;
            if ($item->menu_item_variation_id && $item->variation) {
                $itemName .= ' - ' . $item->variation;
            }
            
            $this->reportData[] = [
                'menu_item_id' => $item->menu_item_id,
                'menu_item_variation_id' => $item->menu_item_variation_id,
                'item_name' => $itemName,
                'category_name' => $categoryName,
                'quantity_sold' => $item->total_quantity_sold,
                'avg_unit_price' => $item->avg_unit_price,
                'total_sales' => $item->total_sales_amount,
                'ingredient_cost' => $totalCost,
                'profit' => $profit,
                'profit_margin' => $item->total_sales_amount > 0 ? ($profit / $item->total_sales_amount) * 100 : 0
            ];

            $this->totalSales += $item->total_sales_amount;
            $this->totalCost += $totalCost;
            $this->totalProfit += $profit;
        }
    }

    public function exportCsv()
    {
        $this->generateReport();
        
        $filename = 'profit-and-loss-report-' . date('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Headers
            fputcsv($file, [
                __('inventory::modules.reports.menu_item'),
                __('inventory::modules.reports.category'),
                __('inventory::modules.reports.quantity_sold'),
                __('inventory::modules.reports.avg_price'),
                __('inventory::modules.reports.total_sales'),
                __('inventory::modules.reports.ingredient_cost'),
                __('inventory::modules.reports.profit'),
                __('inventory::modules.reports.profit_margin') . ' (%)'
            ]);
            
            // Data rows
            foreach ($this->reportData as $item) {
                fputcsv($file, [
                    $item['item_name'],
                    $item['category_name'],
                    $item['quantity_sold'],
                    number_format($item['avg_unit_price'], 2),
                    number_format($item['total_sales'], 2),
                    number_format($item['ingredient_cost'], 2),
                    number_format($item['profit'], 2),
                    number_format($item['profit_margin'], 1)
                ]);
            }
            
            // Summary rows
            fputcsv($file, ['']);
            fputcsv($file, [
                __('inventory::modules.reports.total_sales'),
                '',
                '',
                '',
                number_format($this->totalSales, 2),
                '',
                '',
                ''
            ]);
            fputcsv($file, [
                __('inventory::modules.reports.total_cost'),
                '',
                '',
                '',
                '',
                number_format($this->totalCost, 2),
                '',
                ''
            ]);
            fputcsv($file, [
                __('inventory::modules.reports.total_profit'),
                '',
                '',
                '',
                '',
                '',
                number_format($this->totalProfit, 2),
                ''
            ]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('inventory::livewire.reports.profit-and-loss-report');
    }
}
