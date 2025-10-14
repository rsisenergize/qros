<?php

namespace Modules\Inventory\Console;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Modules\Inventory\Entities\InventorySetting;
use Modules\Inventory\Entities\Unit;
use Modules\Inventory\Entities\InventoryItemCategory;

class ActivateModuleCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'inventory:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add all the module settings of inventory module';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            Artisan::call('module:migrate Inventory');
        } catch (\Exception $e) {
            // Silent exception
        }

        $restaurant = Restaurant::with('branches')->get();

        foreach ($restaurant as $restaurant) {
            InventorySetting::create([
                'restaurant_id' => $restaurant->id,
            ]);

            foreach ($restaurant->branches as $branch) {
                foreach (Unit::UNITS as $unit) {
                    Unit::firstOrCreate(array_merge($unit, [
                        'branch_id' => $branch->id
                    ]));
                }

                foreach (InventoryItemCategory::CATEGORIES as $category) {
                    InventoryItemCategory::firstOrCreate([
                        'branch_id' => $branch->id,
                        'name' => $category
                    ]);
                }
            }
        }
    }
}
