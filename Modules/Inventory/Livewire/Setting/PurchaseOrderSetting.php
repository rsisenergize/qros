<?php

namespace Modules\Inventory\Livewire\Setting;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Modules\Inventory\Entities\InventorySetting;

class PurchaseOrderSetting extends Component
{
    use LivewireAlert;

    public $settings;
    public bool $allowPurchaseOrder;

    public function mount()
    {
        $this->settings = InventorySetting::first();

        $this->allowPurchaseOrder = $this->settings->allow_auto_purchase;
    }

    public function submitForm()
    {
        $this->settings->allow_auto_purchase = $this->allowPurchaseOrder;
        $this->settings->save();

        $this->alert('success', __('messages.settingsUpdated'));
    }

    public function render()
    {
        return view('inventory::livewire.setting.purchase-order-setting');
    }
}
