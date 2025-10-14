<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Module;
use Modules\Kiosk\Entities\KioskGlobalSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('kiosk_global_settings')) {
            Schema::create('kiosk_global_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('license_type', 20)->nullable();
                $table->string('purchase_code')->nullable();
                $table->timestamp('purchased_on')->nullable();
                $table->timestamp('supported_until')->nullable();
                $table->boolean('notify_update')->default(1);
                $table->timestamps();
            });

            KioskGlobalSetting::create([]);
        }



        Module::firstOrCreate(['name' => 'Kiosk']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kiosk_global_settings');
    }
};
