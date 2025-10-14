<div>
    <aside id="sidebar"
        class="fixed top-0 ltr:left-0 rtl:right-0 z-20 flex flex-col flex-shrink-0 hidden w-64 h-full pt-16 font-normal duration-75 lg:flex transition-width"
        aria-label="Sidebar">
        <div
            class="relative flex flex-col flex-1 min-h-0 pt-0 bg-gray-50 border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto">
                <div
                    class="flex-1 px-3 space-y-1 bg-gray-50 divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">

                    <ul class="py-2 space-y-2">


                        @livewire('sidebar-menu-item', ['name' => __('menu.dashboard'), 'icon' => 'dashboard', 'link' => route('superadmin.dashboard'), 'active' => request()->routeIs('superadmin.dashboard')])

                        @livewire('sidebar-menu-item', ['name' => __('superadmin.menu.restaurants'), 'icon' => 'restaurants', 'link' => route('superadmin.restaurants.index'), 'active' => request()->routeIs('superadmin.restaurants.*')])

                        @livewire('sidebar-menu-item', ['name' => __('menu.payments'), 'icon' => 'payments', 'link' => route('superadmin.restaurant-payments.index'), 'active' => request()->routeIs('superadmin.restaurant-payments.index')])

                        @livewire('sidebar-menu-item', ['name' => __('menu.packages'), 'icon' => 'packages', 'link' => route('superadmin.packages.index'), 'active' => request()->routeIs('superadmin.packages.*')])

                        @livewire('sidebar-menu-item', ['name' => __('menu.billing'), 'icon' => 'billing', 'link' => route('superadmin.invoices.index'), 'active' => request()->routeIs('superadmin.invoices.*')])

                        @livewire('sidebar-menu-item', ['name' => __('menu.offlineRequest'), 'icon' => 'offline-plan-request', 'link' => route('superadmin.offline-plan-request'), 'active' => request()->routeIs('superadmin.offline-plan-request')])

                        @livewire('sidebar-menu-item', ['name' => __('superadmin.menu.superadmin'), 'icon' => 'staff', 'link' => route('superadmin.users.index'), 'active' => request()->routeIs('superadmin.users.*')])

                        @livewire('sidebar-menu-item', ['name' => __('menu.landingSites'), 'icon' => 'landing', 'link' => route('superadmin.landing-sites.index'), 'active' => request()->routeIs('superadmin.landing-sites.*')])

                        @foreach (custom_module_plugins() as $item)
                            @includeIf(strtolower($item) . '::sections.superadmin-sidebar')
                        @endforeach

                        @livewire('sidebar-menu-item', ['name' => __('menu.settings'), 'icon' => 'settings', 'link' => route('superadmin.superadmin-settings.index'), 'active' => request()->routeIs('superadmin.superadmin-settings.index')])

                    </ul>

                </div>
            </div>

            <div class="absolute bottom-0 left-0 justify-center hidden w-full p-4 space-x-4 bg-white lg:flex dark:bg-gray-800" sidebar-bottom-menu="">

                <a href="javascript:void(0)" wire:click="$dispatch('showRaiseSupportTicket')" class="inline-flex justify-center items-center p-2 rounded cursor-pointer hover:text-skin-base hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white border-skin-base border border-solid text-skin-base">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-ticket-perforated w-5 h-5" viewBox="0 0 16 16">
                        <path d="M4 4.85v.9h1v-.9zm7 0v.9h1v-.9zm-7 1.8v.9h1v-.9zm7 0v.9h1v-.9zm-7 1.8v.9h1v-.9zm7 0v.9h1v-.9zm-7 1.8v.9h1v-.9zm7 0v.9h1v-.9z"/>
                        <path d="M1.5 3A1.5 1.5 0 0 0 0 4.5V6a.5.5 0 0 0 .5.5 1.5 1.5 0 1 1 0 3 .5.5 0 0 0-.5.5v1.5A1.5 1.5 0 0 0 1.5 13h13a1.5 1.5 0 0 0 1.5-1.5V10a.5.5 0 0 0-.5-.5 1.5 1.5 0 0 1 0-3A.5.5 0 0 0 16 6V4.5A1.5 1.5 0 0 0 14.5 3zM1 4.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v1.05a2.5 2.5 0 0 0 0 4.9v1.05a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-1.05a2.5 2.5 0 0 0 0-4.9z"/>
                      </svg>
                      <span class="ml-2 text-sm">Raise Support Ticket</span>
                </a>

            </div>
        </div>
    </aside>

    <div class="fixed inset-0 z-10 hidden bg-gray-900/50 dark:bg-gray-900/90" id="sidebarBackdrop"></div>


</div>
