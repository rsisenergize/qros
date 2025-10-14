<?php

namespace Modules\Kiosk\Livewire\Kiosk;

use Livewire\Component;

class OrderConfirmation extends Component
{
    public $restaurant;
    public $shopBranch;
    public $order;

    public function mount($restaurant, $shopBranch, $order)
    {
        $this->restaurant = $restaurant;
        $this->shopBranch = $shopBranch;
        $this->order = $order;
    }

    public function render()
    {
        return view('kiosk::livewire.kiosk.order-confirmation');
    }
}
