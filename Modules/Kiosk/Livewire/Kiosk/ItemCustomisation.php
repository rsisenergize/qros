<?php

namespace Modules\Kiosk\Livewire\Kiosk;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\MenuItem;
use App\Models\ModifierOption;
use Modules\Kiosk\Services\KioskCartService;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ItemCustomisation extends Component
{
    use LivewireAlert;
    
    public $restaurant;
    public $shopBranch;
    public $itemId;
    public $item;
    public $selectedModifiers = [];
    public $finalModifiers = [];
    public $modifiers = [];
    public $requiredModifiers = [];
    public $totalPrice = 0;
    public $quantity = 1;
    public $selectedVariant;
    
    public function mount($restaurant, $shopBranch)
    {
        $this->restaurant = $restaurant;
        $this->shopBranch = $shopBranch;        
        $this->item = new MenuItem();
    }

    public function selectVariant($variationId)
    {
        $this->selectedVariant = $variationId;
        $this->calculateTotalPrice();
    }

    public function toggleSelection($groupId, $optionId)
    {
        $modifierGroup = $this->item->modifierGroups()
            ->withPivot(['is_required', 'allow_multiple_selection'])
            ->firstWhere('modifier_groups.id', $groupId);

        $allowMultiple = $modifierGroup->pivot->allow_multiple_selection;

        if ($allowMultiple) {
            if (in_array($optionId, $this->selectedModifiers)) {
                if ($optionId !== 1) {
                    $this->selectedModifiers = array_diff($this->selectedModifiers, [$optionId]);
                }
            }
        } else {
            // Store whether current option was checked before clearing
            $wasChecked = isset($this->selectedModifiers[$optionId]) && $this->selectedModifiers[$optionId];
            
            // First remove all options from this modifier group
            foreach ($modifierGroup->options as $option) {
                unset($this->selectedModifiers[$option->id]);
            }
            
            // Then add back the current option if it was checked
            if ($wasChecked) {
                $this->selectedModifiers[$optionId] = true;
            }
        }

        $this->calculateTotalPrice();

        // dd($this->selectedModifiers, $allowMultiple, $this->item->modifierGroups);
    }

    public function calculateTotalPrice()
    {
        $this->totalPrice = $this->selectedVariant ? $this->item->variations()->where('id', $this->selectedVariant)->first()->price : $this->item->price;

        $modifierIds = array_keys(array_filter($this->selectedModifiers));

        $modifierOptions = ModifierOption::whereIn('id', $modifierIds)->get();
        
        foreach ($modifierOptions as $modifierOption) {
            $this->totalPrice += $modifierOption->price;
        }

        $this->totalPrice = $this->totalPrice * $this->quantity;
    }

    public function increaseQuantity()
    {
        $this->quantity++;
        $this->calculateTotalPrice();
    }

    public function decreaseQuantity()
    {
        $this->quantity--;
        $this->calculateTotalPrice();
    }

    public function resetCount()
    {
        $this->quantity = 1;
        $this->selectedModifiers = [];
        $this->selectedVariant = null;
        $this->calculateTotalPrice();
    }

    #[On('selectItem')]
    public function showItem($itemId)
    {
        $this->itemId = $itemId;
        $this->item = MenuItem::find($itemId);
        $this->totalPrice = $this->item->price;
        $this->resetCount();
    }

    public function addToCartFromCustomization()
    {
        try {
            $cartService = new KioskCartService();
            
            // Get selected modifier option IDs
            $modifierOptionIds = array_keys(array_filter($this->selectedModifiers));
            
            // Add item to cart
            $result = $cartService->addKioskItem(
                branchId: $this->shopBranch->id,
                menuItemId: $this->item->id,
                quantity: $this->quantity,
                variationId: $this->selectedVariant,
                modifierOptionIds: $modifierOptionIds,
                
            );
            
            if ($result['success']) {
                $this->alert('success', 'Item added to cart successfully!', [
                    'toast' => true,
                    'position' => 'top-end',
                    'timer' => 3000,
                ]);
                
                // Reset the form
                $this->resetCount();
                
                // Dispatch event to update cart count in other components
                $this->dispatch('cartUpdated', [
                    'count' => $result['cart_count'],
                    'total' => $result['cart_total']
                ]);
                
                // Navigate back to menu or show success message
                $this->dispatch('showMenu');
            } else {
                $this->alert('error', 'Failed to add item to cart. Please try again.');
            }
            
        } catch (\Exception $e) {
            $this->alert('error', 'An error occurred while adding item to cart: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('kiosk::livewire.kiosk.item-customisation');
    }
}
