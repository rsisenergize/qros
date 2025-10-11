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
        Schema::table('global_settings', function (Blueprint $table) {
            $table->text('meta_keyword')->nullable()->change();
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->text('meta_keyword')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('global_settings', 'meta_keywords')) {
            Schema::table('global_settings', function (Blueprint $table) {
                $table->dropColumn('meta_keywords');
            });
        }
    }
};
