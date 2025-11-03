<?php

namespace App\Imports;

use App\Models\MenuItem;
use App\Models\ItemCategory;
use App\Models\Menu;
use App\Models\KotPlace;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\DB;

class MenuItemImport implements ToModel, WithHeadingRow, WithChunkReading, WithValidation, SkipsOnError, SkipsOnFailure, WithBatchInserts
{
    use Importable, SkipsErrors, SkipsFailures;

    protected $restaurantId;
    protected $branchId;
    protected $kitchenId;
    protected $columnMapping = [];
    protected $results = [
        'total' => 0,
        'success' => 0,
        'failed' => 0,
        'skipped' => 0,
        'categories_created' => 0,
        'menus_created' => 0
    ];
    protected $errors = [];

    public function __construct($restaurantId, $branchId, $kitchenId = null, $columnMapping = [])
    {
        $this->restaurantId = $restaurantId;
        $this->branchId = $branchId;
        $this->kitchenId = $kitchenId;
        $this->columnMapping = $columnMapping;
    }

    public function model(array $row)
    {
        $this->results['total']++;

        try {
            // Map the row data using column mapping
            $mappedRow = $this->mapRowData($row);

            // Validate required fields
            if (empty($mappedRow['item_name'])) {
                $this->results['skipped']++;
                return null;
            }

            if (empty($mappedRow['category_name'])) {
                $this->results['skipped']++;
                return null;
            }

            if (empty($mappedRow['menu_name'])) {
                $this->results['skipped']++;
                return null;
            }

            if (empty($mappedRow['price']) || !is_numeric($mappedRow['price'])) {
                $this->results['skipped']++;
                return null;
            }

            // Find category by name (using JSON query for translatable field)
            $category = ItemCategory::where('branch_id', $this->branchId)
                ->whereRaw("JSON_EXTRACT(category_name, '$.en') = ?", [$mappedRow['category_name'] ?? ''])
                ->first();


            if (!$category) {
                // Auto-create the category if it doesn't exist
                $category = ItemCategory::create([
                    'category_name' => ['en' => $mappedRow['category_name']],
                    'branch_id' => $this->branchId,
                    'is_active' => true,
                ]);
                $this->results['categories_created']++;
            }

            // Find menu by name (using JSON query for translatable field)
            $menu = Menu::where('branch_id', $this->branchId)
                ->whereRaw("JSON_EXTRACT(menu_name, '$.en') = ?", [$mappedRow['menu_name'] ?? ''])
                ->first();


            if (!$menu) {
                // Auto-create the menu if it doesn't exist
                $menu = Menu::create([
                    'menu_name' => ['en' => $mappedRow['menu_name']],
                    'branch_id' => $this->branchId,
                    'is_active' => true,
                ]);
                $this->results['menus_created']++;
            }

            // Check for duplicate menu item by name and category
            $existingMenuItem = MenuItem::where('branch_id', $this->branchId)
                ->where('item_name', $mappedRow['item_name'] ?? '')
                ->where('item_category_id', $category->id)
                ->first();

            if ($existingMenuItem) {
                $this->results['skipped']++;

                return null;
            }

            // Prepare the data
            $data = [
                'item_name' => $mappedRow['item_name'] ?? '',
                'description' => $mappedRow['description'] ?? '',
                'price' => floatval($mappedRow['price'] ?? 0),
                'item_category_id' => $category->id,
                'menu_id' => $menu->id,
                'type' => $this->mapItemType($mappedRow['type'] ?? 'veg'),
                'is_available' => 1, // Default to available (1 = yes, 0 = no)
                'show_on_customer_site' => $this->mapBoolean($mappedRow['show_on_customer_site'] ?? 'yes'),
                'branch_id' => $this->branchId,
                'kot_place_id' => $this->kitchenId,
            ];

            $this->results['success']++;
            return new MenuItem($data);
        } catch (\Exception $e) {
            $this->results['failed']++;
            return null;
        }
    }

    public function rules(): array
    {
        // Return empty rules as we're handling validation in the model() method
        // The actual validation happens after mapping and transforming the data
        return [];
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 100;
    }

    private function mapItemType($type)
    {
        $type = strtolower(trim($type));

        switch ($type) {
            case 'non-veg':
            case 'nonveg':
            case 'non_veg':
            case 'non veg':
                return MenuItem::NONVEG;
            case 'egg':
                return MenuItem::EGG;
            case 'veg':
            case 'vegetarian':
            default:
                return MenuItem::VEG;
        }
    }

    private function mapBoolean($value)
    {
        $value = strtolower(trim($value));

        return in_array($value, ['yes', '1', 'true', 'y']) ? 1 : 0;
    }

    public function getResults()
    {
        return $this->results;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    private function mapRowData(array $row)
    {
        $mappedRow = [];

        // If no column mapping is provided, return the row as-is
        if (empty($this->columnMapping)) {
            return $row;
        }

        // Map the row data using the column mapping
        foreach ($this->columnMapping as $csvHeader => $mappedField) {
            if (!empty($mappedField)) {
                // Normalize the CSV header to match how WithHeadingRow processes it
                // WithHeadingRow converts headers to lowercase with underscores
                $normalizedHeader = $this->normalizeHeader($csvHeader);

                // Try both the original header and normalized header
                $value = null;
                if (isset($row[$csvHeader])) {
                    $value = $row[$csvHeader];
                } elseif (isset($row[$normalizedHeader])) {
                    $value = $row[$normalizedHeader];
                }

                // If found, clean the data and ensure proper encoding
                if ($value !== null) {
                    if (is_string($value)) {
                        // Remove BOM if present and trim whitespace
                        $value = trim($value, "\xEF\xBB\xBF");
                        $value = trim($value);
                    }
                    $mappedRow[$mappedField] = $value;
                }
            }
        }

        return $mappedRow;
    }

    private function normalizeHeader($header)
    {
        // Convert to lowercase and replace spaces with underscores
        // This matches how WithHeadingRow processes headers
        // WithHeadingRow: lowercase, spaces to underscores, removes non-alphanumeric chars
        $normalized = strtolower(trim($header));
        $normalized = preg_replace('/[^a-z0-9]/', '_', $normalized);
        // Remove consecutive underscores
        $normalized = preg_replace('/_+/', '_', $normalized);
        // Remove leading/trailing underscores
        return trim($normalized, '_');
    }
}

