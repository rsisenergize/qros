<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Modules\Inventory\Entities\InventoryGlobalSetting;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('inventory_global_settings')) {
            Schema::create('inventory_global_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('license_type', 20)->nullable();
                $table->string('purchase_code')->nullable();
                $table->timestamp('purchased_on')->nullable();
                $table->timestamp('supported_until')->nullable();
                $table->boolean('notify_update')->default(1);
                $table->timestamps();
            });
        }

        InventoryGlobalSetting::create();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_global_settings');
    }
};
