<?php

namespace Modules\Kiosk\Console;

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
    protected $name = 'kiosk:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add all the module settings of kiosk module';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            Artisan::call('module:migrate Kiosk');
        } catch (\Exception $e) {
            // Silent exception
        }
    }
}
