<?php

namespace Modules\Kiosk\Livewire\Kiosk;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\PaymentGatewayCredential;
use App\Models\Tax;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\ItemCategory;
use App\Models\Menu as MenuModel;
use Modules\Kiosk\Entities\KioskAd;
use Modules\Kiosk\Services\KioskCartService;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Menu extends Component
{
    use LivewireAlert;
    
    public $restaurant;
    public $shopBranch;
    public $filterCategories;
    public $menuId;
    public $showVeg;
    public $showHalal;
    public $search;
    public $selectedCategory = null;
    public $taxMode;
    public $kioskAds;
    public function mount($restaurant, $shopBranch)
    {
        $this->restaurant = $restaurant;
        $this->shopBranch = $shopBranch;
        $this->selectedCategory = $this->shopBranch->itemCategories->first()->id ?? null;
        $this->taxMode = $this->restaurant->tax_mode ?? 'order';
        $this->kioskAds = KioskAd::all();
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
    }

    public function showItem($itemId)
    {
        $this->dispatch('selectItem', itemId: $itemId);
        $this->dispatch('showItemCustomisation');
    }

    public function addToCart($itemId)
    {
        try {
            $menuItem = MenuItem::findOrFail($itemId);
            
            // Check if item has variations or modifiers that require customization
            if ($menuItem->variations_count > 0 || $menuItem->modifier_groups_count > 0) {
                // Show customization modal
                $this->showItem($itemId);
                return;
            }
            
            // Add directly to cart
            $cartService = new KioskCartService();
            $result = $cartService->addKioskItem(
                branchId: $this->shopBranch->id,
                menuItemId: $itemId,
                quantity: 1
            );
            
            if ($result['success']) {
                $this->alert('success', __('kiosk::modules.alerts.add_to_cart_success'), [
                    'toast' => true,
                    'position' => 'top-end',
                    'timer' => 2000,
                ]);
                
                // Dispatch event to update cart count
                $this->dispatch('cartUpdated', [
                    'count' => $result['cart_count'],
                    'total' => $result['cart_total']
                ]);
            } else {
                $this->alert('error', __('kiosk::modules.alerts.add_to_cart_failed'));
            }
            
        } catch (\Exception $e) {
            $this->alert('error', __('kiosk::modules.alerts.add_to_cart_error', ['message' => $e->getMessage()]));
        }
    }

    #[On('cartUpdated')]
    public function cartUpdated($cartCount)
    {
        $cartService = new KioskCartService();
        $cartCount = $cartService->getKioskCartBadgeCount($this->shopBranch->id);
        $this->dispatch('showCart', $cartCount);
        $this->cartCount = $cartCount;
    }

    public function removeFromCart($itemId)
    {
        $cartService = new KioskCartService();
        $cartService->removeKioskItem($this->shopBranch->id, $itemId);
        $this->dispatch('cartUpdated');
    }
    
    public function updateQuantity($itemId, $quantity)
    {
        $cartService = new KioskCartService();
        $result = $cartService->updateKioskItemQuantity($itemId, $quantity);

        $this->cartItemList = $cartService->getKioskCartSummary($this->shopBranch->id);

    }

    public function render()
    {
        $locale = session('locale', app()->getLocale());

        $menuList = MenuModel::withoutGlobalScopes()->where('branch_id', $this->shopBranch->id)->withCount('items')->orderBy('sort_order')->get();

        $query = MenuItem::withCount('variations', 'modifierGroups')->with('category')
        ->select('menu_items.*', 'item_categories.category_name')
        ->join('item_categories', 'menu_items.item_category_id', '=', 'item_categories.id')
        ->where('menu_items.branch_id', $this->shopBranch->id)
        ->where('show_on_customer_site', true);

        if (!is_null($this->selectedCategory)) {
            $query = $query->where('menu_items.item_category_id', $this->selectedCategory);
        }

        if (!empty($this->menuId)) {
            $query = $query->where('menu_items.menu_id', $this->menuId);
        }

        if ($this->showVeg == 1) {
            $query = $query->where('menu_items.type', 'veg');
        }

        if ($this->showHalal == 1) {
            $query = $query->where('menu_items.type', 'halal');
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('item_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('translations', function ($q) {
                        $q->where('item_name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $query = $query->orderBy('item_categories.sort_order')
            ->withCount('variations')
            ->withCount('modifierGroups')
            ->orderBy('sort_order')
            ->get();
            // ->groupBy(function ($item) use ($locale) {
            //     return $item->category->getTranslation('category_name', $locale);
            // });


        $categoryList = ItemCategory::withoutGlobalScopes()->whereHas('items')->with(['items' => function ($q) {
            if (!empty($this->menuId)) {
                $q->where('menu_items.menu_id', $this->menuId);
            }

            if ($this->showVeg == 1) {
                $q->where('menu_items.type', 'veg');
            }

            if ($this->showHalal == 1) {
                $q->where('menu_items.type', 'halal');
            }

            return $q->where('menu_items.is_available', 1);
        }])->where('branch_id', $this->shopBranch->id)->orderBy('sort_order')->get();

        $kioskService = new KioskCartService();
        $cartCount = $kioskService->getKioskCartBadgeCount($this->shopBranch->id);

        $cartItemList = $kioskService->getKioskCartSummary($this->shopBranch->id);

        $subtotal = $cartItemList['sub_total'];
        $total = $cartItemList['total'];
        $totalTaxAmount = $cartItemList['total_tax_amount'];
        $taxBreakdown = $cartItemList['tax_breakdown'];
        $taxMode = $cartItemList['tax_mode'];

        return view('kiosk::livewire.kiosk.menu', [
            'menuItems' => $query,
            'categoryList' => $categoryList,
            'menuList' => $menuList,
            'cartCount' => $cartCount,
            'cartItemList' => $cartItemList,
            'subtotal' => $subtotal,
            'total' => $total,
            'totalTaxAmount' => $totalTaxAmount,
            'taxBreakdown' => $taxBreakdown,
            'taxMode' => $taxMode,
            'kioskAds' => $this->kioskAds,
        ]);
    }
}
