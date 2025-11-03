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
        Schema::create('modifier_option_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('modifier_group_id');
            $table->foreign('modifier_group_id')->references('id')->on('modifier_groups')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('modifier_option_id')->nullable();
            $table->foreign('modifier_option_id')->references('id')->on('modifier_options')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('order_type_id')->nullable();
            $table->foreign('order_type_id')->references('id')->on('order_types')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('delivery_app_id')->nullable();
            $table->foreign('delivery_app_id')->references('id')->on('delivery_platforms')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('modifier_option_prices');
    }
};
