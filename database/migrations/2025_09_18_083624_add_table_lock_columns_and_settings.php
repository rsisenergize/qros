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

        // Create table_sessions table for managing table locks
        Schema::create('table_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('table_id');
            $table->unsignedBigInteger('locked_by_user_id')->nullable();
            $table->datetime('locked_at')->nullable();
            $table->datetime('last_activity_at')->nullable();
            $table->string('session_token')->unique()->nullable();
            $table->timestamps();

            $table->foreign('table_id')->references('id')->on('tables')->onDelete('cascade');
            $table->foreign('locked_by_user_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['table_id', 'locked_by_user_id']);
            $table->index('last_activity_at');
        });


        // Add a column to set the table lock timeout (in minutes) for each restaurant
        Schema::table('restaurants', function (Blueprint $table) {
            $table->integer('table_lock_timeout_minutes')->default(10);
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_sessions');

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('table_lock_timeout_minutes');
        });
    }
};
