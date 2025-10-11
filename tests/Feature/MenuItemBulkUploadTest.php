<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\Branch;
use App\Models\ItemCategory;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Livewire\Menu\MenuItems;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MenuItemBulkUploadTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $restaurant;
    protected $branch;
    protected $category;
    protected $menu;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->restaurant = Restaurant::factory()->create();
        $this->branch = Branch::factory()->create(['restaurant_id' => $this->restaurant->id]);
        $this->user = User::factory()->create(['restaurant_id' => $this->restaurant->id]);
        $this->category = ItemCategory::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'category_name' => 'Main Course'
        ]);
        $this->menu = Menu::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'menu_name' => 'Dinner Menu'
        ]);
    }

    public function test_bulk_upload_modal_can_be_opened()
    {
        $this->actingAs($this->user);

        Livewire::test(MenuItems::class)
            ->call('showBulkUploadModal')
            ->assertSet('showBulkUploadModal', true);
    }

    public function test_bulk_upload_modal_can_be_closed()
    {
        $this->actingAs($this->user);

        Livewire::test(MenuItems::class)
            ->set('showBulkUploadModal', true)
            ->call('hideBulkUploadModal')
            ->assertSet('showBulkUploadModal', false);
    }

    public function test_file_validation_works()
    {
        $this->actingAs($this->user);

        // Test invalid file type
        $invalidFile = UploadedFile::fake()->create('test.txt', 100);

        Livewire::test(MenuItems::class)
            ->set('uploadFile', $invalidFile)
            ->call('updatedUploadFile')
            ->assertHasErrors(['uploadFile']);
    }

    public function test_sample_file_download_works()
    {
        $this->actingAs($this->user);

        $response = Livewire::test(MenuItems::class)
            ->call('downloadSampleFile');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv');
        $response->assertHeader('Content-Disposition', 'attachment; filename="menu_items_sample.csv"');
    }

    public function test_bulk_upload_processes_valid_csv()
    {
        Storage::fake('local');

        $this->actingAs($this->user);

        // Create a valid CSV content
        $csvContent = "item_name,description,price,category_name,menu_name,type,is_available,show_on_customer_site\n";
        $csvContent .= "Test Item,Test Description,10.99,Main Course,Dinner Menu,veg,yes,yes\n";

        $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        Livewire::test(MenuItems::class)
            ->set('uploadFile', $file)
            ->call('processBulkUpload')
            ->assertSet('uploadSuccess', true)
            ->assertSet('uploadProgress', 100);

        // Verify menu item was created
        $this->assertDatabaseHas('menu_items', [
            'item_name' => 'Test Item',
            'restaurant_id' => $this->restaurant->id,
            'branch_id' => $this->branch->id,
        ]);
    }

    public function test_bulk_upload_handles_invalid_data()
    {
        Storage::fake('local');

        $this->actingAs($this->user);

        // Create CSV with invalid category
        $csvContent = "item_name,description,price,category_name,menu_name,type,is_available,show_on_customer_site\n";
        $csvContent .= "Test Item,Test Description,10.99,Invalid Category,Dinner Menu,veg,yes,yes\n";

        $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        Livewire::test(MenuItems::class)
            ->set('uploadFile', $file)
            ->call('processBulkUpload');

        // Should not create menu item with invalid category
        $this->assertDatabaseMissing('menu_items', [
            'item_name' => 'Test Item',
        ]);
    }

    public function test_duplicate_menu_items_are_skipped()
    {
        Storage::fake('local');

        $this->actingAs($this->user);

        // Create existing menu item
        MenuItem::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'branch_id' => $this->branch->id,
            'item_name' => 'Existing Item',
            'item_category_id' => $this->category->id,
            'menu_id' => $this->menu->id,
        ]);

        // Try to import same item
        $csvContent = "item_name,description,price,category_name,menu_name,type,is_available,show_on_customer_site\n";
        $csvContent .= "Existing Item,Test Description,10.99,Main Course,Dinner Menu,veg,yes,yes\n";

        $file = UploadedFile::fake()->createWithContent('test.csv', $csvContent);

        Livewire::test(MenuItems::class)
            ->set('uploadFile', $file)
            ->call('processBulkUpload');

        // Should still have only one item
        $this->assertEquals(1, MenuItem::where('item_name', 'Existing Item')->count());
    }
}
