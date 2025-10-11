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
        // 1. Create payfast_payments table
        Schema::create('payfast_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payfast_payment_id')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade'); // fixed typo here
            $table->decimal('amount', 10, 2);
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamp('payment_date')->nullable();
            $table->json('payment_error_response')->nullable();
            $table->timestamps();
        });

        // 2. Alter payments table to include 'payfast' in payment_method enum
        // Schema::table('payments', function (Blueprint $table) {
        //     $table->enum('payment_method', ['cash', 'upi', 'card', 'due', 'stripe', 'razorpay', 'paypal', 'payfast'])->default('cash')->change();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop payfast_payments table
        Schema::dropIfExists('payfast_payments');

        // Optional: revert the enum change (if you want to remove 'payfast')
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'upi', 'card', 'due', 'stripe', 'razorpay', 'paypal'])->default('cash')->change();
        });
    }
};
