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
            // Xendit payment gateway fields
            $table->boolean('xendit_status')->default(false);
            $table->enum('xendit_mode', ['sandbox', 'live'])->default('sandbox');
            $table->string('test_xendit_public_key')->nullable();
            $table->string('test_xendit_secret_key')->nullable();
            $table->string('live_xendit_public_key')->nullable();
            $table->string('live_xendit_secret_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            $table->dropColumn([
                'xendit_status',
                'xendit_mode',
                'test_xendit_public_key',
                'test_xendit_secret_key',
                'live_xendit_public_key',
                'live_xendit_secret_key'
            ]);
        });
    }
};
