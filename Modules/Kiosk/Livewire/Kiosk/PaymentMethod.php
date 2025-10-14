<?php

namespace Modules\Kiosk\Livewire\Kiosk;

use Livewire\Component;
use Modules\Kiosk\Services\KioskCartService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTax;
use App\Models\OrderType;
use App\Models\Tax;
use App\Models\Kot;
use App\Models\KotItem;
use App\Models\Payment;
use App\Events\NewOrderCreated;
use App\Events\OrderUpdated;
use App\Models\Customer;

class PaymentMethod extends Component
{
    public $restaurant;
    public $shopBranch;
    public $paymentMethod;

    // Customer information properties
    public $customerName = '';
    public $customerEmail = '';
    public $customerPhone = '';
    public $pickupTime = '15';

    protected $listeners = [
        'refreshPaymentMethod' => '$refresh'
    ];

    public function mount($restaurant, $shopBranch)
    {
        $this->restaurant = $restaurant;
        $this->shopBranch = $shopBranch;
        $this->paymentMethod = 'due';
    }

    public function processPayment()
    {
        $kioskService = new KioskCartService();
        $cartItemList = $kioskService->getKioskCartSummary($this->shopBranch->id);

        if (empty($cartItemList['items'])) {
            return;
        }

        // Create or find customer
        $customer = $this->createOrFindCustomer();

        $orderNumberData = Order::generateOrderNumber($this->shopBranch);

        $orderTypeModel = OrderType::where('is_default', 1)
            ->where('type', $cartItemList['order_type'])
            ->first();

        $order = Order::create([
            'order_number' => $orderNumberData['order_number'],
            'formatted_order_number' => $orderNumberData['formatted_order_number'],
            'branch_id' => $this->shopBranch->id,
            'table_id' => null,
            'date_time' => now(),
            'customer_id' => $customer->id ?? null,
            'sub_total' => $cartItemList['sub_total'],
            'total' => $cartItemList['total'],
            'order_type' => $cartItemList['order_type'],
            'order_type_id' => $orderTypeModel->id ?? null,
            'custom_order_type_name' => $orderTypeModel->order_type_name ?? 'dine_in',
            'status' => 'pending_verification',
            'order_status' => $this->restaurant->auto_confirm_orders ? 'confirmed' : 'placed',
            'placed_via' => 'kiosk',
            'tax_mode' => $cartItemList['tax_mode'],
            'total_tax_amount' => $cartItemList['total_tax_amount'],
            'pickup_date' => $cartItemList['order_type'] === 'pickup' ? now() : null,
        ]);

        $transactionId = uniqid('KIOSK_TXN_', true);

        $kot = Kot::create([
            'branch_id' => $this->shopBranch->id,
            'kot_number' => (Kot::generateKotNumber($this->shopBranch) + 1),
            'order_id' => $order->id,
            'note' => null,
            'transaction_id' => $transactionId,
        ]);

        foreach ($cartItemList['items'] as $item) {
            $orderItem = OrderItem::create([
                'branch_id' => $this->shopBranch->id,
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item']['id'],
                'menu_item_variation_id' => $item['variation']['id'] ?? null,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'amount' => $item['amount'],
                'transaction_id' => $transactionId,
                'note' => null,
                'tax_amount' => $item['tax_amount'] ?? null,
                'tax_percentage' => $item['tax_percentage'] ?? null,
                'tax_breakup' => !empty($item['tax_breakup']) ? json_encode($item['tax_breakup']) : null,
            ]);

            $kotItem = KotItem::create([
                'kot_id' => $kot->id,
                'menu_item_id' => $item['menu_item']['id'],
                'menu_item_variation_id' => $item['variation']['id'] ?? null,
                'quantity' => $item['quantity'],
                'transaction_id' => $transactionId,
                'note' => null,
            ]);

            if (!empty($item['modifiers'])) {
                $modifierOptionIds = collect($item['modifiers'])->pluck('id')->all();
                $kotItem->modifierOptions()->sync($modifierOptionIds);
                $orderItem->modifierOptions()->sync($modifierOptionIds);
            }
        }

        if ($cartItemList['tax_mode'] === 'order') {
            $taxes = Tax::withoutGlobalScopes()->where('restaurant_id', $this->restaurant->id)->get();
            foreach ($taxes as $tax) {
                OrderTax::firstOrCreate([
                    'order_id' => $order->id,
                    'tax_id' => $tax->id,
                ]);
            }
        }

        Payment::create([
            'order_id' => $order->id,
            'branch_id' => $this->shopBranch->id,
            'payment_method' => $this->paymentMethod,
            'amount' => $cartItemList['total'],
        ]);

        NewOrderCreated::dispatch($order);
        event(new OrderUpdated($order, 'created'));

        $kioskService->clearKioskCart($this->shopBranch->id);
        

        // Notify kiosk UI to show the confirmation screen with dynamic details
        return $this->redirect(route('kiosk.order-confirmation', $order->uuid), true);
    }

    private function createOrFindCustomer()
    {
        $this->customerName = session('customerInfo')['name'] ?? '';
        $this->customerEmail = session('customerInfo')['email'] ?? '';
        $this->customerPhone = session('customerInfo')['phone'] ?? '';
        $this->pickupTime = session('customerInfo')['pickup_time'] ?? '';

        // If no customer information provided, return null
        if (empty($this->customerName) && empty($this->customerEmail) && empty($this->customerPhone)) {
            return null;
        }

        // Try to find existing customer by email or phone
        $customer = null;
        
        if (!empty($this->customerEmail)) {
            $customer = Customer::where('email', $this->customerEmail)
            ->where('restaurant_id', $this->restaurant->id)
            ->first();
        }
        
        if (!$customer && !empty($this->customerPhone)) {
            $customer = Customer::where('phone', $this->customerPhone)
            ->where('restaurant_id', $this->restaurant->id)
            ->first();
        }

        // If customer found, update their information if needed
        if ($customer) {
            $updated = false;
            
            if (!empty($this->customerName) && $customer->name !== $this->customerName) {
                $customer->name = $this->customerName;
                $updated = true;
            }
            
            if (!empty($this->customerEmail) && $customer->email !== $this->customerEmail) {
                $customer->email = $this->customerEmail;
                $updated = true;
            }
            
            if (!empty($this->customerPhone) && $customer->phone !== $this->customerPhone) {
                $customer->phone = $this->customerPhone;
                $updated = true;
            }
            
            if ($updated) {
                $customer->save();
            }
        } else {
            // Create new customer
            $customer = Customer::create([
                'restaurant_id' => $this->restaurant->id,
                'name' => $this->customerName,
                'email' => $this->customerEmail,
                'phone' => $this->customerPhone,
            ]);
        }

        return $customer;
    }

    public function selectPaymentMethod(string $method): void
    {
        $this->paymentMethod = $method;
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

        return view('kiosk::livewire.kiosk.payment-method', [
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
