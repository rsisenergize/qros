<?php

use Illuminate\Database\Migrations\Migration;
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

        $inventoryModule = Module::firstOrCreate(['name' => 'Inventory']);

        $permissions = [
            ['guard_name' => 'web', 'name' => 'Show Supplier', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Create Supplier', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Update Supplier', 'module_id' => $inventoryModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Supplier', 'module_id' => $inventoryModule->id],
        ];

        Permission::insert($permissions);

        $allPermissions = Permission::where('module_id', $inventoryModule->id)->get()->pluck('name')->toArray();

        $restaurants = Restaurant::select('id')->get();

        foreach ($restaurants as $restaurant) {
            $adminRole = Role::where('name', 'Admin_' . $restaurant->id)->first();
            $branchHeadRole = Role::where('name', 'Branch Head_' . $restaurant->id)->first();

            if ($adminRole) {
                $adminRole->givePermissionTo($allPermissions);
            }
            if ($branchHeadRole) {
                $branchHeadRole->givePermissionTo($allPermissions);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
