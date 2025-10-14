<?php

namespace Modules\Inventory\Listeners;

use App\Events\NewOrderCreated;
use Modules\Inventory\Entities\Recipe;
use Modules\Inventory\Entities\InventoryMovement;
use Modules\Inventory\Entities\InventoryStock;
use Illuminate\Support\Facades\DB;

class UpdateInventoryOnOrderReceived
{
    public function handle(NewOrderCreated $event): void
    {
        $order = $event->order;

        // Get all order items
        foreach ($order->load('items')->items as $orderItem) {
            // Get recipe for this menu item
            $recipes = Recipe::where('menu_item_id', $orderItem->menu_item_id)->get();
            foreach ($recipes as $recipe) {
                // Calculate quantity needed based on order quantity
                $quantityNeeded = $recipe->quantity * $orderItem->quantity;

                try {
                    DB::transaction(function () use ($order, $recipe, $quantityNeeded) {
                        // Update inventory stock
                        $stock = InventoryStock::where('branch_id', $order->branch_id)
                            ->where('inventory_item_id', $recipe->inventory_item_id)
                            ->lockForUpdate()
                            ->first();

                        if ($stock) {

                            // Create inventory movement record for stock out
                            InventoryMovement::create([
                                'branch_id' => $order->branch_id,
                                'inventory_item_id' => $recipe->inventory_item_id,
                                'quantity' => $quantityNeeded,
                                'transaction_type' => 'out',
                                'added_by' => auth()->check() ? auth()->id() : null
                            ]);

                            // Update stock quantity
                            $stock->quantity = $stock->quantity - $quantityNeeded;
                            $stock->save();

                            if ($recipe->inventoryItem->current_stock <= 0) {
                                foreach ($recipe->inventoryItem->menuItems as $menuItem) {
                                    $menuItem->update([
                                        'in_stock' => 0
                                    ]);
                                }
                            }
                        }
                    });
                } catch (\Exception $e) {
                    \Log::error('Error updating inventory for order: ' . $order->id . ' - ' . $e->getMessage());
                }
            }
        }
    }
} 