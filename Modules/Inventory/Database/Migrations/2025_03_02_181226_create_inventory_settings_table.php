<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->boolean('allow_auto_purchase')->default(false);
            $table->timestamps();
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->foreignId('preferred_supplier_id')->nullable()->constrained('suppliers')->cascadeOnDelete();
            $table->decimal('reorder_quantity', 16, 2)->default(0);
        });

        Artisan::call('inventory:activate');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_settings');
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn('preferred_supplier_id');
            $table->dropColumn('reorder_quantity');
        });
    }
};
