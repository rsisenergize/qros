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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('terms_and_privacy_accepted')->default(false)->after('phone_code');
            $table->boolean('marketing_emails_accepted')->default(false)->after('terms_and_privacy_accepted');
        });

        Schema::table('global_settings', function (Blueprint $table) {
            $table->boolean('show_privacy_consent_checkbox')->default(false)->after('privacy_policy_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['terms_and_privacy_accepted', 'marketing_emails_accepted']);
        });

        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn('show_privacy_consent_checkbox');
        });
    }
};
