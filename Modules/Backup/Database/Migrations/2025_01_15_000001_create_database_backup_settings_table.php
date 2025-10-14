<?php

use App\Models\StorageSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Backup\Models\DatabaseBackupSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('database_backup_settings')) {
            return;
        }

        Schema::create('database_backup_settings', function (Blueprint $table) {
            $table->id();
            $table->string('license_type', 20)->nullable();
            $table->string('purchase_code')->nullable();
            $table->timestamp('purchased_on')->nullable();
            $table->timestamp('supported_until')->nullable();
            $table->boolean('notify_update')->default(1);
            $table->boolean('is_enabled')->default(false);
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->time('backup_time')->default('02:00:00');
            $table->integer('retention_days')->default(30);
            $table->integer('max_backups')->default(10);
            $table->boolean('include_files')->default(false);
            $table->boolean('include_modules')->default(false);
            $table->enum('storage_location', ['local', 'storage_setting'])->default('local');
            $table->json('storage_config')->nullable();
            $table->timestamps();
        });

        $setting = DB::table('file_storage_settings')->where('status', 'enabled')->first();

        if ($setting) {
            DatabaseBackupSetting::firstOrCreate([], [
                'is_enabled' => false,
                'frequency' => 'daily',
                'backup_time' => '02:00:00',
                'retention_days' => 30,
                'max_backups' => 10,
                'storage_location' => $setting->filesystem == 'local' ? 'local' : 'storage_setting',

            ]);
        } else {
            DatabaseBackupSetting::firstOrCreate([], [
                'is_enabled' => false,
                'frequency' => 'daily',
                'backup_time' => '02:00:00',
                'retention_days' => 30,
                'max_backups' => 10,
                'storage_location' => 'local',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_backup_settings');
    }
};
