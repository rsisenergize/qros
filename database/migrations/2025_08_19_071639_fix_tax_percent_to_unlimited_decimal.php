<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('taxes', function (Blueprint $table) {
            // Change column to decimal without precision/scale
            $table->string('tax_percent')->change();
        });
    }

    public function down(): void
    {
        Schema::table('taxes', function (Blueprint $table) {
            // Rollback to old definition
            $table->decimal('tax_percent', 16, 2)->change();
        });
    }
};
