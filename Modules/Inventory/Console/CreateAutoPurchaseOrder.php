<?php

namespace Modules\Inventory\Console;

use Illuminate\Console\Command;
use App\Models\Restaurant;
use Modules\Inventory\Entities\PurchaseOrder;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\Inventory\Entities\InventorySetting;
use Modules\Inventory\Entities\InventoryItem;
use Illuminate\Support\Facades\DB;

class CreateAutoPurchaseOrder extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'inventory:create-auto-purchase-order';

    /**
     * The console command description.
     */
    protected $description = 'Create auto purchase order';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating auto purchase order...');

        $restaurants = Restaurant::select('id', 'name')->with('branches')->get();

        foreach ($restaurants as $restaurant) {

            $inventorySettings = InventorySetting::where('restaurant_id', $restaurant->id)->first();

            if ($inventorySettings && $inventorySettings->allow_auto_purchase) {
                $this->info('Creating auto purchase order for restaurant: ' . $restaurant->name);

                foreach ($restaurant->branches as $branch) {
                    $inventoryItems = InventoryItem::where('branch_id', $branch->id)->get();

                    foreach ($inventoryItems as $inventoryItem) {
                        if ($inventoryItem->current_stock <= $inventoryItem->threshold_quantity) {

                            DB::transaction(function () use ($inventoryItem, $branch) {
                                $po = new PurchaseOrder([
                                    'branch_id' => $branch->id,
                                    'supplier_id' => $inventoryItem->preferred_supplier_id,
                                    'order_date' => now(),
                                    'notes' => __('inventory::modules.purchaseOrder.auto_purchase_order_notes'),
                                    'status' => 'sent',
                                ]);
                    
                                $po->generatePoNumber();
                                $po->save();
                    
                                $po->items()->create([
                                    'inventory_item_id' => $inventoryItem->id,
                                    'quantity' => $inventoryItem->reorder_quantity,
                                    'unit_price' => $inventoryItem->unit_purchase_price,
                                    'subtotal' => $inventoryItem->reorder_quantity * $inventoryItem->unit_purchase_price,
                                ]);
                    
                                $po->update(['total_amount' => $po->items->sum('subtotal')]);
                            });

                            $this->info('Creating auto purchase order for item: ' . $inventoryItem->name . ' in branch: ' . $branch->name . ' Current stock: ' . $inventoryItem->current_stock . ' Threshold quantity: ' . $inventoryItem->threshold_quantity);
                        }
                    }

         
                }

            }
        }

        $this->info('Auto purchase order created successfully.');
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
