<?php

namespace Modules\Kiosk\Livewire\Kiosk;

use Livewire\Component;
use Modules\Kiosk\Services\KioskCartService;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class CartSummary extends Component
{
    use LivewireAlert;
    
    protected $listeners = ['refreshCartSummary' => '$refresh'];
    
    public $restaurant;
    public $shopBranch;
    
    // Customer Information
    public $customerName = '';
    public $customerEmail = '';
    public $customerPhone = '';
    public $pickupTime = '15';
    public $orderType = 'dine_in';

    public function mount($restaurant, $shopBranch)
    {
        $this->restaurant = $restaurant;
        $this->shopBranch = $shopBranch;
    }

    public function removeFromCart($itemId)
    {
        $cartService = new KioskCartService();
        $result = $cartService->removeKioskItem($itemId);
        
        if ($result['success']) {
            $this->alert('success', 'Item removed from cart', [
                'toast' => true,
                'position' => 'top-end',
                'timer' => 2000,
            ]);
        }
    }

    public function updateQuantity($itemId, $change)
    {
        $cartService = new KioskCartService();
        $result = $cartService->updateKioskItemQuantity($itemId, $change);
        
        if ($result['success']) {
            $this->alert('success', $result['message'], [
                'toast' => true,
                'position' => 'top-end',
                'timer' => 1500,
            ]);
        }
    }

    public function proceedToPayment()
    {
        $this->validate([
            'customerName' => 'required|string|min:2',
            'customerEmail' => 'required|email',
            'customerPhone' => 'required|string|min:10',
        ]);

        
        session(['customerInfo' => [
            'name' => $this->customerName,
            'email' => $this->customerEmail,
            'phone' => $this->customerPhone,
            'pickup_time' => $this->pickupTime,
        ]]);
        
        // Dispatch event to proceed to payment screen
        $this->dispatch('proceedToPayment');
    }

    public function backToMenu()
    {
        $this->dispatch('showMenuScreen');
    }

    public function render()
    {
        $kioskService = new KioskCartService();
        $cartItemList = $kioskService->getKioskCartSummary($this->shopBranch->id);
        
        $subtotal = $cartItemList['sub_total'];
        $total = $cartItemList['total'];
        $totalTaxAmount = $cartItemList['total_tax_amount'];
        $taxBreakdown = $cartItemList['tax_breakdown'];
        $taxMode = $cartItemList['tax_mode'];
        $cartCount = $cartItemList['count'];

        return view('kiosk::livewire.kiosk.cart-summary', [
            'cartItemList' => $cartItemList,
            'subtotal' => $subtotal,
            'total' => $total,
            'totalTaxAmount' => $totalTaxAmount,
            'taxBreakdown' => $taxBreakdown,
            'taxMode' => $taxMode,
            'cartCount' => $cartCount,
        ]);
    }
}
