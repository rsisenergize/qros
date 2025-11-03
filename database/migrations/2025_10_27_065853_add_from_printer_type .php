<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\LanguageSetting;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('printers', 'print_type')) {
            Schema::table('printers', function (Blueprint $table) {
                $table->enum('print_type', ['image', 'pdf'])->default('image')->after('printing_choice');
            });
        }

        // if (Schema::hasColumn('printers', 'ipv4_address')) {
        //     Schema::table('printers', function (Blueprint $table) {
        //         $table->dropColumn('ipv4_address');
        //         $table->dropColumn('thermal_or_nonthermal');
        //         $table->dropColumn('profile');
        //         $table->dropColumn('char_per_line');
        //         $table->dropColumn('ip_address');
        //         $table->dropColumn('port');
        //         $table->dropColumn('path');
        //     });
        // }
    }
};
