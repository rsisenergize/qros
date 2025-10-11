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
            $table->integer('pickup_days_range')->nullable()->after('allow_customer_pickup_orders')->default(7);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dateTime('pickup_date')->nullable()->after('order_type');
        });
        Schema::table('receipt_settings', function (Blueprint $table) {
            $table->boolean('show_order_type')->nullable()->after('show_payment_details')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('pickup_days_range');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('pickup_date');
        });
        Schema::table('receipt_settings', function (Blueprint $table) {
            $table->dropColumn('show_order_type');
        });
    }

};
