<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up(): void
    {


        Schema::table('customers', function (Blueprint $table) {});

        $checkCustomerModule = Module::where('name', 'Customer')->first();

        if ($checkCustomerModule) {
            $module = $checkCustomerModule;

            $permissions = [
                Permission::firstOrCreate([
                    'guard_name' => 'web',
                    'name' => 'Create Customer',
                    'module_id' => $module->id
                ])
            ];
            // Permission already created above

            // Assign permissions to roles
            $adminRole = Role::where('display_name', 'Admin')->get();
            $branchHeadRole = Role::where('display_name', 'Branch Head')->get();

            $allPermissions = Permission::get()->pluck('name')->toArray();

            foreach ($adminRole as $role) {
                $role->givePermissionTo($allPermissions);
            }

            foreach ($branchHeadRole as $role) {
                $role->givePermissionTo($allPermissions);
            }
        }
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {});
        $module = Module::where('name', 'Customers')->first();

        if ($module) {
            Permission::where('module_id', $module->id)->delete();
            $module->delete();
        }
    }
};
