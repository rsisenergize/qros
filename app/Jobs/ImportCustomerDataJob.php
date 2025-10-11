<?php

namespace App\Jobs;

use App\Imports\CustomerImport;
use App\Imports\CustomersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImportCustomerDataJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    protected $filePath;
    protected $restaurantId;

    public function __construct($filePath, $restaurantId)
    {
        $this->filePath = $filePath;
        $this->restaurantId = $restaurantId;
    }

    public function handle()
    {
        if (!Storage::exists($this->filePath)) {
            Log::error('Import file not found', ['file_path' => $this->filePath]);
            return;
        }

        try {
            Log::info('Starting customer import', [
                'file_path' => $this->filePath,
                'restaurant_id' => $this->restaurantId
            ]);

            $import = new CustomerImport($this->restaurantId);
            Excel::import($import, Storage::path($this->filePath));

            Log::info('Customer import completed successfully', [
                'file_path' => $this->filePath,
                'restaurant_id' => $this->restaurantId,
                'imported_count' => $import->getImportedCount(),
                'skipped_count' => $import->getSkippedCount()
            ]);

        } catch (\Exception $e) {
            Log::error('Customer import failed', [
                'file_path' => $this->filePath,
                'restaurant_id' => $this->restaurantId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            // Clean up the uploaded file
            Storage::delete($this->filePath);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Customer import job failed', [
            'file_path' => $this->filePath,
            'restaurant_id' => $this->restaurantId,
            'error' => $exception->getMessage()
        ]);

        // Clean up the uploaded file even if job failed
        if (Storage::exists($this->filePath)) {
            Storage::delete($this->filePath);
        }
    }
}
