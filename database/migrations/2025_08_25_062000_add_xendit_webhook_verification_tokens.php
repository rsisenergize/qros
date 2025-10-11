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
            // Xendit webhook verification tokens
            $table->string('test_xendit_webhook_token')->nullable();
            $table->string('live_xendit_webhook_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            $table->dropColumn([
                'test_xendit_webhook_token',
                'live_xendit_webhook_token'
            ]);
        });
    }
};
