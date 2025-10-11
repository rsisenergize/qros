<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class CustomerDisplay extends Component
{
    public $orderItems = [];
    public $subTotal = 0;
    public $total = 0;
    public $discount = 0;
    public $orderNumber = null;
    public $taxes = [];
    public $extraCharges = [];
    public $tip = 0;
    public $deliveryFee = 0;
    public $orderType = null;
    public $status = 'idle';
    public $cashDue = null;
    public $qrCodeImageUrl = null;
    public $formattedOrderNumber = null;

    // Poll every 1 second
    public function render()
    {
        $cart = Cache::get('customer_display_cart');

        if ($cart) {
            $this->orderNumber = $cart['order_number'];
            $this->formattedOrderNumber = $cart['formatted_order_number'];
            $this->subTotal = $cart['sub_total'];
            $this->total = $cart['total'];
            $this->discount = $cart['discount'] ?? 0;
            $this->orderItems = $cart['items'] ?? [];
            $this->taxes = $cart['taxes'] ?? [];
            $this->extraCharges = $cart['extra_charges'] ?? [];
            $this->tip = $cart['tip'] ?? 0;
            $this->deliveryFee = $cart['delivery_fee'] ?? 0;
            $this->orderType = $cart['order_type'] ?? null;
            $this->status = $cart['status'] ?? 'idle';
            $this->cashDue = $cart['cash_due'] ?? null;
            $this->qrCodeImageUrl = $cart['qr_code_image_url'] ?? null;
        } else {
            $this->orderNumber = null;
            $this->formattedOrderNumber = null;
            $this->subTotal = 0;
            $this->total = 0;
            $this->discount = 0;
            $this->orderItems = [];
            $this->taxes = [];
            $this->extraCharges = [];
            $this->tip = 0;
            $this->deliveryFee = 0;
            $this->orderType = null;
            $this->status = 'idle';
            $this->cashDue = null;
            $this->qrCodeImageUrl = null;
        }

        return view('livewire.customer-display');
    }

    public function refreshCustomerDisplay()
    {
        // This method will be called by Pusher to refresh the display
        // The render method will automatically fetch the latest data from cache
        $this->dispatch('customer-display-updated');
    }
}
