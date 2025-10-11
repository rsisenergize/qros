<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Helper\Files;
use App\Models\PrintJob;

class CleanupPrintJobFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:print-files';

    const CUTOFF_TIME = 5;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up print job files older than 5 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of old print job files...');

        $printFolder = public_path(Files::UPLOAD_FOLDER . '/print');

        // Check if the print folder exists
        if (!File::exists($printFolder)) {
            $this->warn('Print folder does not exist: ' . $printFolder);
            return 0;
        }

        $cutoffTime = now()->subMinutes(self::CUTOFF_TIME);
        $deletedCount = 0;
        $totalSize = 0;

        try {
            // Get all files in the print folder
            $files = File::files($printFolder);

            foreach ($files as $file) {
                $filePath = $file->getPathname();
                $fileName = $file->getFilename();
                // Get file modification time
                $fileTime = \Carbon\Carbon::createFromTimestamp(File::lastModified($filePath));

                // Check if file is older than 5 minutes
                if ($fileTime->lt($cutoffTime)) {
                    $this->info("File is older than " . self::CUTOFF_TIME . " minutes: {$fileName}");
                    $fileSize = File::size($filePath);


                    // Delete the file
                    if (File::delete($filePath)) {
                        $deletedCount++;
                        $totalSize += $fileSize;
                        $this->line("Deleted: {$fileName} (Age: {$fileTime->diffForHumans()}, Size: " . $this->formatBytes($fileSize) . ")");
                    } else {
                        $this->error("Failed to delete: {$fileName}");
                    }

                    PrintJob::where('image_filename', $fileName)->delete();
                }
            }

            if ($deletedCount > 0) {
                $this->info("Cleanup completed successfully!");
                $this->info("Deleted {$deletedCount} files");
                $this->info("Total space freed: " . $this->formatBytes($totalSize));
            } else {
                $this->info("No files older than " . self::CUTOFF_TIME . " minutes found.");
            }
        } catch (\Exception $e) {
            $this->error("Error during cleanup: " . $e->getMessage());
            return 1;
        }

        if (PrintJob::where('status', 'pending')->count() == 0) {
            PrintJob::truncate();
            $this->info("Print jobs truncated successfully!");
        }

        $this->info("Cleanup completed successfully!");

        return 0;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
