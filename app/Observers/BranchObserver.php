<?php

namespace App\Observers;

use App\Models\Branch;
use App\Models\MenuItem;
use App\Models\OrderType;
use App\Models\OnboardingStep;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ExpenseCategory;

class BranchObserver
{

    public function creating(Branch $branch)
    {
        $branch->generateUniqueHash();
    }

    public function created(Branch $branch)
    {

        // Add Onboarding Steps
        OnboardingStep::create(['branch_id' => $branch->id]);

        $branch->generateQrCode();

        $branch->generateKotSetting();

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($daysOfWeek as $day) {
            DB::table('reservation_settings')->insert([
                [
                    'day_of_week' => $day,
                    'time_slot_start' => '08:00:00',
                    'time_slot_end' => '11:00:00',
                    'time_slot_difference' => 30,
                    'slot_type' => 'Breakfast',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'branch_id' => $branch->id,
                ],
                [
                    'day_of_week' => $day,
                    'time_slot_start' => '12:00:00',
                    'time_slot_end' => '17:00:00',
                    'time_slot_difference' => 60,
                    'slot_type' => 'Lunch',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'branch_id' => $branch->id,
                ],
                [
                    'day_of_week' => $day,
                    'time_slot_start' => '18:00:00',
                    'time_slot_end' => '22:00:00',
                    'time_slot_difference' => 60,
                    'slot_type' => 'Dinner',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'branch_id' => $branch->id,
                ]
            ]);
        }

        // Create Kitchen place
        $kotPlace = $branch->kotPlaces()->create([
            'name' => 'Default Kitchen',
            'branch_id' => $branch->id,
            'printer_id' => null, // Will update after printer is created
            'type' => 'food',
            'is_active' => true,
            'is_default' => true,
        ]);

        // Update all menu items for this branch to set kot_place_id to the default kitchen
        MenuItem::where('branch_id', $branch->id)->update(['kot_place_id' => $kotPlace->id]);

        // Create default order place
        $orderPlace = $branch->orderPlaces()->create([
            'name' => 'Default POS Terminal',
            'branch_id' => $branch->id,
            'printer_id' => null, // Will update after printer is created
            'type' => 'vegetarian',
            'is_active' => true,
            'is_default' => true,
        ]);

        // Create printer and assign KOT and Order place IDs
        $printer = $branch->printerSettings()->create([
            'name' => 'Default Thermal Printer',
            'restaurant_id' => $branch->restaurant_id,
            'branch_id' => $branch->id,
            'is_active' => true,
            'is_default' => true,
            'printing_choice' => 'browserPopupPrint',
            'kots' => [$kotPlace->id],
            'orders' => [$orderPlace->id],
        ]);

        // Ensure default order types are not duplicated for this branch
        $defaultOrderTypes = ['Dine In', 'Delivery', 'Pickup'];
        $defaultOrderTypesSlug = ['dine_in', 'delivery', 'pickup'];

        foreach ($defaultOrderTypes as $index => $type) {
            $branch->orderTypes()->create([
                'order_type_name' => $type,
                'branch_id' => $branch->id,
                'slug' => $defaultOrderTypesSlug[$index],
                'is_default' => true,
                'type' => $defaultOrderTypesSlug[$index]
            ]);
        }

        // Update KOT and Order place with printer_id
        $kotPlace->printer_id = $printer->id;
        $kotPlace->save();

        /**
         * ✅ Create Default Expense Categories
         */
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

        foreach ($defaultCategories as $category) {

            // Create each default expense category for the branch
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
