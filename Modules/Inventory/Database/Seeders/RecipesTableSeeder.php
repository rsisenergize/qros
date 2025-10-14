<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Entities\Recipe;
use Modules\Inventory\Entities\InventoryItem;
use App\Models\MenuItem;
use Modules\Inventory\Entities\Unit;

class RecipesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Get units from database
        $units = Unit::where('branch_id', 1)->get();
        $kg = $units->where('symbol', 'kg')->first();
        $gram = $units->where('symbol', 'g')->first();
        $liter = $units->where('symbol', 'L')->first();
        $piece = $units->where('symbol', 'pc')->first();
        $dozen = $units->where('symbol', 'dz')->first();

        // Define recipes for menu items using existing inventory items
        $recipes = [
            'Butter Chicken' => [
                [
                    'inventory_item' => 'Chicken Breast',
                    'quantity' => 0.25, // 250g in kg
                    'unit_id' => $kg->id
                ],
                [
                    'inventory_item' => 'Heavy Cream',
                    'quantity' => 0.1, // 100ml in L
                    'unit_id' => $liter->id
                ],
                [
                    'inventory_item' => 'Tomatoes',
                    'quantity' => 0.2, // 200g in kg
                    'unit_id' => $kg->id
                ],
            ],
            'Dal Makhani' => [
                [
                    'inventory_item' => 'All-Purpose Flour',
                    'quantity' => 0.1, // 100g in kg
                    'unit_id' => $kg->id
                ],
                [
                    'inventory_item' => 'Heavy Cream',
                    'quantity' => 0.05, // 50ml in L
                    'unit_id' => $liter->id
                ],
            ],
            'Chicken Manchurian' => [
                [
                    'inventory_item' => 'Chicken Breast',
                    'quantity' => 0.2, // 200g in kg
                    'unit_id' => $kg->id
                ],
                [
                    'inventory_item' => 'All-Purpose Flour',
                    'quantity' => 0.05, // 50g in kg
                    'unit_id' => $kg->id
                ],
            ],
            'Masala Dosa' => [
                [
                    'inventory_item' => 'Basmati Rice',
                    'quantity' => 0.2, // 200g in kg
                    'unit_id' => $kg->id
                ],
                [
                    'inventory_item' => 'All-Purpose Flour',
                    'quantity' => 0.05, // 50g in kg
                    'unit_id' => $kg->id
                ],
            ],
        ];

        // Create recipes for each menu item
        foreach ($recipes as $menuItemName => $ingredients) {
            // Find the menu item
            $menuItem = MenuItem::where('item_name', $menuItemName)
                ->where('branch_id', 1)
                ->first();

            if ($menuItem) {
                foreach ($ingredients as $ingredient) {
                    // Find the inventory item
                    $inventoryItem = InventoryItem::where('name', $ingredient['inventory_item'])
                        ->where('branch_id', 1)
                        ->first();

                    if ($inventoryItem) {
                        Recipe::create([
                            'menu_item_id' => $menuItem->id,
                            'inventory_item_id' => $inventoryItem->id,
                            'quantity' => $ingredient['quantity'],
                            'unit_id' => $ingredient['unit_id']
                        ]);
                    } else {
                        $this->command->warn("Inventory item not found: {$ingredient['inventory_item']}");
                    }
                }
            } else {
                $this->command->warn("Menu item not found: {$menuItemName}");
            }
        }
    }
}
