<?php

namespace App\Livewire\Settings;

use App\Models\Menu;
use App\Models\Branch;
use Livewire\Component;
use App\Models\MenuItem;
use Livewire\Attributes\On;
use App\Models\ItemCategory;
use App\Models\KotPlace;
use App\Models\ModifierGroup;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class BranchSettings extends Component
{
    use LivewireAlert;

    // Form fields
    public $branchName;
    public $branchAddress;
    public $branchLat = '26.9125';
    public $branchLng = '75.7875';
    public $isEditing = false;
    public $confirmDeleteBranchModal = false;
    public $activeBranch = null;
    public $activeBranchId = null;
    public $formMode = 'add';
    public $cloneData;
    public $cloneMenu = false;
    public $clonecategories = false;
    public $cloneMenuItems = false;
    public $cloneItemModifires = false;
    public $cloneModifiersGroups = false;
    public $cloneReservationSettings = false;
    public $cloneDeliverySettings = false;
    public $cloneKotSettings = false;
    public $menus;
    public $menu;

    public function mount()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->branchName = '';
        $this->branchAddress = '';
        $this->branchLat = '26.9125';
        $this->branchLng = '75.7875';
        $this->activeBranchId = null;
        $this->formMode = 'add';
        $this->isEditing = false;
        $this->cloneData = null;
        $this->cloneMenu = false;
        $this->clonecategories = false;
        $this->cloneMenuItems = false;
        $this->cloneItemModifires = false;
        $this->cloneModifiersGroups = false;
        $this->cloneReservationSettings = false;
        $this->cloneDeliverySettings = false;
        $this->cloneKotSettings = false;
    }

    private function checkBranchLimit(): bool
    {
        if (!in_array('Change Branch', restaurant_modules(), true)) {
            abort(403, __('messages.invalidRequest'));
        }

        $restaurant = restaurant();
        $branchLimit = $restaurant->package->branch_limit;

        if (is_null($branchLimit) || $branchLimit === -1) {
            return true;
        }

        if ($branchLimit === 0 || $restaurant->branches()->count() >= $branchLimit) {
            $this->alert('error', __('messages.branchLimitReached'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
            ]);
            return false;
        }

        return true;
    }

    public function createMode()
    {
        if (!$this->checkBranchLimit()) {
            return;
        }

        $this->dispatch('initAddressMap');

        $this->resetForm();
        $this->formMode = 'add';
        $this->isEditing = true;
    }

    public function showEditBranch($id)
    {
        $this->showEditBranchModal = true;
        $this->editMode($id);
    }

    public function editMode($id)
    {
        $branch = Branch::findOrFail($id);
        $this->activeBranchId = $branch->id;
        $this->activeBranch = $branch;
        $this->branchName = $branch->name;
        $this->branchAddress = $branch->address;
        $this->branchLat = $branch->lat ?? '26.9125';
        $this->branchLng = $branch->lng ?? '75.7875';
        $this->formMode = 'edit';
        $this->isEditing = true;
        $this->dispatch('initAddressMap');
    }

    public function cancelEdit()
    {
        $this->resetForm();
    }

    public function showDeleteBranch($id)
    {
        $this->activeBranch = Branch::findOrFail($id);
        $this->activeBranchId = $id;
        $this->confirmDeleteBranchModal = true;
    }

    public function deleteBranch()
    {
        Branch::destroy($this->activeBranchId);
        $this->activeBranch = null;
        $this->activeBranchId = null;
        $this->confirmDeleteBranchModal = false;

        session(['branches' => Branch::get()]);

        $this->alert('success', __('messages.branchDeleted'), [
        'toast' => true,
        'position' => 'top-end',
        'showCancelButton' => false,
        'cancelButtonText' => __('app.close')
        ]);
    }

    #[On('updateLivewireMapProperties')]
    public function updateLivewireMapProperties($lat, $lng)
    {
        $this->branchLat = $lat;
        $this->branchLng = $lng;
    }

    public function saveBranch()
    {
        if ($this->formMode === 'add' && !$this->checkBranchLimit())
        {
            $this->alert('error', __('messages.invalidRequest'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
            ]);
            return;
        }

        if ($this->formMode === 'add')
        {
            $rules = [
            'branchName'    => 'required|unique:branches,name,null,id,restaurant_id,' . restaurant()->id,
            'branchAddress' => 'required',
            'branchLat'     => 'required|numeric|between:-90,90',
            'branchLng'     => 'required|numeric|between:-180,180',
            ];

            if ($this->cloneMenuItems) {
                $rules['clonecategories'] = 'accepted';
                $rules['cloneMenu'] = 'accepted';
            }

            if ($this->cloneItemModifires) {
                $rules['cloneMenuItems'] = 'accepted';
            }

            $this->validate($rules, [
            'clonecategories.accepted' => __('messages.cloneCategoriesRequired'),
            'cloneMenu.accepted' => __('messages.cloneMenuRequired'),
            'cloneMenuItems.accepted' => __('messages.cloneMenuItemRequired'),
            ]);

            $newBranch = Branch::create([
                'name'          => $this->branchName,
                'restaurant_id' => restaurant()->id,
                'address'       => $this->branchAddress,
                'lat'           => $this->branchLat,
                'lng'           => $this->branchLng,
                'cloned_branch_name' => $this->cloneData ? Branch::find($this->cloneData)->name : null,
                'cloned_branch_id' => $this->cloneData,
                'is_menu_clone' => $this->cloneMenu,
                'is_item_categories_clone' => $this->clonecategories,
                'is_menu_items_clone' => $this->cloneMenuItems,
                'is_item_modifiers_clone' => $this->cloneItemModifires,
                'is_modifiers_groups_clone' => $this->cloneModifiersGroups,
                'is_clone_reservation_settings' => $this->cloneReservationSettings,
                'is_clone_delivery_settings' => $this->cloneDeliverySettings,
                'is_clone_kot_setting' => $this->cloneKotSettings,
            ]);

            if ($this->cloneData) {
                $this->cloneBranchData($this->cloneData, $newBranch);
            }

            $this->alert('success', __('messages.branchAdded'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
            ]);
        }
        else
        {
            $rules = [
            'branchName'    => 'required|unique:branches,name,' . $this->activeBranchId . ',id,restaurant_id,' . restaurant()->id,
            'branchAddress' => 'required',
            'branchLat'     => 'required|numeric|between:-90,90',
            'branchLng'     => 'required|numeric|between:-180,180',
            ];

            if ($this->cloneMenuItems) {
                $rules['clonecategories'] = 'accepted';
                $rules['cloneMenu'] = 'accepted';
            }

            if ($this->cloneItemModifires) {
                $rules['cloneMenuItems'] = 'accepted';
            }

            $this->validate($rules, [
            'clonecategories.accepted' => __('messages.cloneCategoriesRequired'),
            'cloneMenu.accepted' => __('messages.cloneMenuRequired'),
            'cloneMenuItems.accepted' => __('messages.cloneMenuItemRequired'),
            ]);

            Branch::where('id', $this->activeBranchId)->update([
                'name'          => $this->branchName,
                'restaurant_id' => restaurant()->id,
                'address'       => $this->branchAddress,
                'lat'           => $this->branchLat,
                'lng'           => $this->branchLng,
                'cloned_branch_name' => $this->cloneData ? Branch::find($this->cloneData)->name : null,
                'cloned_branch_id' => $this->cloneData,
                'is_menu_clone' => $this->cloneMenu,
                'is_item_categories_clone' => $this->clonecategories,
                'is_menu_items_clone' => $this->cloneMenuItems,
                'is_item_modifiers_clone' => $this->cloneItemModifires,
                'is_modifiers_groups_clone' => $this->cloneModifiersGroups,
                'is_clone_reservation_settings' => $this->cloneReservationSettings,
                'is_clone_delivery_settings' => $this->cloneDeliverySettings,
                'is_clone_kot_setting' => $this->cloneKotSettings,
            ]);
            $this->activeBranch = Branch::find($this->activeBranchId);
            // dd($this->activeBranch);
            if ($this->cloneData) {
                $this->cloneBranchData($this->cloneData, $this->activeBranch);
            }

            session()->forget(['restaurant', 'branch']);

            $this->alert('success', __('messages.branchUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
            ]);
        }

        session(['branches' => Branch::get()]);
        $this->resetForm();
    }

    protected function cloneBranchData($sourceBranchId, Branch $newBranch)
    {
        if (!$sourceBranchId) {
            return;
        }

        // if ($this->cloneDeliverySettings && $sourceBranch->deliverySetting) {
        //     $clonedSetting = $sourceBranch->deliverySetting->replicate();
        //     $clonedSetting->branch_id = $newBranch->id;
        //     $clonedSetting->save();
        // }

        // Maps to maintain old ID => new ID
        $menuMap = [];
        $categoryMap = [];
        $itemMap = [];

        // Clone Menus
        $menus = Menu::withoutGlobalScopes()->where('branch_id', $sourceBranchId)->get();

        if ($this->cloneMenu && $menus->isNotEmpty()) {

            foreach ($menus as $menu) {
                Menu::withoutEvents(function () use ($menu, $newBranch, &$menuMap) {
                    $clone = $menu->replicate();
                    $clone->branch_id = $newBranch->id;
                    $clone->save();
                    $menuMap[$menu->id] = $clone->id;
                });
            }

        }

        // Clone Item Categories
        $categories = ItemCategory::withoutGlobalScopes()->where('branch_id', $sourceBranchId)->get();

        if ($this->clonecategories && $categories->isNotEmpty()) {
            foreach ($categories as $category) {
                ItemCategory::withoutEvents(function () use ($category, $newBranch, &$categoryMap) {
                    $clone = $category->replicate();
                    $clone->branch_id = $newBranch->id;
                    $clone->save();
                    $categoryMap[$category->id] = $clone->id;
                });
            }
        }

        // Clone Menu Items (once only)
        $menuItems = MenuItem::with('modifiers', 'taxes')->withoutGlobalScopes()->where('branch_id', $sourceBranchId)->get();

        if ($this->cloneMenuItems && $menuItems->isNotEmpty()) {

            foreach ($menuItems as $item) {
                MenuItem::withoutEvents(function () use ($item, $newBranch, $menuMap, $categoryMap, &$itemMap) {
                    $clone = $item->replicate();
                    $clone->branch_id = $newBranch->id;
                    // Update kot_place_id according to new branch id

                    $kotPlace = KotPlace::withoutGlobalScopes()
                        ->where('branch_id', $newBranch->id)
                        ->first();
                    $clone->kot_place_id = $kotPlace->id ?? null;

                    $clone->menu_id = $menuMap[$item->menu_id] ?? null;
                    $clone->item_category_id = $categoryMap[$item->item_category_id] ?? null;
                    $clone->save();

                    $itemMap[$item->id] = $clone->id;

                    // Clone taxes associated with the menu item (only when tax_mode is 'item')
                    if ($item->taxes && $item->taxes->isNotEmpty()) {
                        $taxIds = $item->taxes->pluck('id')->toArray();
                        $clone->taxes()->sync($taxIds);
                    }

                    return $clone;
                });
            }
        }

        // Clone Item Modifiers (after items are cloned and mapped)
        if ($this->cloneItemModifires && $menuItems->isNotEmpty()) {
            foreach ($menuItems as $item) {
                $clonedItemId = $itemMap[$item->id] ?? null;

                if ($clonedItemId && $item->modifiers) {
                    foreach ($item->modifiers as $modifier) {
                        $clonedModifier = $modifier->replicate();
                        $clonedModifier->menu_item_id = $clonedItemId;
                        $clonedModifier->save();
                    }
                }
            }
        }

        // Clone Modifier Groups
            $modifierGroups = ModifierGroup::withoutGlobalScopes()->where('branch_id', $sourceBranchId)->get();

        if ($this->cloneModifiersGroups && $modifierGroups->isNotEmpty()) {
            foreach ($modifierGroups as $group) {
                $clonedGroup = $group->replicate();
                $clonedGroup->branch_id = $newBranch->id;
                $clonedGroup->save();
            }
        }
    }

    public function handleCloneMenuItemsChange()
    {
        // If menu items are selected, ensure menu is also selected
        if ($this->cloneMenuItems && !$this->cloneMenu && !$this->clonecategories) {
            $this->cloneMenu = true;
            $this->clonecategories = true;
        }
    }

    public function handleCloneItemModifiersChange()
    {
        // If item modifiers are selected, ensure menu items and menu are also selected
        if ($this->cloneItemModifires) {

            if (!$this->cloneMenuItems && !$this->cloneMenu && !$this->clonecategories) {
                $this->cloneMenuItems = true;
                $this->cloneMenu = true;
                $this->clonecategories = true;
            }
        }
    }

    public function render()
    {
        $branches = Branch::where('restaurant_id', restaurant()->id)->get();
        $mapApiKey = global_setting()->google_map_api_key ?? null;

        return view('livewire.settings.branch-settings', [
            'branches' => $branches,
            'mapApiKey' => $mapApiKey
        ]);
    }

}
