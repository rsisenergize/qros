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
        if (!Schema::hasColumn('kots', 'kitchen_place_id')) {
            Schema::table('kots', function (Blueprint $table) {
                $table->unsignedBigInteger('kitchen_place_id')->nullable()->after('branch_id');
                $table->foreign('kitchen_place_id')->references('id')->on('kot_places')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::table('kots', function (Blueprint $table) {
            $table->dropForeign(['kot_place_id']);
            $table->dropColumn('kot_place_id');
        });
    }

};
