<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class InventoryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        if (!app()->environment('codecanyon')) {

            $this->call([
                InventoryItemCategoriesTableSeeder::class,
                UnitsTableSeeder::class,
                SuppliersTableSeeder::class,
                InventoryItemsTableSeeder::class,
                InventoryMovementsTableSeeder::class,
                InventoryStockTableSeeder::class,
                RecipesTableSeeder::class,
                InventorySettingSeeder::class,
            ]);
        }
    }
}
