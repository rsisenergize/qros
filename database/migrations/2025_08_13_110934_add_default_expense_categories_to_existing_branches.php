<?php

use App\Models\Branch;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (!class_exists(Branch::class) || !class_exists(ExpenseCategory::class)) {
            return; // Avoid errors if models are missing
        }

        $defaultCategories = [
            [
                'name' => 'Rent',
                'description' => 'Monthly rent for restaurant space',
                'is_active' => true,
            ],
            [
                'name' => 'Utilities',
                'description' => 'Electricity, water, gas, and other utilities',
                'is_active' => true,
            ],
            [
                'name' => 'Salaries',
                'description' => 'Employee salaries and wages',
                'is_active' => true,
            ],
            [
                'name' => 'Ingredients',
                'description' => 'Food ingredients and raw materials',
                'is_active' => true,
            ],
            [
                'name' => 'Equipment',
                'description' => 'Kitchen equipment and appliances',
                'is_active' => true,
            ],
            [
                'name' => 'Marketing',
                'description' => 'Advertising and promotional expenses',
                'is_active' => true,
            ],
            [
                'name' => 'Insurance',
                'description' => 'Business insurance and liability coverage',
                'is_active' => true,
            ],
            [
                'name' => 'Maintenance',
                'description' => 'Repairs and maintenance costs',
                'is_active' => true,
            ],
            [
                'name' => 'Licenses',
                'description' => 'Business licenses and permits',
                'is_active' => true,
            ],
            [
                'name' => 'Miscellaneous',
                'description' => 'Other miscellaneous expenses',
                'is_active' => true,
            ],
        ];

       $branches = Branch::all();

        foreach ($branches as $branch) {
                foreach ($defaultCategories as $category) {
                    $exists = ExpenseCategory::where('branch_id', $branch->id)
                        ->where('name', $category['name'])
                        ->exists();

                    if (! $exists) {
                    DB::table('expense_categories')->insert([
                    'branch_id'   => $branch->id,
                    'name'        => $category['name'],
                    'description' => $category['description'],
                    'is_active'   => $category['is_active'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                    }
                }
            }
    }

    public function down(): void
    {
        $defaultCategoryNames = [
            'Rent',
            'Utilities',
            'Salaries',
            'Ingredients',
            'Equipment',
            'Marketing',
            'Insurance',
            'Maintenance',
            'Licenses',
            'Miscellaneous',
        ];

        ExpenseCategory::whereIn('name', $defaultCategoryNames)->delete();
    }
};
