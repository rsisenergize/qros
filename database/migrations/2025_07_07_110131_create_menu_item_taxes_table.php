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
        Schema::table('menu_items', function (Blueprint $table) {
            $table->boolean('tax_inclusive')->default(false);
        });

        Schema::create('menu_item_tax', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_item_id');
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('tax_id');
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('tax_amount', 15, 2)->nullable()->after('amount');
            $table->decimal('tax_percentage', 8, 4)->nullable()->after('tax_amount');
            $table->json('tax_breakup')->nullable()->after('tax_percentage');
        });

        // Add tax details to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_tax_amount', 16, 2)->nullable()->default(0)->after('tip_amount');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_tax');
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'tax_percentage', 'tax_breakup']);
        });
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('tax_inclusive');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('total_tax_amount');
        });
    }
};
