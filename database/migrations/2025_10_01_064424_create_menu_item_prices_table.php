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
        Schema::create('menu_item_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_item_id');
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('order_type_id');
            $table->foreign('order_type_id')->references('id')->on('order_types')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('delivery_app_id')->nullable();
            $table->foreign('delivery_app_id')->references('id')->on('delivery_platforms')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('menu_item_variation_id')->nullable();
            $table->foreign('menu_item_variation_id')->references('id')->on('menu_item_variations')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('calculated_price', 16, 2);
            $table->decimal('override_price', 16, 2)->nullable();
            $table->decimal('final_price', 16, 2)->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_prices');
    }
};
