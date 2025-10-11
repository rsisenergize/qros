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
        Schema::table('pusher_settings', function (Blueprint $table) {
            $table->boolean('pusher_broadcast')->default(false);
            $table->string('pusher_app_id')->nullable();
            $table->string('pusher_key')->nullable();
            $table->string('pusher_secret')->nullable();
            $table->string('pusher_cluster')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pusher_settings', function (Blueprint $table) {
            $table->dropColumn(['pusher_app_id', 'pusher_key', 'pusher_secret', 'pusher_cluster', 'pusher_broadcast']);
        });
    }
};
