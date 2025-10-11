<?php

namespace App\Livewire\Order;

use App\Models\Tax;
use App\Models\Order;
use App\Models\Table;
use App\Models\Printer;
use Livewire\Component;
use App\Models\MenuItem;
use App\Models\OrderTax;
use App\Models\OrderItem;
use App\Models\OrderCharge;
use Livewire\Attributes\On;
use App\Traits\PrinterSetting;
use App\Models\KotCancelReason;
use App\Models\DeliveryExecutive;
use App\Models\Kot;
use App\Models\KotItem;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class OrderDetail extends Component
{

    use LivewireAlert, PrinterSetting;

    public $order;
    public $taxes;
    public $total = 0;
    public $subTotal = 0;
    public $showOrderDetail = false;
    public $showAddCustomerModal = false;
    public $showTableModal = false;
    public $cancelOrderModal = false;
    public $deleteOrderModal = false;
    public $tableNo;
    public $tableId;
    public $orderStatus;
    public $discountAmount = 0;
    public $deliveryExecutives;
    public $deliveryExecutive;
    public $orderProgressStatus;
    public $fromPos = null;
    public $confirmDeleteModal = false;
    public $cancelReasons;
    public $cancelReason;
    public $cancelReasonText;
    public $totalTaxAmount = 0;
    public $taxMode;
    public $currencyId;

    public function mount()
    {
        $this->total = 0;
        $this->subTotal = 0;
        $this->taxes = Tax::all();
        $this->deliveryExecutives = DeliveryExecutive::where('status', 'available')->get();
        if ($this->order) {
            $this->deliveryExecutive = $this->order->delivery_executive_id;
        }
        $this->cancelReasons = KotCancelReason::where('cancel_order', true)->get();
    }

    public function printOrder($orderId)
    {


        $orderPlaces = \App\Models\MultipleOrder::with('printerSetting')->get();

        foreach ($orderPlaces as $orderPlace) {
            $printerSetting = $orderPlace->printerSetting;
        }

        try {

            switch ($printerSetting?->printing_choice) {
            case 'directPrint':

                $this->handleOrderPrint($orderId);
                    break;
            default:
                $url = route('orders.print', $orderId);
                $this->dispatch('print_location', $url);
                    break;
            }
        } catch (\Throwable $e) {
            $this->alert('error', __('messages.printerNotConnected') . ' : ' . $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        }
    }

    #[On('showOrderDetail')]
    public function showOrder($id, $fromPos = null)
    {
        $this->order = Order::with('items', 'items.menuItem', 'items.menuItemVariation', 'payments', 'cancelReason')->find($id);
        $this->orderStatus = $this->order->status;
        $this->fromPos = $fromPos;
        $this->orderProgressStatus = $this->order->order_status->value;
        $restaurant = restaurant();
        $this->currencyId = $restaurant->currency_id;
        $this->taxMode = $this->order?->tax_mode ?? ($this->restaurant->tax_mode ?? 'order');

        if ($this->taxMode === 'item') {
            $this->totalTaxAmount = $this->order?->total_tax_amount ?? 0;
        }
        $this->showOrderDetail = true;
    }

    #[On('setTable')]
    public function setTable(Table $table)
    {
        $this->tableNo = $table->table_code;
        $this->tableId = $table->id;

        if ($this->order) {
            $currentOrder = Order::where('id', $this->order->id)->first();

            Table::where('id', $currentOrder->table_id)->update([
                'available_status' => 'available'
            ]);

            $currentOrder->update(['table_id' => $table->id]);

            if ($this->order->date_time->format('d-m-Y') == now()->format('d-m-Y')) {
                Table::where('id', $this->tableId)->update([
                    'available_status' => 'running'
                ]);
            }

            $this->order->fresh();
            $this->dispatch('showOrderDetail', id: $this->order->id);
        }

        $this->dispatch('posOrderSuccess');
        $this->dispatch('refreshOrders');
        $this->dispatch('refreshPos');

        $this->showTableModal = false;
    }

    public function saveOrderStatus()
    {
        if ($this->order) {
            Order::where('id', $this->order->id)->update(['status' => $this->orderStatus]);

            $this->dispatch('posOrderSuccess');
            $this->dispatch('refreshOrders');
            $this->dispatch('refreshPos');
        }
    }

    public function showAddCustomer($id)
    {
        $this->order = Order::find($id);
        $this->showAddCustomerModal = true;
    }

    public function deleteOrderItems($id)
    {
        $orderItem = OrderItem::find($id);

        if ($orderItem) {
            $kotItems = KotItem::where('menu_item_id', $orderItem->menu_item_id)
                ->where('menu_item_variation_id', $orderItem->menu_item_variation_id)
                ->where('quantity', $orderItem->quantity)
                ->whereHas('kot', function($query) use ($orderItem) {
                    $query->where('order_id', $orderItem->order_id);
                })
                ->get();

            foreach ($kotItems as $kotItem) {
                $kotItem->delete();
            }
        }

        OrderItem::destroy($id);

        if ($this->order) {
            $this->order->refresh();

            if ($this->order->items->count() === 0) {
                $this->deleteOrder($this->order->id);

                return;
            }

            $this->total = 0;
            $this->subTotal = 0;

            foreach ($this->order->items as $value) {
                $this->subTotal = ($this->subTotal + $value->amount);
                $this->total = ($this->total + $value->amount);
            }

            foreach ($this->taxes as $value) {
                $this->total = ($this->total + (($value->tax_percent / 100) * $this->subTotal));
            }

            Order::where('id', $this->order->id)->update([
                'sub_total' => $this->subTotal,
                'total' => $this->total
            ]);
        }

        $this->dispatch('refreshPos');
    }

    public function updatedOrderProgressStatus($value)
    {
        if (empty($this->order) || is_null($value)) {
            return;
        }

        $this->order->update(['order_status' => $value]);
        $this->orderProgressStatus = $value;

        if ($value === 'confirmed') {
            $this->order->kot->each(function ($kot) {
                $kot->update(['status' => 'in_kitchen']);
            });
        }

        $this->dispatch('posOrderSuccess');
        $this->dispatch('refreshOrders');
        $this->dispatch('refreshPos');
    }

    public function saveOrder($action)
    {

        switch ($action) {
        case 'bill':
            $successMessage = __('messages.billedSuccess');
            $status = 'billed';
            $tableStatus = 'running';
                break;

        case 'kot':
                return $this->redirect(route('pos.show', $this->order->table_id), navigate: true);
        }

        $taxes = Tax::all();

        Order::where('id', $this->order->id)->update([
            'date_time' => now(),
            'status' => $status
        ]);

        if ($status == 'billed') {
            $totalTaxAmount = 0;

            foreach ($this->order->kot as $kot) {
                foreach ($kot->items as $item) {
                    $price = (($item->menu_item_variation_id) ? $item->menuItemVariation->price : $item->menuItem->price);
                    $amount = $price * $item->quantity;

                    // Calculate tax for item-level taxation
                    $taxAmount = 0;
                    $taxPercentage = 0;
                    $taxBreakup = null;

                    if ($this->taxMode === 'item') {
                        $menuItem = $item->menuItem;
                        $taxes = $menuItem->taxes ?? collect();
                        $isInclusive = restaurant()->tax_inclusive ?? false;

                        if ($taxes->isNotEmpty()) {
                            $taxResult = MenuItem::calculateItemTaxes($price, $taxes, $isInclusive);
                            $taxAmount = $taxResult['tax_amount'] * $item->quantity;
                            $taxPercentage = $taxResult['tax_percentage'];
                            $taxBreakup = json_encode($taxResult['tax_breakdown']);
                            $totalTaxAmount += $taxAmount;
                        }
                    }

                    OrderItem::create([
                        'order_id' => $this->order->id,
                        'menu_item_id' => $item->menu_item_id,
                        'menu_item_variation_id' => $item->menu_item_variation_id,
                        'quantity' => $item->quantity,
                        'price' => $price,
                        'amount' => $amount,
                        'tax_amount' => $taxAmount,
                        'tax_percentage' => $taxPercentage,
                        'tax_breakup' => $taxBreakup,
                    ]);
                }
            }

            if ($this->taxMode === 'order') {
                foreach ($taxes as $value) {
                    OrderTax::create([
                        'order_id' => $this->order->id,
                        'tax_id' => $value->id
                    ]);
                }
            }

            $this->total = 0;
            $this->subTotal = 0;

            foreach ($this->order->load('items')->items as $value) {
                if ($this->taxMode === 'item') {
                    $isInclusive = restaurant()->tax_inclusive ?? false;
                    if ($isInclusive) {
                        // For inclusive tax: subtract tax from amount to get subtotal
                        $this->subTotal += ($value->amount - ($value->tax_amount ?? 0));
                    } else {
                        // For exclusive tax: amount is subtotal
                        $this->subTotal += $value->amount;
                    }
                } else {
                    $this->subTotal += $value->amount;
                }
                $this->total += $value->amount;
            }

            // Calculate taxes for order-level taxation
            if ($this->taxMode === 'order') {
                foreach ($taxes as $value) {
                    $taxAmount = ($value->tax_percent / 100) * $this->subTotal;
                    $this->total += $taxAmount;
                    $totalTaxAmount += $taxAmount;
                }
            } elseif ($this->taxMode === 'item') {
                $isInclusive = restaurant()->tax_inclusive ?? false;
                if (!$isInclusive) {
                    // For exclusive taxes, add tax to total
                    $this->total += $totalTaxAmount;
                }
            }

            // Apply discounts
            if ($this->order->discount_type === 'percent') {
                $this->discountAmount = round(($this->subTotal * $this->order->discount_value) / 100, 2);
            } elseif ($this->order->discount_type === 'fixed') {
                $this->discountAmount = min($this->order->discount_value, $this->subTotal);
            }

            $this->total -= $this->discountAmount ?? 0;

            Order::where('id', $this->order->id)->update([
                'sub_total' => $this->subTotal,
                'total' => $this->total,
                'discount_amount' => $this->discountAmount,
                'total_tax_amount' => $totalTaxAmount,
            ]);
        }

        Table::where('id', $this->tableId)->update([
            'available_status' => $tableStatus
        ]);


        $this->alert('success', $successMessage, [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        if ($status == 'billed') {
            $this->dispatch('showOrderDetail', id: $this->order->id);
            $this->dispatch('posOrderSuccess');
            $this->dispatch('refreshOrders');
            $this->dispatch('resetPos');
        }
    }

    public function showPayment($id)
    {
        $this->dispatch('showPaymentModal', id: $id);
    }

    public function cancelOrderStatus($id)
    {
        // Validate that a cancel reason is provided
        if (!$this->cancelReason && !$this->cancelReasonText) {
            $this->alert('error', __('modules.settings.cancelReasonRequired'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close'),
            ]);
            return;
        }

        if ($id) {
            $order = Order::find($id);

            if ($order) {
                $order->update([
                    'status' => 'canceled',
                    'order_status' => 'cancelled',
                    'cancel_reason_id' => $this->cancelReason,
                    'cancel_reason_text' => $this->cancelReasonText,
                ]);

                if ($order->table_id) {
                    Table::where('id', $order->table_id)->update([
                        'available_status' => 'available',
                    ]);
                }

                $this->alert('success', __('messages.orderCanceled'), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close'),
                ]);

                $this->confirmDeleteModal = false;
                $this->cancelReason = null;
                $this->cancelReasonText = null;

                return $this->redirect(route('pos.index'), navigate: true);
            }
        }
    }

    public function cancelOrder($id)
    {
        // Validate that a cancel reason is provided
        if (!$this->cancelReason && !$this->cancelReasonText) {
            $this->alert('error', __('modules.settings.cancelReasonRequired'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close'),
            ]);
            return;
        }

        $order = Order::find($id);

        if ($order) {
            $order->update([
                'status' => 'canceled',
                'order_status' => 'cancelled',
                'cancel_reason_id' => $this->cancelReason,
                'cancel_reason_text' => $this->cancelReasonText,

            ]);
            $order->kot()->delete();
            $order->payments()->delete();

            if ($order->table_id) {
                Table::where('id', $order->table_id)->update([
                    'available_status' => 'available',
                ]);
            }
            $this->cancelOrderModal = false;
            $this->confirmDeleteModal = false;
            $this->cancelReason = null;
            $this->cancelReasonText = null;
            $this->dispatch('showOrderDetail', id: $this->order->id);
            $this->dispatch('posOrderSuccess');
            $this->dispatch('refreshOrders');

            $this->alert('success', __('messages.orderCanceled'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);

            if ($this->fromPos) {
                return $this->redirect(route('pos.index'), navigate: true);
            } else {
                $this->dispatch('resetPos');
            }
        }
    }

    public function paymentReceived($orderId, $status)
    {
        $order = Order::with('payments')->find($orderId);

        if (!$order) {
            $this->alert('error', __('messages.orderNotFound'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
            return;
        }

        if ($status === 'received') {
            $amountPaid = $order->payments->sum('amount');
            $order->update([
                'status' => 'paid',
                'amount_paid' => $amountPaid
            ]);
        } elseif ($status === 'not_received') {
            $latestPayment = $order->payments->last();
            if ($latestPayment) {
                $latestPayment->delete();
            }
            $order->update(['status' => 'payment_due']);
        }

        $this->alert('success', __('messages.statusUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        $this->dispatch('showOrderDetail', id: $this->order->id);
        $this->dispatch('refreshOrders');
        $this->dispatch('refreshPos');
    }

    public function deleteOrder($id)
    {
        $order = Order::find($id);

        if (!$order) {
            $this->alert('error', __('messages.orderNotFound'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
            return;
        }

        if ($order->table_id) {
            Table::where('id', $order->table_id)->update(['available_status' => 'available']);
        }
        // Delete associated KOT records
        $order->kot()->delete();

        $order->delete();


        $this->deleteOrderModal = false;
        $this->showOrderDetail = false;
        $order = null;
        $this->order = null;

        $this->alert('success', __('messages.orderDeleted'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);


        if ($this->fromPos) {
            return $this->redirect(route('pos.index'), navigate: true);
        }
        else {

            $this->dispatch('refreshOrders');
            $this->dispatch('refreshPos');
            $this->dispatch('refreshKots');
        }

    }

    public function saveDeliveryExecutive()
    {
        $this->order->update(['delivery_executive_id' => $this->deliveryExecutive]);
        $this->order->fresh();
        $this->alert('success', __('messages.deliveryExecutiveAssigned'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function removeCharge($chargeId)
    {
        $charge = OrderCharge::find($chargeId);

        if ($charge) {
            $chargeAmount = $charge->charge->getAmount($this->order->sub_total - ($this->order->discount_amount ?? 0));
            $charge->delete();
            $this->order->refresh();
            $this->total = $this->order->total - $chargeAmount;
            $this->order->update(['total' => $this->total]);
        }
    }

    public function updatePaymentMethod($id, $paymentMethod)
    {
        if (!$id || !$paymentMethod || !$this->order) {
            return;
        }

        $payment = $this->order->payments()->whereId($id)->first();

        if (!$payment) {
            return;
        }

        $payment->payment_method = $paymentMethod;
        $payment->save();

        $hasPaymentDue = $this->order->payments->contains('payment_method', 'due');

        $newStatus = $hasPaymentDue ? 'payment_due' : 'paid';

        if ($this->order->status !== $newStatus) {
            $this->order->status = $newStatus;
            $this->order->save();
        }

        $this->alert('success', __('messages.statusUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        $this->dispatch('showOrderDetail', id: $this->order->id);
        $this->dispatch('refreshOrders');
    }

    /**
     * Get the display price for an item (base price without tax for inclusive items)
     */
    public function getItemDisplayPrice($key)
    {
        if ($this->taxMode === 'item' && isset($this->orderItemTaxDetails[$key])) {
            return $this->orderItemTaxDetails[$key]['display_price'] ?? 0;
        }

        // Check if we have session data arrays (for active POS session)
        if (isset($this->orderItemList[$key])) {
            $basePrice = isset($this->orderItemVariation[$key]) ? $this->orderItemVariation[$key]->price : $this->orderItemList[$key]->price;
            $modifierPrice = $this->orderItemModifiersPrice[$key] ?? 0;
            return $basePrice + $modifierPrice;
        }

        // For existing order items (when viewing order details), calculate from the order item itself
        if ($this->order && isset($this->order->items[$key])) {
            $orderItem = $this->order->items[$key];
            $basePrice = !is_null($orderItem->menuItemVariation) ? $orderItem->menuItemVariation->price : $orderItem->menuItem->price;
            $modifierPrice = $orderItem->modifierOptions->sum('price');

            // If tax is inclusive, calculate the display price without tax
            if (restaurant()->tax_inclusive && restaurant()->tax_mode === 'item') {
                $menuItem = $orderItem->menuItem;
                $taxes = $menuItem->taxes ?? collect();
                $itemPriceWithModifiers = $basePrice + $modifierPrice;

                if ($taxes->isNotEmpty()) {
                    $taxPercent = $taxes->sum('tax_percent');
                    $displayPrice = $itemPriceWithModifiers / (1 + $taxPercent / 100);
                    return $displayPrice;
                }
            }

            return $basePrice + $modifierPrice;
        }

        return 0;
    }

    public function render()
    {
        return view('livewire.order.order-detail');
    }

}
