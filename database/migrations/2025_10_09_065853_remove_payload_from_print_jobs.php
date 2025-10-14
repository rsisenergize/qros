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
        Schema::table('print_jobs', function (Blueprint $table) {
            if (Schema::hasColumn('print_jobs', 'payload')) {
                $table->dropColumn('payload');
            }
            $table->text('error')->nullable()->after('status');
        });

        LanguageSetting::where('language_code', 'gr')->update(['language_code' => 'el']);
        User::withoutGlobalScopes()->where('locale', 'gr')->update(['locale' => 'el']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('print_jobs', function (Blueprint $table) {
            if (Schema::hasColumn('print_jobs', 'payload')) {
                $table->longText('payload')->nullable()->after('status');
            }
            $table->dropColumn('error');
        });
    }
};
