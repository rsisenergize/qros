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
       
        Schema::create('cart_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('order_type_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('placed_via', ['pos', 'shop', 'kiosk'])->nullable();
            $table->string('order_type');
            $table->decimal('sub_total', 16, 2);
            $table->decimal('total', 16, 2);
            $table->decimal('total_tax_amount', 16, 2)->default(0);
            $table->enum('tax_mode', ['order', 'item'])->default('order');
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_session_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('menu_item_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('menu_item_variation_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 16, 2);
            $table->decimal('amount', 16, 2);
            $table->decimal('tax_amount', 16, 2)->nullable();
            $table->decimal('tax_percentage', 8, 4)->nullable();
            $table->json('tax_breakup')->nullable();
            $table->timestamps();
        });

        Schema::create('cart_item_modifier_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_item_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('modifier_option_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_item_modifier_options');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('cart_sessions');
    }
};
