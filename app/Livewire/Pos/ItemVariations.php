<?php

namespace App\Livewire\Pos;

use Livewire\Component;

class ItemVariations extends Component
{

    public $menuItem;
    public $itemVariation;
    public $variationName;
    public $variationPrice;
    public $showEditVariationsModal = false;
    public $showDeleteVariationsModal = false;
    public $orderTypeId;
    public $deliveryAppId;

    public function mount($menuItem, $orderTypeId = null, $deliveryAppId = null)
    {
        $this->menuItem = $menuItem->load('variations');
        $this->orderTypeId = $orderTypeId;
        $this->deliveryAppId = $deliveryAppId;
        
        // Set price context on the menu item
        if ($this->orderTypeId) {
            $this->menuItem->setPriceContext($this->orderTypeId, $this->deliveryAppId);
            
            // Set price context on all variations so they can use ->price directly
            foreach ($this->menuItem->variations as $variation) {
                $variation->setPriceContext($this->orderTypeId, $this->deliveryAppId);
            }
        }
    }

    public function setItemVariation($id)
    {
        $this->dispatch('setPosVariation', variationId: $id);
    }

    public function render()
    {
        return view('livewire.pos.item-variations');
    }

}
