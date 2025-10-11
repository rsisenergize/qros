<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class RestaurantPaymentSettings extends Component
{
    public $activeTab = 'superadminPaymentSetting';
    protected $queryString = ['activeTab'];

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.settings.restaurant-payment-settings');
    }
}
