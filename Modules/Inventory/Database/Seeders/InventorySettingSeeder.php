<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Entities\InventorySetting;

class InventorySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InventorySetting::create([
            'restaurant_id' => 1,
            'allow_auto_purchase' => true,
        ]);
    }
}
