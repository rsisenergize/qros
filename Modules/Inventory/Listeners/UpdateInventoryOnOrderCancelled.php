<?php

namespace Modules\Inventory\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\OrderCancelled;
use Modules\Inventory\Entities\Recipe;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\InventoryStock;
use Modules\Inventory\Entities\InventoryMovement;

class UpdateInventoryOnOrderCancelled
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(OrderCancelled $event): void
    {
        $order = $event->order;

        // Get all order items
        foreach ($order->load('items.modifierOptions')->items as $orderItem) {
            // Get recipe for this menu item or variation
            $recipes = collect();
            
            // If order item has a variation, get recipes for that variation
            if ($orderItem->menu_item_variation_id) {
                $recipes = Recipe::where('menu_item_id', $orderItem->menu_item_id)
                    ->where('menu_item_variation_id', $orderItem->menu_item_variation_id)
                    ->get();
            }
            
            // If no variation or no variation-specific recipes found, get base menu item recipes
            if ($recipes->isEmpty()) {
                $recipes = Recipe::where('menu_item_id', $orderItem->menu_item_id)
                    ->whereNull('menu_item_variation_id')
                    ->get();
            }
            
            foreach ($recipes as $recipe) {
                // Calculate quantity needed based on order quantity
                $quantityNeeded = $recipe->quantity * $orderItem->quantity;

                $this->processRecipe($order, $recipe, $quantityNeeded);
            }

            // Process recipes for modifier options
            foreach ($orderItem->modifierOptions as $modifierOption) {
                $modifierRecipes = Recipe::where('modifier_option_id', $modifierOption->id)->get();
                foreach ($modifierRecipes as $recipe) {
                    // Calculate quantity needed based on order quantity
                    $quantityNeeded = $recipe->quantity * $orderItem->quantity;

                    $this->processRecipe($order, $recipe, $quantityNeeded);
                }
            }
        }
    }

    private function processRecipe($order, $recipe, $quantityNeeded): void
    {
        try {
            DB::transaction(function () use ($order, $recipe, $quantityNeeded) {
                // Update inventory stock
                $stock = InventoryStock::where('branch_id', $order->branch_id)
                    ->where('inventory_item_id', $recipe->inventory_item_id)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {

                    // Create inventory movement record for stock in
                    InventoryMovement::create([
                        'branch_id' => $order->branch_id,
                        'inventory_item_id' => $recipe->inventory_item_id,
                        'quantity' => $quantityNeeded,
                        'transaction_type' => 'in',
                        'added_by' => auth()->check() ? auth()->id() : null
                    ]);

                    // Update stock quantity
                    $stock->quantity = $stock->quantity + $quantityNeeded;
                    $stock->save();
                }
            });
        } catch (\Exception $e) {
            \Log::error('Error updating inventory for order: ' . $order->id . ' - ' . $e->getMessage());
        }
    }
}
