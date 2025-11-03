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
        Schema::table('kot_items', function (Blueprint $table) {
            // Add cancel reason columns
            $table->unsignedBigInteger('cancel_reason_id')->nullable()->after('status');
            $table->text('cancel_reason_text')->nullable()->after('cancel_reason_id');

            // Add foreign key constraint for cancel reason
            $table->foreign('cancel_reason_id')->references('id')->on('kot_cancel_reasons')->onDelete('cascade')->onUpdate('cascade');
        });

        // Modify the status enum to include 'cancelled' option
        Schema::table('kot_items', function (Blueprint $table) {
            $table->enum('status', ['pending', 'cooking', 'ready', 'cancelled'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kot_items', function (Blueprint $table) {
            // Drop foreign key and columns
            $table->dropForeign(['cancel_reason_id']);
            $table->dropColumn('cancel_reason_id');
            $table->dropColumn('cancel_reason_text');
        });

        // Revert status enum to original values
        Schema::table('kot_items', function (Blueprint $table) {
            $table->enum('status', ['pending', 'cooking', 'ready'])->nullable()->change();
        });
    }
};
