<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->boolean('is_pwa_install_alert_show')->default(0);
        });

        Schema::table('global_settings', function (Blueprint $table) {
            $table->string('is_pwa_install_alert_show')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('is_pwa_install_alert_show');
        });
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn('is_pwa_install_alert_show');
        });
    }
};
