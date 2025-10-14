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
        Schema::table('superadmin_payment_gateways', function (Blueprint $table) {
            $table->boolean('paddle_status')->default(false);
            $table->enum('paddle_mode', ['sandbox', 'live'])->default('sandbox');
            $table->string('test_paddle_vendor_id')->nullable();
            $table->text('test_paddle_api_key')->nullable();
            $table->string('test_paddle_public_key')->nullable();
            $table->text('test_paddle_client_token')->nullable();
            $table->string('live_paddle_vendor_id')->nullable();
            $table->text('live_paddle_api_key')->nullable();
            $table->string('live_paddle_public_key')->nullable();
            $table->text('live_paddle_client_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('superadmin_payment_gateways', function (Blueprint $table) {
            $table->dropColumn([
                'paddle_status',
                'paddle_mode',
                'test_paddle_vendor_id',
                'test_paddle_api_key',
                'test_paddle_public_key',
                'test_paddle_client_token',
                'live_paddle_vendor_id',
                'live_paddle_api_key',
                'live_paddle_public_key',
                'live_paddle_client_token'
            ]);
        });
    }

};
