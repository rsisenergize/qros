<?php

namespace Modules\Kiosk\Livewire\Kiosk;

use Livewire\Component;

class Welcome extends Component
{
    public $restaurant;
    public $shopBranch;

    public function mount($restaurant, $shopBranch)
    {
        $this->restaurant = $restaurant;
        $this->shopBranch = $shopBranch;
    }

    public function render()
    {
        return view('kiosk::livewire.kiosk.welcome');
    }
}
