<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Livewire\CustomerDisplay;
use App\Livewire\Pos\Pos;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;

class CustomerDisplayMultiDisplayTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear any existing cache and session
        Cache::forget('active_customer_displays');
        Cache::forget('customer_display_cart');
        Session::flush();
    }

    protected function tearDown(): void
    {
        // Clean up after each test
        Cache::forget('active_customer_displays');
        Cache::forget('customer_display_cart');
        Session::flush();
        
        parent::tearDown();
    }

    /** @test */
    public function each_customer_display_gets_unique_id()
    {
        // Create instances without rendering views
        $display1 = new CustomerDisplay();
        $display1->mount();
        
        // Clear session to ensure second display gets different ID
        Session::forget('customer_display_id');
        
        $display2 = new CustomerDisplay();
        $display2->mount();

        $id1 = $display1->getDisplayId();
        $id2 = $display2->getDisplayId();

        $this->assertNotEquals($id1, $id2);
        $this->assertNotEmpty($id1);
        $this->assertNotEmpty($id2);
    }

    /** @test */
    public function displays_register_themselves_as_active()
    {
        $display1 = new CustomerDisplay();
        $display1->mount();
        
        // Clear session to ensure second display gets different ID
        Session::forget('customer_display_id');
        
        $display2 = new CustomerDisplay();
        $display2->mount();

        $activeDisplays = $display1->getActiveDisplays();

        $this->assertCount(2, $activeDisplays);
        $this->assertContains($display1->getDisplayId(), $activeDisplays);
        $this->assertContains($display2->getDisplayId(), $activeDisplays);
    }

    /** @test */
    public function displays_use_unique_cache_keys()
    {
        $display1 = new CustomerDisplay();
        $display1->mount();
        
        // Clear session to ensure second display gets different ID
        Session::forget('customer_display_id');
        
        $display2 = new CustomerDisplay();
        $display2->mount();

        $id1 = $display1->getDisplayId();
        $id2 = $display2->getDisplayId();

        $testData1 = ['order_number' => 'TEST001', 'items' => ['item1']];
        $testData2 = ['order_number' => 'TEST002', 'items' => ['item2']];

        // Set data for each display
        Cache::put("customer_display_cart_{$id1}", $testData1, now()->addMinutes(30));
        Cache::put("customer_display_cart_{$id2}", $testData2, now()->addMinutes(30));

        // Verify each display has its own data
        $this->assertEquals($testData1, Cache::get("customer_display_cart_{$id1}"));
        $this->assertEquals($testData2, Cache::get("customer_display_cart_{$id2}"));
        $this->assertNotEquals(Cache::get("customer_display_cart_{$id1}"), Cache::get("customer_display_cart_{$id2}"));
    }

    /** @test */
    public function displays_unregister_when_destroyed()
    {
        $display1 = new CustomerDisplay();
        $display1->mount();
        
        // Clear session to ensure second display gets different ID
        Session::forget('customer_display_id');
        
        $display2 = new CustomerDisplay();
        $display2->mount();

        $id1 = $display1->getDisplayId();
        $id2 = $display2->getDisplayId();

        // Verify both are registered
        $activeDisplays = $display1->getActiveDisplays();
        $this->assertCount(2, $activeDisplays);

        // Unregister one display
        $display1->unregisterActiveDisplay();

        // Verify only one remains
        $activeDisplays = $display2->getActiveDisplays();
        $this->assertCount(1, $activeDisplays);
        $this->assertContains($id2, $activeDisplays);
        $this->assertNotContains($id1, $activeDisplays);
    }

    /** @test */
    public function pos_updates_all_active_displays()
    {
        $display1 = new CustomerDisplay();
        $display1->mount();
        
        // Clear session to ensure second display gets different ID
        Session::forget('customer_display_id');
        
        $display2 = new CustomerDisplay();
        $display2->mount();

        $pos = new Pos();

        $testData = [
            'order_number' => 'TEST003',
            'formatted_order_number' => 'TEST-003',
            'items' => [['name' => 'Test Item', 'qty' => 1, 'price' => 10]],
            'sub_total' => 10,
            'total' => 10,
            'status' => 'idle'
        ];

        // Simulate POS update
        $pos->updateCustomerDisplayCache($testData);

        // Verify both displays received the update
        $id1 = $display1->getDisplayId();
        $id2 = $display2->getDisplayId();

        $this->assertEquals($testData, Cache::get("customer_display_cart_{$id1}"));
        $this->assertEquals($testData, Cache::get("customer_display_cart_{$id2}"));
    }

    /** @test */
    public function pos_updates_global_cache_when_no_displays_registered()
    {
        // Clear any existing displays
        Cache::forget('active_customer_displays');
        
        $pos = new Pos();

        $testData = [
            'order_number' => 'TEST004',
            'formatted_order_number' => 'TEST-004',
            'items' => [['name' => 'Test Item', 'qty' => 1, 'price' => 10]],
            'sub_total' => 10,
            'total' => 10,
            'status' => 'idle'
        ];

        // Simulate POS update
        $pos->updateCustomerDisplayCache($testData);

        // Verify global cache was updated
        $this->assertEquals($testData, Cache::get('customer_display_cart'));
        
        // Verify no display-specific caches exist
        $this->assertEmpty(Cache::get('active_customer_displays', []));
    }

    /** @test */
    public function customer_display_falls_back_to_global_cache()
    {
        // Set up global cache with data
        $testData = [
            'order_number' => 'TEST005',
            'formatted_order_number' => 'TEST-005',
            'items' => [['name' => 'Test Item', 'qty' => 1, 'price' => 10]],
            'sub_total' => 10,
            'total' => 10,
            'status' => 'idle'
        ];
        
        Cache::put('customer_display_cart', $testData, now()->addMinutes(30));
        
        // Create display without registering it
        $display = new CustomerDisplay();
        $display->mount();
        
        // Clear the display-specific cache to force fallback
        Cache::forget("customer_display_cart_{$display->getDisplayId()}");
        
        // Simulate render
        $display->render();
        
        // Verify the display has the data from global cache
        $this->assertEquals($testData['order_number'], $display->orderNumber);
        $this->assertEquals($testData['items'], $display->orderItems);
    }
} 