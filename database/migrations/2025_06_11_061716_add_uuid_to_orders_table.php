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
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'upi', 'card', 'due', 'stripe', 'razorpay', 'flutterwave', 'paypal', 'payfast', 'others'])->default('cash')->change();
        });

        Schema::table('orders', function (Blueprint $table) {
        $table->uuid('uuid')->unique()->nullable()->after('id');
        });

        // Update existing records with UUIDs
        \App\Models\Order::query()->each(function ($order) {
            $order->uuid = (string) \Illuminate\Support\Str::uuid();
            $order->save();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
