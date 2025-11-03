<div @if(!pusherSettings()->is_enabled_pusher_broadcast) wire:poll.10s @endif>
    <div class="block p-4 bg-white dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-4">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white flex justify-between items-center">
                <div class="flex items-center gap-2">
                    @if($showAllKitchens)
                        {{ __('kitchen::modules.menu.allKitchenKot') }}
                    @else
                        {{ $kotPlace?->name }}
                        @lang('menu.kot')
                    @endif
                    @if(pusherSettings()->is_enabled_pusher_broadcast)
                        <div class="flex items-center gap-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            @lang('app.realTime')
                        </div>
                    @endif
                </div>
                @if (!$showAllKitchens && $kotPlace && $kotPlace->printerSetting)
                    <div class="text-lg font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $kotPlace->printerSetting->name }}
                    </div>
                @endif
            </h1>
        </div>

        <div class="flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center">
            <div class="w-full lg:w-auto">
                <div class="w-full">
                    <form class="w-full" action="#" method="GET">
                        <div class="flex flex-col gap-4 md:flex-row">
                            @if($showAllKitchens)
                                <!-- Kitchen Filter for All Kitchens View -->
                                <x-select id="selectedKitchen" class="w-full md:w-48" wire:model.live="selectedKitchen">
                                    <option value="">{{ __('kitchen::modules.menu.allKitchens') }}</option>
                                    @foreach($kitchens as $kitchen)
                                        <option value="{{ $kitchen->id }}">{{ $kitchen->name }}</option>
                                    @endforeach
                                </x-select>


                            @endif

                            <x-select id="dateRangeType" class="w-full md:w-48" wire:model="dateRangeType"
                                wire:change="setDateRange">
                                <option value="today">@lang('app.today')</option>
                                <option value="currentWeek">@lang('app.currentWeek')</option>
                                <option value="lastWeek">@lang('app.lastWeek')</option>
                                <option value="last7Days">@lang('app.last7Days')</option>
                                <option value="currentMonth">@lang('app.currentMonth')</option>
                                <option value="lastMonth">@lang('app.lastMonth')</option>
                                <option value="currentYear">@lang('app.currentYear')</option>
                                <option value="lastYear">@lang('app.lastYear')</option>
                            </x-select>

                            <div id="date-range-picker" date-rangepicker class="flex flex-col w-full gap-2 sm:flex-row">
                                <div class="relative flex-1">
                                    <div class="absolute inset-y-0 flex items-center pointer-events-none start-0 ps-3">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                        </svg>
                                    </div>
                                    <input id="datepicker-range-start" name="start" type="text"
                                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 ps-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        wire:model.change='startDate' placeholder="@lang('app.selectStartDate')">
                                </div>
                                <span class="self-center hidden text-gray-500 sm:block">@lang('app.to')</span>
                                <div class="relative flex-1">
                                    <div class="absolute inset-y-0 flex items-center pointer-events-none start-0 ps-3">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                        </svg>
                                    </div>
                                    <input id="datepicker-range-end" name="end" type="text"
                                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 ps-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        wire:model.live='endDate' placeholder="@lang('app.selectEndDate')">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div @class([
                'grid grid-cols-1 gap-2 w-full lg:w-auto',
                'sm:grid-cols-4' => $kotSettings->default_status == 'pending',
                'sm:grid-cols-3' => $kotSettings->default_status == 'cooking',
                ])>
                @if ($kotSettings->default_status == 'pending')
                    <div wire:click="$set('filterOrders', 'pending_confirmation')" @class([
                        'whitespace-nowrap items-center font-medium
                                                            cursor-pointer p-2 text-center rounded-md text-sm border hover:text-gray-900 bg-white
                                                            hover:bg-gray-200 w-full dark:bg-gray-800 dark:hover:bg-gray-700 cols
                                                            dark:hover:text-white dark:text-neutral-400',
                        ' border-2 border-skin-base dark:border-skin-base' =>
                            $filterOrders == 'pending_confirmation',
                    ])>
                        @lang('modules.reservation.Pending') ({{ $pendingConfirmationCount }})
                    </div>
                @endif

                <div wire:click="$set('filterOrders', 'in_kitchen')" @class([
                    'whitespace-nowrap items-center font-medium
                                                    cursor-pointer p-2 text-center rounded-md text-sm border hover:text-gray-900 bg-white
                                                    hover:bg-gray-200 w-full dark:bg-gray-800 dark:hover:bg-gray-700
                                                    dark:hover:text-white dark:text-neutral-400',
                    ' border-2 border-skin-base dark:border-skin-base' =>
                        $filterOrders == 'in_kitchen',
                ])>
                    @lang('modules.order.in_kitchen') ({{ $inKitchenCount }})
                </div>
                <div wire:click="$set('filterOrders', 'food_ready')" @class([
                    'whitespace-nowrap items-center font-medium
                                                    cursor-pointer p-2 text-center rounded-md text-sm border hover:text-gray-900 bg-white
                                                    hover:bg-gray-200 w-full dark:bg-gray-800 dark:hover:bg-gray-700
                                                    dark:hover:text-white dark:text-neutral-400',
                    ' border-2 border-skin-base dark:border-skin-base' =>
                        $filterOrders == 'food_ready',
                ])>
                    @lang('modules.order.food_ready') ({{ $foodReadyCount }})
                </div>
                <div wire:click="$set('filterOrders', 'cancelled')" @class([
                    'whitespace-nowrap items-center font-medium
                                                    cursor-pointer p-2 text-center rounded-md text-sm border hover:text-gray-900 bg-white
                                                    hover:bg-gray-200 w-full dark:bg-gray-800 dark:hover:bg-gray-700
                                                    dark:hover:text-white dark:text-neutral-400',
                    ' border-2 border-skin-base dark:border-skin-base' =>
                        $filterOrders == 'cancelled',
                ])>
                    @lang('modules.order.cancelled') ({{ $cancelledCount }})
                </div>

            </div>
        </div>

        <div class="flex flex-col my-4">
            {{-- @dd(restaurant_modules()) --}}
            {{-- @dd( in_array('kitchen', custom_module_plugins())); --}}

            <!-- Card Section -->
            <div class="space-y-4">
                <div class="grid sm:grid-cols-3 2xl:grid-cols-4 gap-3 sm:gap-4">
                    @foreach ($kots as $item)
                        @livewire('kot.kot-card', ['kot' => $item, 'kotSettings' => $kotSettings, 'cancelReasons' => $cancelReasons, 'kotPlace' => $kotPlace, 'showAllKitchens' => $showAllKitchens], key('kot-' . $item->id . '-' . $item->updated_at->timestamp))
                    @endforeach
                </div>
            </div>

            <!-- End Card Section -->

        </div>

        <x-confirmation-modal wire:model="confirmDeleteKotModal">
            <x-slot name="title">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">@lang('modules.order.cancelKot')</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">This action cannot be undone</p>
                </div>
            </x-slot>

            <x-slot name="content" class="col-span-full">
                <div class="flex flex-col w-full space-y-6">
                    <!-- Warning Message -->
                    <x-alert type="warning" class="w-full mb-0">
                        <div class="flex items-start gap-3">
                            <svg class="flex-shrink-0 mt-0.5 w-5 h-5 text-amber-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">@lang('modules.order.cancelKotMessage')
                                </p>
                                <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">Please select a reason for
                                    cancellation</p>
                            </div>
                        </div>
                    </x-alert>

                    <!-- Reason Selection -->
                    <div class="w-full">
                        <x-label for="cancelReason" value="{{ __('modules.settings.selectCancelReason') }}"
                            class="text-sm font-medium text-gray-700 dark:text-gray-200" />
                        <x-select id="cancelReason" class="block w-full mt-2" wire:model="cancelReason">
                            <option value="">{{ __('modules.settings.selectCancelReason') }}</option>
                            @foreach ($cancelReasons as $reason)
                                <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error for="cancelReason" class="mt-2" />
                    </div>

                    <!-- Custom Reason Textarea -->
                    <div class="w-full">
                        <textarea wire:model="cancelReasonText" id="cancelReasonText" rows="4"
                            class="block w-full px-4 py-3 transition-all duration-200 border-2 border-gray-300 shadow-sm resize-none rounded-xl dark:border-gray-600 focus:ring-2 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="@lang('modules.settings.enterCancelReason')"></textarea>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$set('confirmDeleteKotModal', false)" wire:loading.attr="disabled">
                    {{ __('app.cancel') }}
                </x-secondary-button>

                <x-danger-button class="ml-3" wire:click="deleteKot({{ $selectedCancelKotId }})"
                    wire:loading.attr="disabled">
                    @lang('modules.order.cancelKot')
                </x-danger-button>
            </x-slot>
        </x-confirmation-modal>

        <!-- Delete KOT Item Modal -->
        <x-confirmation-modal wire:model="confirmDeleteKotItemModal" max-width="xl">

            <x-slot name="title">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        @lang('modules.order.cancelKotItem')
                        @if($this->selectedKotItem)
                            ({{ $this->selectedKotItem->menuItem->item_name ?? 'N/A' }})
                        @endif
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">@lang('modules.order.actionCannotBeUndone')</p>
                </div>
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col w-full space-y-6">
                    <!-- Warning Message -->
                    <x-alert type="warning" class="w-full mb-0">
                        <div class="flex items-start gap-3">
                            <svg class="flex-shrink-0 mt-0.5 w-5 h-5 text-amber-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">@lang('modules.order.cancelKotItemMessage')
                                </p>
                                <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">@lang('modules.order.pleaseSelectReasonForCancellation')</p>
                            </div>
                        </div>
                    </x-alert>

                    <!-- Reason Selection -->
                    <div class="w-full space-y-4">
                        <div>
                            <x-label for="cancelItemReason" value="{{ __('modules.settings.selectCancelReason') }}"
                                class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2" />
                            <x-select id="cancelItemReason" class="block w-full" wire:model.live="cancelItemReason" onchange="console.log('Select changed:', this.value); @this.set('cancelItemReason', this.value);">
                                <option value="">{{ __('modules.settings.selectCancelReason') }}</option>
                                @foreach ($cancelReasons as $reason)
                                    <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                                @endforeach
                            </x-select>
                            <x-input-error for="cancelItemReason" class="mt-2" />
                        </div>

                        <!-- Custom Reason Textarea -->
                        <div>
                            <x-label for="cancelItemReasonText" value="{{ __('modules.settings.enterCancelReason') }}"
                                class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2" />
                            <textarea wire:model.live="cancelItemReasonText" id="cancelItemReasonText" rows="4"
                                class="block w-full px-4 py-3 transition-all duration-200 border-2 border-gray-300 shadow-sm resize-none rounded-xl dark:border-gray-600 focus:ring-2 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="@lang('modules.settings.enterCancelReason')"
                                oninput="console.log('Textarea input:', this.value); @this.set('cancelItemReasonText', this.value);"></textarea>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$set('confirmDeleteKotItemModal', false)" wire:loading.attr="disabled">
                    {{ __('app.cancel') }}
                </x-secondary-button>

                <x-danger-button class="ml-3" wire:click="deleteKotItem({{ $selectedCancelKotItemId }})"
                    wire:loading.attr="disabled">
                    @lang('modules.order.cancelKotItem')
                </x-danger-button>
            </x-slot>
        </x-confirmation-modal>

    </div>

    @push('scripts')


    @if(pusherSettings()->is_enabled_pusher_broadcast)
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const channel = PUSHER.subscribe('kots');
                channel.bind('kot.updated', function(data) {

                    try {
                        @this.dispatch('refreshKots');
                    } catch (error) {
                        console.error('❌ Error in Pusher refreshKots:', error);
                    }

                    console.log('✅ Pusher received data for kots!. Refreshing...');
                });
                PUSHER.connection.bind('connected', () => {
                    console.log('✅ Pusher connected for Kots!');
                });
                channel.bind('pusher:subscription_succeeded', () => {
                    console.log('✅ Subscribed to kots channel!');
                });
            });
        </script>
    @endif


@endpush
