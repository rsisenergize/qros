<?php

use App\Models\Role;
use App\Models\Module;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\GlobalSetting;

return new class extends Migration
{

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_category_id')->nullable();
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('set null');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->string('payment_status');
            $table->date('payment_date')->nullable();
            $table->date('payment_due_date')->nullable();
            $table->string('payment_method');
            $table->string('receipt_path')->nullable();
            $table->timestamps(); // Removed incorrect backtick
        });

        $checkGlobalSetting = GlobalSetting::first();

        // Check if the modules already exist
        $checkExpenseModule = Module::where('name', 'Expense')->first();

        if ($checkGlobalSetting && !$checkExpenseModule) {
            $modules = ['name' => 'Expense'];
            $menuModule = Module::create($modules);

            $permissions = [
                ['guard_name' => 'web', 'name' => 'Create Expense', 'module_id' => $menuModule->id],
                ['guard_name' => 'web', 'name' => 'Show Expense', 'module_id' => $menuModule->id],
                ['guard_name' => 'web', 'name' => 'Update Expense', 'module_id' => $menuModule->id],
                ['guard_name' => 'web', 'name' => 'Delete Expense', 'module_id' => $menuModule->id],
                ['guard_name' => 'web', 'name' => 'Create Expense Category', 'module_id' => $menuModule->id],
                ['guard_name' => 'web', 'name' => 'Show Expense Category', 'module_id' => $menuModule->id],
                ['guard_name' => 'web', 'name' => 'Update Expense Category', 'module_id' => $menuModule->id],
                ['guard_name' => 'web', 'name' => 'Delete Expense Category', 'module_id' => $menuModule->id],
            ];
            Permission::insert($permissions);

            $adminRole = Role::where('display_name', 'Admin')->get();
            $branchHeadRole = Role::where('display_name', 'Branch Head')->get();

            $allPermissions = Permission::get()->pluck('name')->toArray();

            foreach ($adminRole as $role) {
                $role->givePermissionTo($allPermissions);
            }

            foreach ($branchHeadRole as $role) {
                $role->givePermissionTo($allPermissions);
            }



            // info($admxinRole);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
        $module = Module::where('name', 'Expense')->first();

        if ($module) {
            $module->delete();
        }
    }
};
