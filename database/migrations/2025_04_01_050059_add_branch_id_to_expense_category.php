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
        if (!Schema::hasColumn('expense_categories', 'branch_id')) {
            Schema::table('expense_categories', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->after('id');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade')->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
