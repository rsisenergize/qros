<?php

namespace Modules\Inventory\Livewire\Reports;

use Livewire\Component;
use Modules\Inventory\Entities\InventoryItem;
use Modules\Inventory\Entities\InventoryMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Modules\Inventory\Entities\InventoryItemCategory;
use App\Models\OrderItem;
use App\Models\MenuItemVariation;
use App\Models\OrderItemModifierOption;
use Modules\Inventory\Entities\Recipe;


class CogsReport extends Component
{
    public $startDate;
    public $endDate;
    public $selectedCategory = 'all';
    public $reportData = [];
    public $totalCogs = 0;
    public $categories = [];

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $this->endDate = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
        $this->categories = InventoryItemCategory::all();
        $this->generateReport();
    }

    public function generateReport()
    {
        // Set MySQL to non-strict mode for this query
        DB::statement("SET SESSION sql_mode=''");
        
        // Get inventory movements for order usage
        $query = InventoryMovement::query()
            ->join('inventory_items', 'inventory_movements.inventory_item_id', '=', 'inventory_items.id')
            ->whereBetween('inventory_movements.created_at', [$this->startDate, $this->endDate])
            ->where('inventory_movements.transaction_type', InventoryMovement::TRANSACTION_TYPE_ORDER_USED);

        if ($this->selectedCategory !== 'all') {
            $query->where('inventory_items.inventory_item_category_id', $this->selectedCategory);
        }

        $this->reportData = $query->select(
            'inventory_items.name as product_name',
            'inventory_items.inventory_item_category_id',
            'inventory_items.unit_purchase_price',
            'inventory_movements.inventory_item_id',
            DB::raw('SUM(inventory_movements.quantity) as total_quantity'),
            DB::raw('SUM(inventory_movements.quantity * inventory_items.unit_purchase_price) as total_cost')
        )
            ->groupBy('inventory_items.id', 'inventory_items.name', 'inventory_items.inventory_item_category_id')
            ->with('item', 'item.category', 'item.unit')
            ->get();

        // Also get order-based COGS data to cross-reference with variations
        $this->getOrderBasedCogsData();

        $this->calculateTotals();
        // Reset SQL mode back to default after query execution
        DB::statement("SET SESSION sql_mode=(SELECT @@global.sql_mode)");
    }

    private function getOrderBasedCogsData()
    {
        // Get order items with variations to calculate COGS based on recipes
        $orderItems = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('menu_item_variations', 'order_items.menu_item_variation_id', '=', 'menu_item_variations.id')
            ->whereBetween('orders.date_time', [$this->startDate, $this->endDate])
            ->where('orders.status', 'paid')
            ->select([
                'order_items.menu_item_id',
                'order_items.menu_item_variation_id',
                'order_items.quantity',
                'menu_item_variations.variation'
            ])
            ->get();

        // Get modifier options from order items
        $orderItemModifierOptions = OrderItemModifierOption::query()
            ->join('order_items', 'order_item_modifier_options.order_item_id', '=', 'order_items.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.date_time', [$this->startDate, $this->endDate])
            ->where('orders.status', 'paid')
            ->select([
                'order_item_modifier_options.modifier_option_id',
                'order_items.quantity'
            ])
            ->get();

        // Group by inventory item to calculate expected usage
        $expectedUsage = [];
        
        foreach ($orderItems as $orderItem) {
            // Get recipes for this menu item and variation
            $recipes = $this->getRecipesForMenuItem($orderItem->menu_item_id, $orderItem->menu_item_variation_id);
            
            foreach ($recipes as $recipe) {
                $inventoryItemId = $recipe->inventory_item_id;
                $quantityUsed = $recipe->quantity * $orderItem->quantity;
                
                if (!isset($expectedUsage[$inventoryItemId])) {
                    $expectedUsage[$inventoryItemId] = [
                        'quantity' => 0,
                        'cost' => 0,
                        'unit_price' => $recipe->inventoryItem->unit_purchase_price
                    ];
                }
                
                $expectedUsage[$inventoryItemId]['quantity'] += $quantityUsed;
                $expectedUsage[$inventoryItemId]['cost'] += $quantityUsed * $recipe->inventoryItem->unit_purchase_price;
            }
        }

        // Calculate expected usage for modifier options
        foreach ($orderItemModifierOptions as $modifierOption) {
            // Get recipes for this modifier option
            $recipes = $this->getRecipesForModifierOption($modifierOption->modifier_option_id);
            
            foreach ($recipes as $recipe) {
                $inventoryItemId = $recipe->inventory_item_id;
                $quantityUsed = $recipe->quantity * $modifierOption->quantity;
                
                if (!isset($expectedUsage[$inventoryItemId])) {
                    $expectedUsage[$inventoryItemId] = [
                        'quantity' => 0,
                        'cost' => 0,
                        'unit_price' => $recipe->inventoryItem->unit_purchase_price
                    ];
                }
                
                $expectedUsage[$inventoryItemId]['quantity'] += $quantityUsed;
                $expectedUsage[$inventoryItemId]['cost'] += $quantityUsed * $recipe->inventoryItem->unit_purchase_price;
            }
        }

        // Merge expected usage with actual inventory movements
        foreach ($this->reportData as $item) {
            if (isset($expectedUsage[$item->inventory_item_id])) {
                $expected = $expectedUsage[$item->inventory_item_id];
                $item->expected_quantity = $expected['quantity'];
                $item->expected_cost = $expected['cost'];
                $item->variance = $item->total_cost - $expected['cost'];
            } else {
                $item->expected_quantity = 0;
                $item->expected_cost = 0;
                $item->variance = $item->total_cost;
            }
        }
    }

    private function getRecipesForMenuItem($menuItemId, $variationId)
    {
        if ($variationId) {
            // Get recipes specific to this variation
            return Recipe::where('menu_item_id', $menuItemId)
                ->where('menu_item_variation_id', $variationId)
                ->whereNull('modifier_option_id')
                ->with(['inventoryItem'])
                ->get();
        } else {
            // Get base recipes (without variation)
            return Recipe::where('menu_item_id', $menuItemId)
                ->whereNull('menu_item_variation_id')
                ->whereNull('modifier_option_id')
                ->with(['inventoryItem'])
                ->get();
        }
    }

    private function getRecipesForModifierOption($modifierOptionId)
    {
        return Recipe::where('modifier_option_id', $modifierOptionId)
            ->with(['inventoryItem'])
            ->get();
    }

    private function calculateTotals()
    {
        $this->totalCogs = $this->reportData->sum('total_cost');
    }

    public function exportCsv()
    {
        // Generate the report data first
        $this->generateReport();
        
        $filename = 'cogs-report-' . date('Y-m-d-H-i-s') . '.csv';
        
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
                __('inventory::modules.reports.cogs.table.item'),
                __('inventory::modules.reports.cogs.table.category'),
                __('inventory::modules.reports.cogs.table.quantity_used'),
                __('inventory::modules.reports.cogs.table.total_cost'),
                __('inventory::modules.reports.cogs.table.expected_quantity'),
                __('inventory::modules.reports.cogs.table.expected_cost'),
                __('inventory::modules.reports.cogs.table.variance')
            ]);
            
            // Data rows
            foreach ($this->reportData as $item) {
                fputcsv($file, [
                    $item->product_name,
                    $item->item->category->name ?? '',
                    $item->total_quantity,
                    number_format($item->total_cost, 2),
                    $item->expected_quantity ?? 0,
                    number_format($item->expected_cost ?? 0, 2),
                    number_format($item->variance ?? 0, 2)
                ]);
            }
            
            // Summary row
            fputcsv($file, ['']);
            fputcsv($file, [
                __('inventory::modules.reports.cogs.summary.total_cogs'),
                '',
                '',
                number_format($this->totalCogs, 2),
                '',
                '',
                ''
            ]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('inventory::livewire.reports.cogs-report');
    }
}
