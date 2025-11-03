<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\OrderType;
use App\Models\DeliveryPlatform;

class OrderTypeSelection extends Component
{
    public $showOrderTypeModal = true;
    public $orderTypes = [];
    public $deliveryPlatforms = [];
    public $selectedOrderTypeChoice = null;
    public $selectedOrderTypeSlug = null;
    public $selectedDeliveryPlatform = null;
    public $defaultDeliveryPlatform = null;
    
    // Keep track of selection stages
    public $selectionStage = 'order_type'; // order_type, delivery_platform
    
    public function mount()
    {
        $this->loadOrderTypes();
        $this->loadDeliveryPlatforms();
    }

    public function loadOrderTypes()
    {
        $this->orderTypes = OrderType::where('is_active', true)
            ->orderBy('order_type_name')
            ->get();
    }

    public function loadDeliveryPlatforms()
    {
        $this->deliveryPlatforms = DeliveryPlatform::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function selectOrderType($orderTypeId, $slug)
    {
        $this->selectedOrderTypeChoice = $orderTypeId;
        $this->selectedOrderTypeSlug = $slug;
        
        // If it's delivery, move to delivery platform selection stage
        if ($slug === 'delivery') {
            $this->selectionStage = 'delivery_platform';
        } else {
            $this->proceedToPOS();
        }
    }

    public function selectDeliveryPlatformAndProceed($platform)
    {
        $this->selectedDeliveryPlatform = $platform;
        $this->proceedToPOS();
    }

    public function goBackToOrderTypes()
    {
        $this->selectionStage = 'order_type';
        $this->selectedDeliveryPlatform = null;
    }

    public function resetSelection()
    {
        $this->selectedOrderTypeChoice = null;
        $this->selectedOrderTypeSlug = null;
        $this->selectedDeliveryPlatform = null;
        $this->selectionStage = 'order_type';
    }

    public function proceedToPOS()
    {
        if (!$this->selectedOrderTypeChoice) {
            return;
        }

        $params = [
            'orderType' => $this->selectedOrderTypeSlug,
            'orderTypeId' => $this->selectedOrderTypeChoice,
        ];

        if ($this->selectedDeliveryPlatform) {
            $params['deliveryPlatform'] = $this->selectedDeliveryPlatform;
        }

        // Dispatch event to the parent component
        $this->dispatch('setOrderTypeChoice', $params);
    }

    public function render()
    {
        return view('livewire.forms.order-type-selection');
    }
}
