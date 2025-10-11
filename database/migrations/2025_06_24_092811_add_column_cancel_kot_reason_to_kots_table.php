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
        Schema::table('kots', function (Blueprint $table) {
            $table->unsignedBigInteger('cancel_reason_id')->nullable()->after('status');
            $table->text('cancel_reason_text')->nullable()->after('cancel_reason_id');

             $table->foreign('cancel_reason_id')->references('id')->on('kot_cancel_reasons')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kots', function (Blueprint $table) {

            $table->dropForeign(['cancel_reason_id']);
            $table->dropColumn('cancel_reason_id');
            $table->dropColumn('cancel_reason_text');
        });
    }
};
