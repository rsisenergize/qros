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
            $table->unsignedBigInteger('cancel_reason_id')->nullable();
            $table->foreign('cancel_reason_id')->references('id')->on('kot_cancel_reasons')->onDelete('cascade')->onUpdate('cascade');
            $table->string('cancel_reason_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['cancellation_reason_id']);
            $table->dropColumn('cancellation_reason_id');
        });
    }
};
