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
        if (Schema::hasTable('database_backups')) {
            return;
        }

        Schema::create('database_backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('file_path');
            $table->string('file_size')->nullable();
            $table->enum('status', ['completed', 'failed', 'in_progress'])->default('in_progress');
            $table->text('error_message')->nullable();
            $table->enum('backup_type', ['manual', 'scheduled'])->default('manual');
            $table->string('version')->nullable();
            $table->string('stored_on')->default('local');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_backups');
    }
};
