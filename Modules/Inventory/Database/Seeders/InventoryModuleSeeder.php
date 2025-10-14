<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Entities\Unit;
use Modules\Inventory\Entities\Supplier;
use Modules\Inventory\Entities\InventoryItem;
use Modules\Inventory\Entities\InventoryStock;
use Modules\Inventory\Entities\InventoryMovement;
use Modules\Inventory\Entities\InventoryItemCategory;

class InventoryModuleSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UnitsTableSeeder::class,
            InventoryItemCategoriesTableSeeder::class,
            SuppliersTableSeeder::class,
            InventoryItemsTableSeeder::class,
            InventoryStockTableSeeder::class,
            InventoryMovementsTableSeeder::class,
        ]);
    }
}