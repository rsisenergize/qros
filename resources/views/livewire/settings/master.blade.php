<div>
    <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px">
            @if (user()->hasRole('Admin_'.user()->restaurant_id))

            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=restaurant' }}" wire:navigate
                    @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'restaurant'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'restaurant')])>
                    @lang('modules.settings.restaurantSettings')
                </a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=app' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'app'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'app')])>@lang('modules.settings.appSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=branch' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'branch'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'branch')])>@lang('modules.settings.branchSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=currency' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'currency'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'currency')])>@lang('modules.settings.currencySettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=email' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'email'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'email')])>@lang('modules.settings.emailSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=tax' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'tax'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'tax')])>@lang('modules.settings.taxSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=payment' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'payment'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'payment')])>@lang('modules.settings.paymentgatewaySettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=theme' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'theme'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'theme')])>@lang('modules.settings.themeSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=role' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'role'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'role')])>@lang('modules.settings.roleSettings')</a>
            </li>

            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=billing' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'billing'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'billing')])>@lang('modules.settings.billing')</a>
            </li>

            @endif

            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=reservation' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'reservation'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'reservation')])>@lang('modules.settings.reservationSettings')</a>
            </li>

            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=aboutus' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'aboutus'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'aboutus')])>@lang('modules.settings.aboutUsSettings')</a>
            </li>

            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=customerSite' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'customerSite'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'customerSite')])>@lang('modules.settings.customerSiteSettings')</a>
            </li>

              <li class="me-2">
                <a href="{{ route('settings.index').'?tab=receipt' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'receipt'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'receipt')])>@lang('modules.settings.receiptSetting')</a>
            </li>

             <li class="me-2">
                <a href="{{ route('settings.index').'?tab=printer' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'printer'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'printer')])>@lang('modules.settings.printerSetting')</a>
            </li>

            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=deliverySettings' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'deliverySettings'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'deliverySettings')])>@lang('modules.settings.deliverySettings')</a>
            </li>

            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=kotSettings' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'kotSettings'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'kotSettings')])>@lang('modules.settings.kotSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=cancelSettings' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'cancelSettings'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'cancelSettings')])>@lang('modules.settings.cancelSettings')</a>
            </li>

            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=orderSettings' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'orderSettings'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'orderSettings')])>@lang('modules.settings.orderSetting')</a>
            </li>

            <!-- NAV ITEM - CUSTOM MODULES  -->
            @foreach (custom_module_plugins() as $item)
                @includeIf(strtolower($item) . '::sections.settings.restaurant.sidebar')
            @endforeach
        </ul>
    </div>

    <div class="grid grid-cols-1 pt-6 dark:bg-gray-900">

        <div>
            @switch($activeSetting)
                @case('restaurant')
                @livewire('settings.generalSettings', ['settings' => $settings])
                @break

                @case('app')
                @livewire('settings.timezoneSettings', ['settings' => $settings])
                @break

                @case('email')
                @livewire('settings.notificationSettings', ['settings' => $settings])
                @break

                @case('currency')
                @livewire('settings.currencySettings')
                @break

                @case('payment')
                @livewire('settings.paymentSettings', ['settings' => $settings])
                @break

                @case('theme')
                @livewire('settings.themeSettings', ['settings' => $settings])
                @break

                @case('role')
                @livewire('settings.roleSettings', ['settings' => $settings])
                @break

                @case('tax')
                @livewire('settings.taxSettings', ['settings' => $settings])
                @break

                @case('reservation')
                @livewire('settings.reservationSettings')
                @break

                @case('branch')
                @livewire('settings.branchSettings')
                @break
                @case('billing')
                @livewire('settings.billingSettings')
                @break

                @case('aboutus')
                @livewire('settings.aboutUsSettings', ['settings' => $settings])
                @break

                @case('customerSite')
                @livewire('settings.customerSiteSettings', ['settings' => $settings])
                @break

                @case('receipt')
                @livewire('settings.ReceiptSetting', ['settings' => $settings])
                @break

                @case('printer')
                @livewire('settings.PrinterSetting', ['settings' => $settings])
                @break

                @case('deliverySettings')
                @livewire('settings.branchDeliverySettings', ['settings' => $settings])
                @break

                @case('kotSettings')
                @livewire('settings.kotSettings', ['settings' => $settings])
                @break

                @case('cancelSettings')
                @livewire('settings.CancellationSettings', ['settings' => $settings])
                @break

                @case('orderSettings')
                @livewire('settings.OrderSettings', ['settings' => $settings])
                @break

                @default

            @endswitch

             <!-- NAV ITEM - CUSTOM MODULES  -->
             @foreach (custom_module_plugins() as $item)
                @if($activeSetting == $item.'Settings')
                    @livewire($item.'::restaurant.setting')
                @endif
           @endforeach
        </div>

    </div>

</div>
