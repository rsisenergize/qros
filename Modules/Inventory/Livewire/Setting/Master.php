<?php

namespace Modules\Inventory\Livewire\Setting;

use Modules\Inventory\Entities\InventorySetting;
use Livewire\Attributes\On;
use Livewire\Component;

class Master extends Component
{

    public $settings;
    public $activeSetting;

    public function mount()
    {
        $this->settings = InventorySetting::find(restaurant()->id);
        $this->activeSetting = request('tab') != '' ? request('tab') : (user()->hasRole('Admin_'.user()->restaurant_id) ? 'purchase-order' : 'reservation');
    }

    #[On('settingsUpdated')]
    public function refreshSettings()
    {
        $this->settings->fresh();
    }

    public function render()
    {
        return view('inventory::livewire.setting.master');
    }
}
