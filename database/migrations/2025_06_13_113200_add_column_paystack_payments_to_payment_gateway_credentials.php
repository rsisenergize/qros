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
         Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            $table->string('paystack_key')->nullable();
                $table->string('paystack_secret')->nullable();
                $table->string('paystack_merchant_email')->nullable();
                $table->boolean('paystack_status')->default(false);
                $table->enum('paystack_mode', ['sandbox', 'live'])->default('sandbox');
                $table->string('test_paystack_key')->nullable();
                $table->string('test_paystack_secret')->nullable();
                $table->string('test_paystack_merchant_email')->nullable();
                $table->string('paystack_payment_url')->nullable()->default('https://api.paystack.co');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            $table->dropColumn([
                'paystack_key',
                'paystack_secret',
                'paystack_merchant_email',
                'paystack_status',
                'paystack_mode',
                'test_paystack_key',
                'test_paystack_secret',
                'test_paystack_merchant_email',
                'paystack_payment_url',
            ]);
        });
    }
};
