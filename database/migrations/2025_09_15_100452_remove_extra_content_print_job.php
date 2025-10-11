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

        if (Schema::hasColumn('print_jobs', 'image_path')) {
            Schema::table('print_jobs', function (Blueprint $table) {
                $table->dropColumn('image_path');
            });
        }

        if (Schema::hasColumn('print_jobs', 'html_content')) {
            Schema::table('print_jobs', function (Blueprint $table) {
                $table->dropColumn('html_content');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('print_jobs', function (Blueprint $table) {
            $table->dropColumn('html_content');
        });
    }
};
