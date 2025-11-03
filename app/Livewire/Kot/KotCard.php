<?php

namespace App\Livewire\Kot;

use App\Models\Kot;
use App\Models\KotItem;
use App\Models\Printer;
use Livewire\Component;
use App\Models\KotPlace;
use App\Traits\PrinterSetting;
use App\Models\KotCancelReason;
use App\Events\KotUpdated;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class KotCard extends Component
{
    use LivewireAlert;
    public $kot;
    public $confirmDeleteKotModal = false;
    public $kotSettings;
    public $cancelReasons;
    public $cancelReason;
    public $cancelReasonText;
    public $kotPlace;
    public $showAllKitchens = false;

    // Status modal properties
    public $showStatusModal = false;
    public $selectedItemId = null;
    public $selectedItemStatus = null;

    use PrinterSetting;

    public function mount($kot, $kotSettings, $showAllKitchens = false)
    {
        $this->kot = $kot;
        $this->kotSettings = $kotSettings;
        $this->showAllKitchens = $showAllKitchens;
        $this->cancelReasons = KotCancelReason::where('cancel_kot', true)->get();
    }


    public function changeKotStatus($status)
    {
        $kot = Kot::find($this->kot->id);
        $kot->status = $status;
        $kot->save();

        if ($status == 'food_ready') {
            // Only update non-cancelled items
            KotItem::where('kot_id', $this->kot->id)
                ->where('status', '!=', 'cancelled')
                ->update([
                    'status' => 'ready'
                ]);
        }

        if ($status == 'in_kitchen') {
            // Only update non-cancelled items
            KotItem::where('kot_id', $this->kot->id)
                ->where('status', '!=', 'cancelled')
                ->update([
                    'status' => 'cooking'
                ]);
        }

        // Refresh the KOT data
        $this->kot = $kot->fresh(['items']);

        $this->dispatch('refreshKots');
    }

    public function changeKotItemStatus($itemId, $status)
    {
        $kotItem = KotItem::find($itemId);
        $kotItem->status = $status;
        $kotItem->save();

        // Auto-update KOT status based on item statuses
        $this->updateKotStatusBasedOnItems();

        $this->dispatch('refreshKots');
    }

    private function updateKotStatusBasedOnItems()
    {
        $kot = Kot::find($this->kot->id);
        $nonCancelledItems = $kot->items->where('status', '!=', 'cancelled');

        if ($nonCancelledItems->isEmpty()) {
            return; // No items to check
        }

        $totalItems = $nonCancelledItems->count();
        $pendingItems = $nonCancelledItems->where('status', 'pending')->count();
        $cookingItems = $nonCancelledItems->where('status', 'cooking')->count();
        $readyItems = $nonCancelledItems->where('status', 'ready')->count();

        $statusChanged = false;

        // Only update KOT status when ALL items have the same status
        // If all items are pending, keep KOT as pending_confirmation
        if ($pendingItems === $totalItems && $kot->status !== 'pending_confirmation') {
            $kot->status = 'pending_confirmation';
            $kot->save();
            $statusChanged = true;
        }
        // If all items are cooking, move KOT to in_kitchen
        elseif ($cookingItems === $totalItems && $kot->status !== 'in_kitchen') {
            $kot->status = 'in_kitchen';
            $kot->save();
            $statusChanged = true;
        }
        // If all items are ready, move KOT to food_ready
        elseif ($readyItems === $totalItems && $kot->status !== 'food_ready') {
            $kot->status = 'food_ready';
            $kot->save();
            $statusChanged = true;
        }

        // Refresh the KOT data if status changed
        if ($statusChanged) {
            $this->kot = $kot->fresh(['items']);
        }
    }

    public function openStatusModal($itemId, $currentStatus)
    {
        $this->selectedItemId = $itemId;
        $this->selectedItemStatus = $currentStatus;
        $this->showStatusModal = true;
    }

    public function closeStatusModal()
    {
        $this->showStatusModal = false;
        $this->selectedItemId = null;
        $this->selectedItemStatus = null;
    }

    public function updateItemStatus($status)
    {
        if ($this->selectedItemId) {
            $this->changeKotItemStatus($this->selectedItemId, $status);
            $this->closeStatusModal();
        }
    }

    public function reduceKotItemQuantity($itemId)
    {
        $kotItem = KotItem::find($itemId);

        if ($kotItem && $kotItem->quantity > 1) {
            $kotItem->quantity = $kotItem->quantity - 1;
            $kotItem->save();

            $this->dispatch('refreshKots');
        }
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

        $kot = Kot::findOrFail($id);
        $order = $kot->order;
        $kotCounts = $order->kot->count();

        // Update cancel reason info
        $kot->cancel_reason_id = $this->cancelReason;
        $kot->cancel_reason_text = $this->cancelReasonText;
        $kot->status = 'cancelled';
        $kot->save();

        // If this is the only KOT in the order, cancel the order
        if ($kotCounts === 1) {
            $order->status = 'canceled';
            $order->save();

            if ($order->table) {
                $order->table->update(['available_status' => 'available']);
            }
        }

        // Optional: soft delete kot or destroy it
        // Kot::destroy($id); // if using force delete

        $this->confirmDeleteKotModal = false;

        $this->dispatch('refreshKots');
    }


    public function printKot($kot)
    {
        // First save the image, then print
        $this->saveKotImageAndPrint($kot);
    }

    public function saveKotImageAndPrint($kot)
    {
        // First, trigger the image saving process
        $this->dispatch('saveKotImage', kotId: $kot);

        // Then proceed with the original print logic
        $this->executePrintKot($kot);
    }

    public function executePrintKot($kot)
    {
        if (in_array('Kitchen', restaurant_modules()) && in_array('kitchen', custom_module_plugins())) {

            $kot = Kot::with(['items.menuItem.kotPlace'])->find($kot);
            $kotPlaceItems = [];

            foreach ($kot->items as $kotItem) {
                if ($kotItem->menuItem && $kotItem->menuItem->kot_place_id) {
                    $kotPlaceId = $kotItem->menuItem->kot_place_id;

                    if (!isset($kotPlaceItems[$kotPlaceId])) {
                        $kotPlaceItems[$kotPlaceId] = [];
                    }

                    $kotPlaceItems[$kotPlaceId][] = $kotItem;
                }
            }

            $kotPlaceIds = array_keys($kotPlaceItems);

            $kotPlaces = KotPlace::with('printerSetting')->whereIn('id', $kotPlaceIds)->get();


            foreach ($kotPlaces as $kotPlace) {
                $printerSetting = $kotPlace->printerSetting;

                if (!$printerSetting) {
                    $printerSetting = Printer::where('is_default', true)->first();
                }

                if ($printerSetting->is_active == 0) {
                    $printerSetting = Printer::where('is_default', true)->first();
                }
                try {
                    switch ($printerSetting->printing_choice) {
                        case 'directPrint':
                            $this->handleKotPrint($kot->id, $kotPlace->id);
                            break;
                        default:

                            $url = route('kot.print', [$kot->id, $kotPlace?->id]);
                            $this->dispatch('print_location', $url);
                            break;
                    }
                } catch (\Throwable $e) {
                    $this->alert('error', __('messages.printerNotConnected') . ' executePrintKot error: ' . $e->getMessage(), [
                        'toast' => true,
                        'position' => 'top-end',
                        'showCancelButton' => false,
                        'cancelButtonText' => __('app.close')
                    ]);
                }
            }
        } else {
            $kot = Kot::with(['items.menuItem.kotPlace'])->find($kot);
            $kotPlace = KotPlace::where('is_default', 1)->first();
            $printerSetting = $kotPlace->printerSetting;
            // If no printer is set, fallback to print URL dispatch
            if (!$printerSetting) {
                $url = route('kot.print', [$kot->id, $kotPlace?->id]);
                $this->dispatch('print_location', $url);
            }


            try {
                switch ($printerSetting->printing_choice) {
                    case 'directPrint':
                        $this->handleKotPrint($kot->id, $kotPlace->id);
                        break;
                    default:
                        $url = route('kot.print', [$kot]);
                        $this->dispatch('print_location', $url);
                        break;
                }
            } catch (\Throwable $e) {
                $this->alert('error', __('messages.printerNotConnected') . ' executePrintKot error else: ' . $e->getMessage(), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
                ]);
            }
        }
    }

    public function render()
    {

        return view('livewire.kot.kot-card');
    }
}
