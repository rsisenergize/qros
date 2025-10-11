<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupModulesStructureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a temporary Modules directory structure for testing
        $modulesPath = base_path('Modules');
        if (!File::exists($modulesPath)) {
            File::makeDirectory($modulesPath, 0755, true);
        }

        // Create some test modules
        $testModules = [
            'Modules/TestModule1/test.php',
            'Modules/TestModule2/config.php',
            'Modules/Backup/test.php',
        ];

        foreach ($testModules as $moduleFile) {
            $fullPath = base_path($moduleFile);
            $dir = dirname($fullPath);
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            File::put($fullPath, '<?php echo "test"; ?>');
        }
    }

    protected function tearDown(): void
    {
        // Clean up test modules
        $modulesPath = base_path('Modules');
        if (File::exists($modulesPath)) {
            File::deleteDirectory($modulesPath);
        }

        parent::tearDown();
    }

    public function test_backup_with_modules_excluded_should_preserve_modules_folder_structure()
    {
        // Create a backup with modules excluded
        $this->artisan('backup:database', [
            '--include-files' => 'true',
            '--include-modules' => 'false',
            '--type' => 'manual'
        ])->assertExitCode(0);

        // Find the backup file
        $backupFiles = File::glob(storage_path('app/backups/*.zip'));
        $this->assertNotEmpty($backupFiles, 'Backup file should be created');

        $backupFile = $backupFiles[0];

        // Extract and check the ZIP contents
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($backupFile) === ZipArchive::ER_OK);

        // Check that Modules folder exists in the backup
        $modulesFolderExists = false;
        $modulesContents = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (strpos($filename, 'Modules/') === 0) {
                $modulesContents[] = $filename;
                if ($filename === 'Modules/' || $filename === 'Modules') {
                    $modulesFolderExists = true;
                }
            }
        }

        $zip->close();

        // The Modules folder should exist but be empty (no contents)
        $this->assertTrue($modulesFolderExists, 'Modules folder should exist in backup');
        $this->assertCount(1, $modulesContents, 'Only the Modules folder should exist, not its contents');
        $this->assertContains('Modules/', $modulesContents, 'Modules folder should be present');
    }

    public function test_backup_with_modules_included_should_include_modules_contents()
    {
        // Create a backup with modules included
        $this->artisan('backup:database', [
            '--include-files' => 'true',
            '--include-modules' => 'true',
            '--type' => 'manual'
        ])->assertExitCode(0);

        // Find the backup file
        $backupFiles = File::glob(storage_path('app/backups/*.zip'));
        $this->assertNotEmpty($backupFiles, 'Backup file should be created');

        $backupFile = $backupFiles[0];

        // Extract and check the ZIP contents
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($backupFile) === ZipArchive::ER_OK);

        // Check that Modules folder and its contents exist in the backup
        $modulesContents = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (strpos($filename, 'Modules/') === 0) {
                $modulesContents[] = $filename;
            }
        }

        $zip->close();

        // The Modules folder and its contents should exist
        $this->assertGreaterThan(1, count($modulesContents), 'Modules folder and its contents should be included');
        $this->assertContains('Modules/', $modulesContents, 'Modules folder should be present');
        $this->assertContains('Modules/TestModule1/test.php', $modulesContents, 'Module files should be included');
        $this->assertContains('Modules/TestModule2/config.php', $modulesContents, 'Module files should be included');
        $this->assertContains('Modules/Backup/test.php', $modulesContents, 'Module files should be included');
    }
}
