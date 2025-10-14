@if(in_array('Inventory', restaurant_modules()))
<x-sidebar-dropdown-menu :name='__("inventory::modules.menu.inventory")' isAddon="true" icon='inventory' customIcon='<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="w-6 h-6 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" viewBox="0 0 16 16">
  <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5z"/>
  <path d="M4.5 6.5a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V7a.5.5 0 0 1 .5-.5zm2.5.5a.5.5 0 0 1 1 0v4a.5.5 0 0 1-1 0V7zm3 0a.5.5 0 0 1 1 0v4a.5.5 0 0 1-1 0V7z"/>
</svg>' :active='request()->routeIs(["inventory.*"]) || request()->routeIs(["units.*", "inventory-items.*", "inventory-stocks.*", "inventory-movements.*", "recipes.*", "purchase-orders.*", "inventory.reports.*", "inventory-settings.*", "suppliers.*", "inventory-item-categories.*"])'>
   
    @livewire('sidebar-dropdown-menu', ['name' => __('inventory::modules.menu.dashboard'), 'link' => route('inventory.dashboard'), 'active' => request()->routeIs('inventory.dashboard')])
    
    @if(user_can('Show Unit'))
    @livewire('sidebar-dropdown-menu', ['name' => __('inventory::modules.menu.units'), 'link' => route('units.index'), 'active' => request()->routeIs('units.index')])
    @endif
    
    @if(user_can('Show Inventory Item'))
      @livewire('sidebar-dropdown-menu', ['name' => __('inventory::modules.menu.inventoryItems'), 'link' => route('inventory-items.index'), 'active' => request()->routeIs('inventory-items.index')])
      @livewire('sidebar-dropdown-menu', ['name' => __('inventory::modules.menu.inventoryItemCategories'), 'link' => route('inventory-item-categories.index'), 'active' => request()->routeIs('inventory-item-categories.*')])
    @endif
    
    
    @if(user_can('Show Inventory Stock'))
    @livewire('sidebar-dropdown-menu', ['name' => __('inventory::modules.menu.inventoryStocks'), 'link' => route('inventory-stocks.index'), 'active' => request()->routeIs('inventory-stocks.index')])
    @endif
    
    @if(user_can('Show Inventory Movement'))
    @livewire('sidebar-dropdown-menu', ['name' => __('inventory::modules.menu.inventoryMovements'), 'link' => route('inventory-movements.index'), 'active' => request()->routeIs('inventory-movements.index')])
    @endif
    
    @if(user_can('Show Recipe'))
    @livewire('sidebar-dropdown-menu', ['name' => __('inventory::modules.menu.recipes'), 'link' => route('recipes.index'), 'active' => request()->routeIs('recipes.index')])
    @endif
    
    @if(user_can('Show Purchase Order'))
    @livewire('sidebar-dropdown-menu', ['name' => __('inventory::modules.menu.purchaseOrders'), 'link' => route('purchase-orders.index'), 'active' => request()->routeIs('purchase-orders.index')])
    @endif
    
    @if(user_can('Show Supplier'))
    @livewire('sidebar-dropdown-menu', ['name' => __('inventory::modules.menu.suppliers'), 'link' => route('suppliers.index'), 'active' => request()->routeIs('suppliers.*')])
    @endif

    @if(user_can('Show Inventory Report'))
    @livewire('sidebar-dropdown-menu', ['name' => __('inventory::modules.menu.reports'), 'link' => route('inventory.reports.usage'), 'active' => request()->routeIs('inventory.reports.*')])
    @endif

    @if(user_can('Update Inventory Settings'))
    @livewire('sidebar-dropdown-menu', ['name' => __('inventory::modules.menu.settings'), 'link' => route('inventory-settings.index'), 'active' => request()->routeIs('inventory-settings.index')])
    @endif
</x-sidebar-dropdown-menu>
@endif