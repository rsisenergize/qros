<?php

namespace App\Livewire\Kot;

use Carbon\Carbon;
use App\Models\Kot;
use App\Models\KotItem;
use App\Models\OrderItem;
use Livewire\Component;
use App\Models\KotPlace;
use App\Models\KotSetting;
use Livewire\Attributes\On;
use App\Models\KotCancelReason;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Kots extends Component
{
    use LivewireAlert;

    protected $listeners = ['refreshKots' => '$refresh'];
    public $filterOrders;
    public $dateRangeType;
    public $startDate;
    public $endDate;
    public $kotSettings;
    public $confirmDeleteKotModal = false;
    public $cancelReasons;
    public $kot;
    public $cancelReasonText;
    public $cancelReason;
    public $selectedCancelKotId;
    public $kotPlace;
    public $showAllKitchens = false;
    public $selectedKitchen = '';
    public $search = '';
    public $confirmDeleteKotItemModal = false;
    public $selectedCancelKotItemId;
    public $cancelItemReason;
    public $cancelItemReasonText;

    public function getSelectedKotItemProperty()
    {
        if (!$this->selectedCancelKotItemId) {
            return null;
        }

        return KotItem::with(['menuItem', 'menuItemVariation', 'modifierOptions'])
            ->find($this->selectedCancelKotItemId);
    }

    public function mount($kotPlace = null, $showAllKitchens = false)
    {
        $tz = timezone();
        
        // Load date range type from cookie
        $this->kotSettings = KotSetting::first();
        $this->dateRangeType = request()->cookie('kots_date_range_type', 'today');
        $this->filterOrders = ($this->kotSettings->default_status == 'pending') ? 'pending_confirmation' : 'in_kitchen';
        $this->startDate = Carbon::now($tz)->startOfWeek()->format('m/d/Y');
        $this->endDate = Carbon::now($tz)->endOfWeek()->format('m/d/Y');
        $this->cancelReasons = KotCancelReason::where('cancel_kot', true)->get();
        $this->showAllKitchens = $showAllKitchens;

        if ($this->showAllKitchens) {
            // For all kitchens view, don't set a specific kotPlace
            $this->kotPlace = null;
        } elseif (!in_array('Kitchen', restaurant_modules())) {
            $this->kotPlace = KotPlace::with('printerSetting')->first();
        } else {
            $this->kotPlace = $kotPlace;
        }

        $this->setDateRange();
    }


    public function setDateRange()
    {
        $tz = timezone();
        
        switch ($this->dateRangeType) {
            case 'today':
                $this->startDate = Carbon::now($tz)->startOfDay()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->startOfDay()->format('m/d/Y');
                break;

            case 'lastWeek':
                $this->startDate = Carbon::now($tz)->subWeek()->startOfWeek()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->subWeek()->endOfWeek()->format('m/d/Y');
                break;

            case 'last7Days':
                $this->startDate = Carbon::now($tz)->subDays(7)->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->startOfDay()->format('m/d/Y');
                break;

            case 'currentMonth':
                $this->startDate = Carbon::now($tz)->startOfMonth()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->startOfDay()->format('m/d/Y');
                break;

            case 'lastMonth':
                $this->startDate = Carbon::now($tz)->subMonth()->startOfMonth()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->subMonth()->endOfMonth()->format('m/d/Y');
                break;

            case 'currentYear':
                $this->startDate = Carbon::now($tz)->startOfYear()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->startOfDay()->format('m/d/Y');
                break;

            case 'lastYear':
                $this->startDate = Carbon::now($tz)->subYear()->startOfYear()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->subYear()->endOfYear()->format('m/d/Y');
                break;

            default:
                $this->startDate = Carbon::now($tz)->startOfWeek()->format('m/d/Y');
                $this->endDate = Carbon::now($tz)->endOfWeek()->format('m/d/Y');
                break;
        }
    }

    #[On('setStartDate')]
    public function setStartDate($start)
    {
        $this->startDate = $start;
    }

    #[On('setEndDate')]
    public function setEndDate($end)
    {
        $this->endDate = $end;
    }

    #[On('showCancelKotModal')]
    public function showCancelKotModal($id = null)
    {
        $this->confirmDeleteKotModal = true;
        $this->selectedCancelKotId = $id;
    }

    #[On('showCancelKotItemModal')]
    public function showCancelKotItemModal($id)
    {
        $this->confirmDeleteKotItemModal = true;
        $this->selectedCancelKotItemId = $id;

        // Reset form fields
        $this->cancelItemReason = null;
        $this->cancelItemReasonText = null;
    }
    public function updatedDateRangeType($value)
    {
        cookie()->queue(cookie('kots_date_range_type', $value, 60 * 24 * 30)); // 30 days
    }

    public function deleteKot($id)
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

        // If "Other" is selected, custom reason text is mandatory
        if ($this->cancelReason) {
            $selectedReason = KotCancelReason::find($this->cancelReason);
            if ($selectedReason && strtolower($selectedReason->reason) === 'other' && !$this->cancelReasonText) {
                $this->alert('error', __('modules.settings.customReasonRequired'), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close'),
                ]);
                return;
            }
        }

        $kot = Kot::findOrFail($id);
        $order = $kot->order;
        $kotCounts = $order->kot()->whereNot('status', 'cancelled')->count();

        // Update cancel reason info
        $kot->cancel_reason_id = $this->cancelReason;
        $kot->cancel_reason_text = $this->cancelReasonText;
        $kot->status = 'cancelled';
        $kot->save();


        // If this is the only KOT in the order, cancel the order
        if ($kotCounts === 1) {
            $order->status = 'canceled';
            $order->order_status = 'cancelled';
            $order->save();

            if ($order->table) {
                $order->table->update(['available_status' => 'available']);
            }
        } else {
            // Recalculate order totals if order is not cancelled
            $this->recalculateOrderTotals($order);
        }

        // Optional: soft delete kot or destroy it
        // Kot::destroy($id); // if using force delete

        $this->confirmDeleteKotModal = false;

        $this->reset(['cancelReason', 'cancelReasonText', 'selectedCancelKotId']);

        $this->dispatch('refreshKots');

        // Dispatch event to refresh POS component if it's viewing this order
        $this->dispatch('refreshPosOrder', orderId: $order->id);
    }

    public function deleteKotItem($itemId)
    {
        // Validate that a cancel reason is provided
        if (!$this->cancelItemReason && !$this->cancelItemReasonText) {
            $this->alert('error', __('modules.settings.cancelReasonRequired'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close'),
            ]);
            return;
        }

        // If "Other" is selected, custom reason text is mandatory
        if ($this->cancelItemReason) {
            $selectedReason = KotCancelReason::find($this->cancelItemReason);
            if ($selectedReason && strtolower($selectedReason->reason) === 'other' && !$this->cancelItemReasonText) {
                $this->alert('error', __('modules.settings.customReasonRequired'), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close'),
                ]);
                return;
            }
        }

        $kotItem = KotItem::findOrFail($itemId);
        $kot = $kotItem->kot;
        $order = $kot->order;

        // Get the actual reason text from KotCancelReason model
        $cancelReasonText = null;
        if ($this->cancelItemReason) {
            $cancelReason = KotCancelReason::find($this->cancelItemReason);
            $cancelReasonText = $cancelReason ? $cancelReason->reason : null;
        }

        // Use custom text if provided, otherwise use the reason from the model
        $finalReasonText = $this->cancelItemReasonText ?: $cancelReasonText;

        // Update cancel reason info for the KOT item
        Log::info('About to save with cancelItemReason: ' . ($this->cancelItemReason ?? 'null') . ', cancelItemReasonText: ' . ($this->cancelItemReasonText ?? 'null') . ', finalReasonText: ' . ($finalReasonText ?? 'null'));

        $kotItem->cancel_reason_id = $this->cancelItemReason;
        $kotItem->cancel_reason_text = $finalReasonText;
        $kotItem->status = 'cancelled';

        Log::info('KotItem before save:', [
            'id' => $kotItem->id,
            'cancel_reason_id' => $kotItem->cancel_reason_id,
            'cancel_reason_text' => $kotItem->cancel_reason_text,
            'status' => $kotItem->status
        ]);

        $result = $kotItem->save();

        Log::info('KotItem save result: ' . ($result ? 'true' : 'false'));
        Log::info('KotItem after save:', [
            'id' => $kotItem->id,
            'cancel_reason_id' => $kotItem->cancel_reason_id,
            'cancel_reason_text' => $kotItem->cancel_reason_text,
            'status' => $kotItem->status
        ]);

        // Handle corresponding order item if it exists
        $this->handleOrderItemCancellation($kotItem, $order);

        // Recalculate order totals
        $this->recalculateOrderTotals($order);

        // Check if all items in the KOT are now cancelled
        $totalItems = KotItem::where('kot_id', $kot->id)->count();
        $cancelledItems = KotItem::where('kot_id', $kot->id)->where('status', 'cancelled')->count();

        if ($totalItems === $cancelledItems) {
            // All items are cancelled, cancel the entire KOT
            $kot->cancel_reason_id = $this->cancelItemReason;
            $kot->cancel_reason_text = $finalReasonText;
            $kot->status = 'cancelled';
            $kot->save();

            // Check if this is the only KOT in the order
            $kotCounts = $order->kot()->whereNot('status', 'cancelled')->count();
            if ($kotCounts === 0) {
                $order->status = 'canceled';
                $order->order_status = 'cancelled';
                $order->save();

                if ($order->table) {
                    $order->table->update(['available_status' => 'available']);
                }
            }
        } else {
                   }

        $this->confirmDeleteKotItemModal = false;
        $this->reset(['cancelItemReason', 'cancelItemReasonText', 'selectedCancelKotItemId']);

        $this->alert('success', __('modules.order.kotItemCancelledSuccessfully'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close'),
        ]);

        $this->dispatch('refreshKots');

        // Dispatch event to refresh POS component if it's viewing this order
        $this->dispatch('refreshPosOrder', orderId: $order->id);

        // Reload the page after a short delay to show the success message
        $this->js('setTimeout(() => window.location.reload(), 500)');
    }

    public function render()
    {

        $tz = timezone();

        $start = Carbon::createFromFormat('m/d/Y', $this->startDate, $tz)
            ->startOfDay()
            ->setTimezone('UTC')
            ->toDateTimeString();

        $end = Carbon::createFromFormat('m/d/Y', $this->endDate, $tz)
            ->endOfDay()
            ->setTimezone('UTC')
            ->toDateTimeString();

        if ($this->showAllKitchens) {
            // For all kitchens view - show KOTs from all kitchens
            $kots = Kot::withCount('items')
                ->orderBy('id', 'desc')
                ->join('orders', 'kots.order_id', '=', 'orders.id')
                ->where('orders.date_time', '>=', $start)
                ->where('orders.date_time', '<=', $end)
                ->where('orders.status', '<>', 'draft')
                ->with([
                    'items.menuItem',
                    'order',
                    'order.waiter',
                    'order.table',
                    'order.orderType',
                    'items.menuItemVariation',
                    'items.modifierOptions',
                    'cancelReason'
                ]);

            // Filter by kitchen if selected
            if ($this->selectedKitchen) {
                $kots = $kots->whereHas('items.menuItem', function ($q) {
                    $q->where('kot_place_id', $this->selectedKitchen);
                });
            }

            // Search functionality
            if ($this->search) {
                $kots = $kots->where(function ($q) {
                    $q->where('kots.kot_number', 'like', '%' . $this->search . '%')
                        ->orWhere('orders.order_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('order.waiter', function ($waiterQuery) {
                            $waiterQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('order.table', function ($tableQuery) {
                            $tableQuery->where('table_code', 'like', '%' . $this->search . '%');
                        });
                });
            }

            // Waiter role filter
            if (user()->hasRole('Waiter_' . user()->restaurant_id)) {
                $kots = $kots->where('orders.waiter_id', user()->id);
            }

            $kots = $kots->get();
        } elseif (module_enabled('Kitchen') && in_array('Kitchen', restaurant_modules())) {
            // Original kitchen module logic
            $kots = Kot::withCount(['items' => function ($query) {
                $query->whereHas('menuItem', function ($q) {
                    $q->where('kitchen_place_id', $this->kotPlace?->id)
                        ->orWhereNull('kitchen_place_id');
                });
            }])->orderBy('id', 'desc')
                ->join('orders', 'kots.order_id', '=', 'orders.id')
                ->where('orders.date_time', '>=', $start)->where('orders.date_time', '<=', $end)
                ->where('orders.status', '<>', 'draft')
                ->whereHas('items.menuItem', function ($q) {
                    $q->where('kot_place_id', $this->kotPlace?->id);
                })
                ->with([
                    'items' => function ($query) {
                        $query->whereHas('menuItem', function ($q) {
                            $q->where('kot_place_id', $this->kotPlace?->id);
                        })->with(['menuItem', 'menuItemVariation', 'modifierOptions']);
                    },
                    'items.menuItem',
                    'order',
                    'order.waiter',
                    'order.table',
                    'order.orderType',
                    'items.menuItemVariation',
                    'items.modifierOptions',
                    'cancelReason'
                ]);

            if (user()->hasRole('Waiter_' . user()->restaurant_id)) {
                $kots = $kots->where('orders.waiter_id', user()->id);
            }

            $kots = $kots->get();
        } else {
            // Original non-kitchen module logic
            $kots = Kot::withCount('items')->orderBy('id', 'desc')
                ->join('orders', 'kots.order_id', '=', 'orders.id')
                ->where('orders.date_time', '>=', $start)
                ->where('orders.date_time', '<=', $end)
                ->where('orders.status', '<>', 'draft')
                ->with('items', 'items.menuItem', 'order', 'order.waiter', 'order.table', 'items.menuItemVariation', 'items.modifierOptions', 'cancelReason');

            if (user()->hasRole('Waiter_' . user()->restaurant_id)) {
                $kots = $kots->where('orders.waiter_id', user()->id);
            }

            $kots = $kots->get();
        }

        if ($this->kotSettings->default_status == 'pending') {
            $inKitchen = $kots->filter(function ($order) {
                return $order->status == 'in_kitchen';
            });
        } else {
            $inKitchen = $kots->filter(function ($order) {
                return $order->status == 'in_kitchen' || $order->status == 'pending_confirmation';
            });
        }

        $foodReady = $kots->filter(function ($order) {
            return $order->status == 'food_ready';
        });

        $pendingConfirmation = $kots->filter(function ($order) {
            return $order->status == 'pending_confirmation';
        });

        $cancelled = $kots->filter(function ($order) {
            return $order->status == 'cancelled';
        });

        switch ($this->filterOrders) {
            case 'in_kitchen':
                $kotList = $inKitchen;
                break;

            case 'food_ready':
                $kotList = $foodReady;
                break;

            case 'pending_confirmation':
                $kotList = $pendingConfirmation;
                break;

            case 'cancelled':
                $kotList = $cancelled;
                break;

            default:
                $kotList = $kots;
                break;
        }

        $kotSettings = $this->kotSettings;
        $cancelReasons = $this->cancelReasons;
        $kitchens = KotPlace::where('is_active', true)->get();

        return view('livewire.kot.kots', [
            'kots' => $kotList,
            'inKitchenCount' => count($inKitchen),
            'foodReadyCount' => count($foodReady),
            'pendingConfirmationCount' => count($pendingConfirmation),
            'cancelledCount' => count($cancelled),
            'kotSettings' => $kotSettings,
            'cancelReasons' => $cancelReasons,
            'kitchens' => $kitchens,
            'showAllKitchens' => $this->showAllKitchens,
        ]);
    }

    /**
     * Handle order item cancellation when a KOT item is cancelled
     */
    private function handleOrderItemCancellation($kotItem, $order)
    {
        // Find corresponding order item by matching menu item, variation, quantity, and modifiers
        $orderItemQuery = OrderItem::where('order_id', $order->id)
            ->where('menu_item_id', $kotItem->menu_item_id)
            ->where('menu_item_variation_id', $kotItem->menu_item_variation_id)
            ->where('quantity', $kotItem->quantity);

        // If there's a linked order item, use it
        if ($kotItem->order_item_id) {
            $orderItem = OrderItem::find($kotItem->order_item_id);
        } else {
            // Find by matching criteria
            $orderItem = $orderItemQuery->first();
        }

        if ($orderItem) {
            // Mark the order item as cancelled or delete it
            // For now, we'll delete it to match the KOT item behavior
            $orderItem->delete();
        }
    }

    /**
     * Recalculate order totals based on remaining active KOT items
     */
    private function recalculateOrderTotals($order)
    {
        $subTotal = 0;
        $total = 0;
        $totalTaxAmount = 0;

        // Calculate totals from remaining active KOT items
        foreach ($order->kot as $kot) {
            foreach ($kot->items->where('status', '!=', 'cancelled') as $item) {
                $menuItemPrice = $item->menuItem->price ?? 0;
                $variationPrice = $item->menuItemVariation ? $item->menuItemVariation->price : 0;
                $basePrice = $variationPrice ?: $menuItemPrice;

                // Add modifier prices
                $modifierPrice = $item->modifierOptions->sum('price');
                $itemTotal = ($basePrice + $modifierPrice) * $item->quantity;

                $subTotal += $itemTotal;
                $total += $itemTotal;
            }
        }

        // Apply discount if exists
        $discountAmount = 0;
        if ($order->discount_type === 'percent') {
            $discountAmount = round(($subTotal * $order->discount_value) / 100, 2);
        } elseif ($order->discount_type === 'fixed') {
            $discountAmount = min($order->discount_value, $subTotal);
        }

        $discountedTotal = $total - $discountAmount;

        // Add extra charges
        foreach ($order->extraCharges ?? [] as $charge) {
            if (method_exists($charge, 'getAmount')) {
                $total += $charge->getAmount($discountedTotal);
            }
        }

        // Add tip and delivery fee
        if ($order->tip_amount > 0) {
            $total += $order->tip_amount;
        }

        if ($order->delivery_fee > 0) {
            $total += $order->delivery_fee;
        }

        // Apply discount
        $total -= $discountAmount;

        // Calculate taxes if needed
        if ($order->tax_mode === 'item') {
            // For item-level tax, we need to recalculate from remaining order items
            $remainingOrderItems = OrderItem::where('order_id', $order->id)->get();
            $totalTaxAmount = $remainingOrderItems->sum('tax_amount');

            if (restaurant()->tax_inclusive) {
                $subTotal -= $totalTaxAmount;
            } else {
                $total += $totalTaxAmount;
            }
        } elseif ($order->tax_mode === 'order') {
            // For order-level tax, calculate from order taxes
            $orderTaxes = $order->taxes;
            foreach ($orderTaxes as $tax) {
                $taxAmount = $tax->calculateTax($discountedTotal);
                $totalTaxAmount += $taxAmount;
            }
            $total += $totalTaxAmount;
        }

        // Update the order
        $order->update([
            'sub_total' => $subTotal,
            'total' => $total,
            'discount_amount' => $discountAmount,
            'total_tax_amount' => $totalTaxAmount,
        ]);
    }
}
