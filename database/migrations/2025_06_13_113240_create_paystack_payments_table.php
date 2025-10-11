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
        Schema::create('paystack_payments', function (Blueprint $table) {
            $table->id();
            $table->string('paystack_payment_id')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamp('payment_date')->nullable();
            $table->json('payment_error_response')->nullable();
            $table->timestamps();
        });

        //  Schema::table('payments', function (Blueprint $table) {
        //     $table->enum('payment_method', ['cash', 'upi', 'card', 'due', 'stripe', 'flutterwave', 'razorpay', 'paypal', 'payfast', 'paystack'])->default('cash')->change();
        // });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paystack_payments');
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'upi', 'card', 'due', 'stripe', 'flutterwave', 'razorpay', 'paypal', 'payfast'])->default('cash')->change();
        });
    }
};
