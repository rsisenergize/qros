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
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->change();
            $table->string('expense_title')->nullable()->after('expense_category_id');
            $table->unsignedBigInteger('branch_id')->after('expense_category_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['expense_title', 'branch_id']);
            $table->string('payment_method')->nullable(false)->change();
        });
    }

};
