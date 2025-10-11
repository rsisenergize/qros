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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'draft', 'kot', 'billed', 'paid', 'canceled', 'payment_due', 'ready', 'out_for_delivery', 'delivered', 'pending_verification',
            ])->default('kot')->change();
        });

    Schema::table('payments', function (Blueprint $table) {
        $table->enum('payment_method', ['cash', 'upi', 'card', 'due', 'stripe', 'razorpay', 'others'])->default('cash')->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'draft', 'kot', 'billed', 'paid', 'canceled', 'payment_due', 'ready', 'out_for_delivery', 'delivered',
            ])->default('kot')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'upi', 'card', 'due', 'stripe', 'razorpay'])->default('cash')->change();
        });
    }
};
