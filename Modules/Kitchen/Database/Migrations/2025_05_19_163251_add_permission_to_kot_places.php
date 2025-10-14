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
        $kitchenModule = Module::firstOrCreate(['name' => 'Kitchen']);

        $permissionsData = [
            'Show Kitchen Place',
            'Create Kitchen Place',
            'Update Kitchen Place',
            'Delete Kitchen Place',
        ];

        $createdPermissions = [];

        foreach ($permissionsData as $permissionName) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
                'module_id' => $kitchenModule->id,
            ]);
            $createdPermissions[] = $permission->name;
        }

        $restaurants = Restaurant::select('id')->get();

        foreach ($restaurants as $restaurant) {
            $adminRole = Role::where('name', 'Admin_' . $restaurant->id)->first();
            $branchHeadRole = Role::where('name', 'Branch Head_' . $restaurant->id)->first();

            if ($adminRole) {
                $adminRole->givePermissionTo($createdPermissions);
            }

            if ($branchHeadRole) {
                $branchHeadRole->givePermissionTo($createdPermissions);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $kitchenModule = Module::where('name', 'Kitchen')->first();

        if (!$kitchenModule) {
            return;
        }

        $permissionNames = [
            'Show Kitchen Place',
            'Create Kitchen Place',
            'Update Kitchen Place',
            'Delete Kitchen Place',
        ];

        foreach ($permissionNames as $permissionName) {
            $permission = Permission::where([
                'name' => $permissionName,
                'module_id' => $kitchenModule->id,
            ])->first();

            if ($permission) {
                $permission->delete();
            }
        }
    }
};
