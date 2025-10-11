<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CustomerImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue, SkipsEmptyRows
{
    protected $restaurantId;
    protected $skippedCount = 0;
    protected $importedCount = 0;

    public function __construct($restaurantId)
    {
        $this->restaurantId = $restaurantId;
    }

    public function collection(Collection $rows)
    {
        Log::info('Starting customer import collection', [
            'total_rows' => $rows->count(),
            'restaurant_id' => $this->restaurantId
        ]);

        // Debug: Log the first few rows to see what we're getting
        Log::info('First few rows data', [
            'rows' => $rows->take(3)->toArray()
        ]);

        foreach ($rows as $index => $row) {
            try {
                // Convert row to array for easier handling
                $rowData = $row->toArray();

                // Skip if required fields are empty
                if (empty($rowData['name']) && empty($rowData['phone']) && empty($rowData['email'])) {
                    $this->skippedCount++;
                    Log::info('Skipped row due to empty required fields', [
                        'row_index' => $index,
                        'row_data' => $rowData
                    ]);
                    continue;
                }

                // Check for duplicate customer by phone or email
                $existingCustomer = Customer::where('restaurant_id', $this->restaurantId)
                                            ->where(function($query) use ($rowData) {
                                                $query->where('phone', $rowData['phone'] ?? null)
                                                    ->orWhere('email', $rowData['email'] ?? null);
                                            })
                                            ->first();

                // If customer already exists, skip this row and continue
                if ($existingCustomer) {
                    $this->skippedCount++;
                    Log::info('Skipped duplicate customer', [
                        'row_index' => $index,
                        'name' => $rowData['name'] ?? 'N/A',
                        'phone' => $rowData['phone'] ?? 'N/A',
                        'email' => $rowData['email'] ?? 'N/A',
                        'existing_customer_id' => $existingCustomer->id
                    ]);
                    continue;
                }

                // Create a new customer record
                $customer = Customer::create([
                    'name'        => $rowData['name'] ?? null,
                    'phone'       => $rowData['phone'] ?? null,
                    'email'       => $rowData['email'] ?? null,
                    'restaurant_id' => $this->restaurantId,
                ]);

                $this->importedCount++;
                Log::info('Successfully imported customer', [
                    'row_index' => $index,
                    'customer_id' => $customer->id,
                    'name' => $rowData['name'] ?? 'N/A',
                    'phone' => $rowData['phone'] ?? 'N/A',
                    'email' => $rowData['email'] ?? 'N/A'
                ]);

            } catch (\Exception $e) {
                $this->skippedCount++;
                Log::error('Failed to import customer', [
                    'row_index' => $index,
                    'name' => $rowData['name'] ?? 'N/A',
                    'phone' => $rowData['phone'] ?? 'N/A',
                    'email' => $rowData['email'] ?? 'N/A',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        Log::info('Customer import batch completed', [
            'imported_count' => $this->importedCount,
            'skipped_count' => $this->skippedCount,
            'restaurant_id' => $this->restaurantId
        ]);
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

}






