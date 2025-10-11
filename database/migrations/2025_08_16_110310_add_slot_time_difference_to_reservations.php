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
        Schema::table('reservations', function (Blueprint $table) {
            $table->integer('slot_time_difference')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('reservation_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('slot_time_difference');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);
            $table->dropColumn(['reservation_id']);
        });
    }
};
