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
        // Drop existing foreign keys
        Schema::table('kot_places', function (Blueprint $table) {
            $table->dropForeign(['printer_id']);
        });

        Schema::table('order_places', function (Blueprint $table) {
            $table->dropForeign(['printer_id']);
        });

        // Add new foreign keys with 'set null'
        Schema::table('kot_places', function (Blueprint $table) {
            $table->foreign('printer_id')->references('id')->on('printers')->onDelete('set null');
        });

        Schema::table('order_places', function (Blueprint $table) {
            $table->foreign('printer_id')->references('id')->on('printers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Revert to 'cascade' if rolling back
        Schema::table('kot_places', function (Blueprint $table) {
            $table->dropForeign(['printer_id']);
            $table->foreign('printer_id')->references('id')->on('printers')->onDelete('cascade');
        });

        Schema::table('order_places', function (Blueprint $table) {
            $table->dropForeign(['printer_id']);
            $table->foreign('printer_id')->references('id')->on('printers')->onDelete('cascade');
        });
    }

};
