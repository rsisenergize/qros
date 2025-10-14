<?php

namespace Modules\Kiosk\Livewire\Kiosk;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\Kiosk\Services\KioskCartService;

class Dashboard extends Component
{
    public $restaurant;
    public $shopBranch;
    public $showWelcome = true;
    public $showOrderType = false;
    public $showMenu = false;
    public $showItemCustomisation = false;
    public $showCartSummary = false;
    public $showPaymentMethod = false;
    public $showOrderConfirmation = false;
    
    // Cart-related properties
    public $cartCount = 0;
    public $cartTotal = 0;
    public $orderType = 'dine_in';

    public function mount()
    {
        $this->loadCartData();
    }

    #[On('cartUpdated')]
    public function loadCartData()
    {
        $cartService = new KioskCartService();
        $this->cartCount = $cartService->getKioskCartBadgeCount($this->shopBranch->id);
        $this->cartTotal = $cartService->getCartTotal($this->shopBranch->id);
    }

    #[On('showMenu')]
    public function showMenu()
    {
        $this->resetViews();
        $this->showMenu = true;
    }

    #[On('showCartSummary')]
    public function showCartSummary()
    {
        $this->resetViews();
        $this->showCartSummary = true;
    }

    #[On('showPaymentMethod')]
    public function showPaymentMethod()
    {
        $this->resetViews();
        $this->showPaymentMethod = true;
    }

    #[On('showItemCustomisation')]
    public function showItemCustomisation()
    {
        $this->resetViews();
        $this->showItemCustomisation = true;
    }

    #[On('setOrderType')]
    public function setOrderType($orderType)
    {
        $this->orderType = $orderType;

        $cartService = new KioskCartService();
        $cartService->setKioskOrderType($this->shopBranch->id, $orderType);
        
        $this->showMenu();
    }

    public function viewCart()
    {
        $this->showCartSummary();
    }

    public function goToWelcome()
    {
        $this->resetViews();
        $this->showWelcome = true;
    }

    public function goToOrderType()
    {
        $this->resetViews();
        $this->showOrderType = true;
    }

    private function resetViews()
    {
        $this->showWelcome = false;
        $this->showOrderType = false;
        $this->showMenu = false;
        $this->showItemCustomisation = false;
        $this->showCartSummary = false;
        $this->showPaymentMethod = false;
        $this->showOrderConfirmation = false;
    }

    public function render()
    {
        return view('kiosk::livewire.kiosk.dashboard');
    }
}
