<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\OrderType;
use App\Models\RestaurantCharge;

class AddCharges extends Component
{
    public $chargeId;
    public $chargeName;
    public $chargeType = 'percent';
    public $chargeValue;
    public $selectedOrderTypes = [];
    public $selectedChargeId;
    public $orderTypes = [];
    public bool $isEnabled = false;

    public function mount()
    {
        // Get order types as [slug => order_type_name]
        $this->orderTypes = OrderType::pluck('order_type_name', 'slug')->toArray();

        if ($this->selectedChargeId) {
            $charge = RestaurantCharge::find($this->selectedChargeId);
            $this->chargeName = $charge->charge_name;
            $this->chargeType = $charge->charge_type;
            $this->chargeValue = $charge->charge_value;
            $this->selectedOrderTypes = array_unique($charge->order_types ?? []); // already slugs if migrated
            $this->isEnabled = $charge->is_enabled;
        }
    }

    public function toggleSelectOrderType($orderType)
    {
        $this->selectedOrderTypes = in_array($orderType, $this->selectedOrderTypes) ? array_values(array_diff($this->selectedOrderTypes, [$orderType])) : array_unique([...$this->selectedOrderTypes, $orderType]);
    }

    public function submitForm()
    {
        $this->validate([
            'chargeName' => 'required|string|max:255',
            'chargeType' => 'required|in:percent,fixed',
            'chargeValue' => 'required|numeric|min:0',
            'isEnabled' => 'boolean',
        ]);

        // Ensure only valid slugs are saved
        $validSlugs = array_keys($this->orderTypes);
        $selectedSlugs = array_values(array_unique(array_intersect($this->selectedOrderTypes, $validSlugs)));

        $charge = RestaurantCharge::updateOrCreate(
            ['id' => $this->selectedChargeId],
            [
                'charge_name' => $this->chargeName,
                'charge_type' => $this->chargeType,
                'charge_value' => $this->chargeValue,
                'is_enabled' => $this->isEnabled,
                'order_types' => $selectedSlugs,
            ]
        );

        $this->dispatch('hideShowChargesForm');
    }

    public function render()
    {
        return view('livewire.forms.add-charges');
    }
    
}
