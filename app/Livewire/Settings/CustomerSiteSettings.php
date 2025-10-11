<?php
namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\CartHeaderSetting;
use App\Models\CartHeaderImage;
use App\Helper\Files;

class CustomerSiteSettings extends Component
{

    use LivewireAlert, WithFileUploads;

    public $settings;
    public bool $customerLoginRequired;
    public bool $allowCustomerOrders;
    public bool $allowCustomerDeliveryOrders;
    public bool $allowCustomerPickupOrders;
    public bool $isWaiterRequestEnabled;
    public bool $isWaiterRequestEnabledOnDesktop;
    public bool $isWaiterRequestEnabledOnMobile;
    public bool $isWaiterRequestEnabledOpenByQr;
    public string $defaultReservationStatus;
    public $facebook;
    public $instagram;
    public $twitter;
    public $yelp;
    public bool $tableRequired;
    public bool $allowDineIn;
    public $metaKeyword;
    public $metaDescription;
    public bool $enableTipShop;
    public bool $enableTipPos;
    public bool $pwaAlertShow;
    public bool $autoConfirmOrders;
    public $pickupDaysRange;
    public bool $showVeg;
    public bool $showHalal;
    public $activeTab = 'settings';
    public $headerType = 'text';
    public $headerText;
    public $headerImages = [];
    public $newImages = [];
    public $cartHeaderSetting;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->defaultReservationStatus = $this->settings->default_table_reservation_status;
        $this->customerLoginRequired = $this->settings->customer_login_required;
        $this->allowCustomerOrders = $this->settings->allow_customer_orders;
        $this->allowCustomerDeliveryOrders = $this->settings->allow_customer_delivery_orders;
        $this->allowCustomerPickupOrders = $this->settings->allow_customer_pickup_orders;
        $this->pickupDaysRange = $this->settings->pickup_days_range;
        $this->isWaiterRequestEnabled = $this->settings->is_waiter_request_enabled;
        $this->enableTipShop = $this->settings->enable_tip_shop;
        $this->enableTipPos = $this->settings->enable_tip_pos;
        $this->autoConfirmOrders = $this->settings->auto_confirm_orders;
        $this->showVeg = $this->settings->show_veg;
        $this->showHalal = $this->settings->show_halal;
        $this->isWaiterRequestEnabledOnDesktop = $this->settings->is_waiter_request_enabled_on_desktop;
        $this->isWaiterRequestEnabledOnMobile = $this->settings->is_waiter_request_enabled_on_mobile;
        $this->isWaiterRequestEnabledOpenByQr = $this->settings->is_waiter_request_enabled_open_by_qr;

        $this->tableRequired = $this->settings->table_required;
        $this->allowDineIn = $this->settings->allow_dine_in_orders;
        $this->facebook = $this->settings->facebook_link;
        $this->instagram = $this->settings->instagram_link;
        $this->twitter = $this->settings->twitter_link;
        $this->yelp = $this->settings->yelp_link;
        $this->metaKeyword = $this->settings->meta_keyword;
        $this->metaDescription = $this->settings->meta_description;
        $this->pwaAlertShow = $this->settings->is_pwa_install_alert_show;

        // Initialize header settings
        $this->cartHeaderSetting = $this->settings->cartHeaderSetting;
        if ($this->cartHeaderSetting) {
            $this->headerType = $this->cartHeaderSetting->header_type;
            $this->headerText = $this->cartHeaderSetting->header_text;
            $this->headerImages = $this->cartHeaderSetting->images;
        } else {
            $this->headerText = __('messages.frontHeroHeading');
        }
        
        // Initialize newImages as empty array
        $this->newImages = [];
    }

    public function updatedHeaderType($value)
    {
        $this->headerType = $value;
        $this->dispatch('headerTypeChanged', $value);
    }

    public function updatedNewImages()
    {
        $this->validate([
            'newImages.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    }

    public function submitForm()
    {
        $this->validate([
            'defaultReservationStatus' => 'required|in:Confirmed,Checked_In,Cancelled,No_Show,Pending',
            'headerType' => 'required|in:text,image',
            'headerText' => 'required_if:headerType,text',
            'newImages.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if (!$this->allowDineIn && !$this->allowCustomerDeliveryOrders && !$this->allowCustomerPickupOrders) {
            $this->allowCustomerOrders = false;
        }

        $this->settings->default_table_reservation_status = $this->defaultReservationStatus;
        $this->settings->customer_login_required = $this->customerLoginRequired;
        $this->settings->allow_customer_orders = $this->allowCustomerOrders;
        $this->settings->allow_customer_delivery_orders = $this->allowCustomerDeliveryOrders;
        $this->settings->allow_customer_pickup_orders = $this->allowCustomerPickupOrders;
        $this->settings->pickup_days_range = $this->pickupDaysRange;
        $this->settings->is_waiter_request_enabled = $this->isWaiterRequestEnabled;
        $this->settings->is_waiter_request_enabled_on_desktop = $this->isWaiterRequestEnabledOnDesktop;
        $this->settings->is_waiter_request_enabled_on_mobile = $this->isWaiterRequestEnabledOnMobile;
        $this->settings->is_waiter_request_enabled_open_by_qr = $this->isWaiterRequestEnabledOpenByQr;
        $this->settings->table_required = $this->tableRequired;
        $this->settings->allow_dine_in_orders = $this->allowDineIn;
        $this->settings->facebook_link = $this->facebook;
        $this->settings->instagram_link = $this->instagram;
        $this->settings->twitter_link = $this->twitter;
        $this->settings->yelp_link = $this->yelp;
        $this->settings->meta_keyword = $this->metaKeyword;
        $this->settings->meta_description = $this->metaDescription;
        $this->settings->enable_tip_shop = $this->enableTipShop;
        $this->settings->enable_tip_pos = $this->enableTipPos;
        $this->settings->auto_confirm_orders = $this->autoConfirmOrders;
        $this->settings->is_pwa_install_alert_show = $this->pwaAlertShow;
        $this->settings->show_veg = $this->showVeg;
        $this->settings->show_halal = $this->showHalal;
        $this->settings->save();

        // Save header settings
        $this->saveHeaderSettings();

        $this->dispatch('settingsUpdated');

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function saveHeaderSettings()
    {
        if (!$this->cartHeaderSetting) {
            $this->cartHeaderSetting = CartHeaderSetting::create([
                'restaurant_id' => $this->settings->id,
                'header_type' => $this->headerType,
                'header_text' => $this->headerText,
            ]);
        } else {
            $this->cartHeaderSetting->update([
                'header_type' => $this->headerType,
                'header_text' => $this->headerText,
            ]);
        }

        // Handle image uploads using Files::uploadLocalOrS3
        if ($this->headerType === 'image' && $this->newImages) {
            foreach ($this->newImages as $image) {
                if ($image) {
                    $imagePath = Files::uploadLocalOrS3($image, 'cart_header_images', width: 1280, height: 224);
                    CartHeaderImage::create([
                        'cart_header_setting_id' => $this->cartHeaderSetting->id,
                        'image_path' => $imagePath,
                        'sort_order' => $this->cartHeaderSetting->images()->count(),
                    ]);
                }
            }
            // Clear the newImages after upload
            $this->newImages = [];
        }
        
        // Refresh the header images
        $this->headerImages = $this->cartHeaderSetting->fresh()->images;
    }

    public function removeImage($imageId)
    {
        $image = CartHeaderImage::find($imageId);
        if ($image && $image->cart_header_setting_id === $this->cartHeaderSetting->id) {
            // Delete the file from storage
            if ($image->image_path) {
                Files::deleteFile($image->image_path, 'cart_header_images');
            }
            $image->delete();
            $this->headerImages = $this->cartHeaderSetting->fresh()->images;
        }
    }

    public function render()
    {
        return view('livewire.settings.customer-site-settings');
    }

}
