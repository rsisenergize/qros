<?php

use Illuminate\Database\Migrations\Migration;

use App\Models\DesktopApplication;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the desktop application
        $desktopApplication = DesktopApplication::whereNotNull('mac_file_path')->first();

        if ($desktopApplication) {
            $desktopApplication->mac_file_path = DesktopApplication::MAC_FILE_PATH;
            $desktopApplication->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
