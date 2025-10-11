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
        if (!Schema::hasColumn('payment_gateway_credentials', 'flutterwave_status')) {
            Schema::table('payment_gateway_credentials', function (Blueprint $table) {
                $table->boolean('flutterwave_status')->default(false);
                $table->enum('flutterwave_mode', ['test', 'live'])->default('test');
                $table->string('test_flutterwave_key')->nullable();
                $table->string('test_flutterwave_secret')->nullable();
                $table->string('test_flutterwave_hash')->nullable();
                $table->string('live_flutterwave_key')->nullable();
                $table->string('live_flutterwave_secret')->nullable();
                $table->string('live_flutterwave_hash')->nullable();
                $table->string('flutterwave_webhook_secret_hash')->nullable();
            });
        }

        if (!Schema::hasColumn('global_subscriptions', 'flutterwave_id')) {
            Schema::table('global_subscriptions', function (Blueprint $table) {
                $table->string('flutterwave_id')->nullable();
                $table->string('flutterwave_payment_ref')->nullable();
                $table->string('flutterwave_status')->nullable();
                $table->string('flutterwave_customer_id')->nullable();
            });
        }

        if (!Schema::hasColumn('superadmin_payment_gateways', 'flutterwave_status')) {
            Schema::table('superadmin_payment_gateways', function (Blueprint $table) {
                $table->boolean('flutterwave_status')->default(false);
                $table->enum('flutterwave_type', ['test', 'live'])->default('test');
                $table->text('test_flutterwave_key')->nullable();
                $table->text('test_flutterwave_secret')->nullable();
                $table->text('test_flutterwave_hash')->nullable();
                $table->text('flutterwave_test_webhook_key')->nullable();
                $table->text('live_flutterwave_key')->nullable();
                $table->text('live_flutterwave_secret')->nullable();
                $table->text('live_flutterwave_hash')->nullable();
                $table->text('flutterwave_live_webhook_key')->nullable();
            });
        }

        if (!Schema::hasTable('flutterwave_payments')) {
            Schema::create('flutterwave_payments', function (Blueprint $table) {
                $table->id();
                $table->string('flutterwave_payment_id')->nullable();
                $table->unsignedBigInteger('order_id');
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->decimal('amount', 10, 2);
                $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
                $table->timestamp('payment_date')->nullable();
                $table->json('payment_error_response')->nullable();
                $table->timestamps();
            });
        }


        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'upi', 'card', 'due', 'stripe', 'razorpay', 'flutterwave', 'others'])->default('cash')->change();
        });

        if (!Schema::hasColumn('packages', 'flutterwave_annual_plan_id')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->string('flutterwave_annual_plan_id')->nullable()->after('razorpay_monthly_plan_id');
                $table->string('flutterwave_monthly_plan_id')->nullable()->after('flutterwave_annual_plan_id');
            });
        }

        if (!Schema::hasColumn('restaurant_payments', 'flutterwave_transaction_id')) {
            Schema::table('restaurant_payments', function (Blueprint $table) {
                $table->string('flutterwave_transaction_id')->nullable();
                $table->string('flutterwave_payment_ref')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
