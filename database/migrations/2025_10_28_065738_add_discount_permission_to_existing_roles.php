<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Module;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {});

        // Get the Order module
        $checkOrderModule = Module::where('name', 'Order')->first();

        if ($checkOrderModule) {
            $module = $checkOrderModule;

            // Create the new permission if it doesn't exist
            $permission = Permission::firstOrCreate([
                'guard_name' => 'web',
                'name' => 'Add Discount on POS',
                'module_id' => $module->id
            ]);

            // Assign permission to Admin and Branch Head roles only
            $adminRoles = Role::where('display_name', 'Admin')->get();
            $branchHeadRoles = Role::where('display_name', 'Branch Head')->get();

            foreach ($adminRoles as $role) {
                $role->givePermissionTo('Add Discount on POS');
            }

            foreach ($branchHeadRoles as $role) {
                $role->givePermissionTo('Add Discount on POS');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {});

        $module = Module::where('name', 'Order')->first();

        if ($module) {
            Permission::where('name', 'Add Discount on POS')->delete();
        }
    }
};
