<?php

namespace Modules\Kiosk\Livewire\Kiosk;

use Livewire\Component;

class OrderType extends Component
{
    public $restaurant;
    public $shopBranch;
    public $orderType;
    
    public function mount($restaurant, $shopBranch)
    {
        $this->restaurant = $restaurant;
        $this->shopBranch = $shopBranch;
    }

    public function setOrderType($orderType)
    {
        $this->orderType = $orderType;
        $this->dispatch('setOrderType', $orderType);
    }

    public function render()
    {
        return view('kiosk::livewire.kiosk.order-type');
    }
}
