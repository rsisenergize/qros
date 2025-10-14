<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Module;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use App\Models\Restaurant;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->decimal('unit_purchase_price', 16, 2)->default(0);
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->decimal('unit_purchase_price', 16, 2)->default(0);
            $table->date('expiration_date')->nullable();
        });

        $inventoryModule = Module::firstOrCreate(['name' => 'Inventory']);

        $permissions = [
            ['guard_name' => 'web', 'name' => 'Create Inventory Item', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Show Inventory Item', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Update Inventory Item', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Inventory Item', 'module_id' => $inventoryModule->id],

            ['guard_name' => 'web', 'name' => 'Create Inventory Movement', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Show Inventory Movement', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Update Inventory Movement', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Inventory Movement', 'module_id' => $inventoryModule->id],

            ['guard_name' => 'web', 'name' => 'Show Inventory Stock', 'module_id' => $inventoryModule->id],

            ['guard_name' => 'web', 'name' => 'Create Unit', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Show Unit', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Update Unit', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Unit', 'module_id' => $inventoryModule->id],

            ['guard_name' => 'web', 'name' => 'Create Recipe', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Show Recipe', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Update Recipe', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Recipe', 'module_id' => $inventoryModule->id],

            ['guard_name' => 'web', 'name' => 'Create Purchase Order', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Show Purchase Order', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Update Purchase Order', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Purchase Order', 'module_id' => $inventoryModule->id],

            ['guard_name' => 'web', 'name' => 'Show Inventory Report', 'module_id' => $inventoryModule->id],

            ['guard_name' => 'web', 'name' => 'Update Inventory Settings', 'module_id' => $inventoryModule->id],
        ];

        Permission::insert($permissions);

        $allPermissions = Permission::where('module_id', $inventoryModule->id)->get()->pluck('name')->toArray();

        $restaurants = Restaurant::select('id')->get();

        foreach ($restaurants as $restaurant) {
            $adminRole = Role::where('name', 'Admin_' . $restaurant->id)->first();
            $branchHeadRole = Role::where('name', 'Branch Head_' . $restaurant->id)->first();

            $adminRole->givePermissionTo($allPermissions);
            $branchHeadRole->givePermissionTo($allPermissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $inventoryModule = Module::where('name', 'Inventory')->first();

        if ($inventoryModule) {
            $permissions = Permission::where('module_id', $inventoryModule->id)->delete();
        }


        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn('unit_purchase_price');
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropColumn('unit_purchase_price');
            $table->dropColumn('expiration_date');
        });
    }
};
