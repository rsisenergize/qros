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
        Schema::table('split_order_items', function (Blueprint $table) {
            $table->integer('quantity')->nullable()->after('order_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('split_order_items', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
