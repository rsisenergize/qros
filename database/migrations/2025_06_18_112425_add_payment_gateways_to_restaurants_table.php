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
        Schema::table('global_settings', function (Blueprint $table) {
            $table->boolean('enable_stripe')->default(true);
            $table->boolean('enable_razorpay')->default(true);
            $table->boolean('enable_flutterwave')->default(true);
            $table->boolean('enable_payfast')->default(true);
            $table->boolean('enable_paypal')->default(true);
            $table->boolean('enable_paystack')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn([
                'enable_stripe',
                'enable_razorpay',
                'enable_flutterwave',
                'enable_payfast',
                'enable_paypal',
                'enable_paystack',
            ]);
        });
    }
};
