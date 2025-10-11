<div>
    <div class="p-4 bg-white block  dark:bg-gray-800 dark:border-gray-700">
        <div class="flex mb-4">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('menu.orders') ({{ $orders->count() }})</h1>
            <div class="ml-auto flex items-center gap-4">
                <div class="flex items-center gap-2">
                    @if(pusherSettings()->is_enabled_pusher_broadcast)
                        <div class="flex items-center gap-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            @lang('app.realTime')
                        </div>
                    @else
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" wire:model.live="pollingEnabled">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">@lang('app.autoRefresh')</span>
                        </label>
                        <x-select class="w-32 text-sm" wire:model.live="pollingInterval" :disabled="!$pollingEnabled">
                            <option value="5">5 @lang('app.seconds')</option>
                            <option value="10">10 @lang('app.seconds')</option>
                            <option value="15">15 @lang('app.seconds')</option>
                            <option value="30">30 @lang('app.seconds')</option>
                            <option value="60">1 @lang('app.minute')</option>
                        </x-select>
                    @endif

                    <x-select class="w-32 text-sm" wire:model.live.debounce.250ms='filterOrderType'>
                        <option value="">@lang('modules.order.all')</option>
                        <option value="dine_in">@lang('modules.order.dine_in')</option>
                        <option value="delivery">@lang('modules.order.delivery')</option>
                        <option value="pickup">@lang('modules.order.pickup')</option>
                    </x-select>

                </div>
            </div>
        </div>

        <div class="items-center justify-between block sm:flex ">
            <div class="lg:flex items-center mb-4 sm:mb-0">
                <form class="ltr:sm:pr-3 rtl:sm:pl-3" action="#" method="GET">

                    <div class="lg:flex gap-2 items-center">
                        <x-select id="dateRangeType" class="block w-fit" wire:model="dateRangeType"
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

                        <div id="date-range-picker" date-rangepicker class="flex items-center w-full">
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                    </svg>
                                </div>
                                <input id="datepicker-range-start" name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model.change='startDate' placeholder="@lang('app.selectStartDate')">
                                </div>
                                <span class="mx-4 text-gray-500">@lang('app.to')</span>
                                <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                    </svg>
                                </div>
                                <input id="datepicker-range-end" name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model.live='endDate' placeholder="@lang('app.selectEndDate')">
                            </div>
                        </div>
                    </div>
                </form>


                <div class="inline-flex gap-2">
                    <x-select class="text-sm w-full" wire:model.live.debounce.250ms='filterOrders'>
                        <option value="">@lang('app.showAll') @lang('menu.orders')</option>
                        <option value="kot">@lang('modules.order.kot') ({{ $kotCount }})</option>
                        <option value="billed">@lang('modules.order.billed') ({{ $billedCount }})</option>
                        <option value="paid">@lang('modules.order.paid') ({{ $paidOrdersCount }})</option>
                        <option value="canceled">@lang('modules.order.canceled') ({{ $canceledOrdersCount }})</option>
                        <option value="out_for_delivery">@lang('modules.order.out_for_delivery') ({{ $outDeliveryOrdersCount }})</option>
                        <option value="payment_due">@lang('modules.order.payment_due') ({{ $paymentDueCount }})</option>
                        <option value="delivered">@lang('modules.order.delivered') ({{ $deliveredOrdersCount }})</option>
                    </x-select>

                    @if(!user()->hasRole('Waiter_' . user()->restaurant_id))
                    <x-select class="text-sm w-full" wire:model.live.debounce.250ms='filterWaiter'>
                        <option value="">@lang('app.showAll') @lang('modules.order.waiter')</option>
                        @foreach ($waiters as $waiter)
                            <option value="{{ $waiter->id }}">{{ $waiter->name }}</option>
                        @endforeach
                    </x-select>
                    @endif

                </div>

            </div>

            @if(user_can('Create Order'))
                <x-primary-link wire:navigate href="{{ route('pos.index') }}">@lang('modules.order.newOrder')</x-primary-link>
            @endif

        </div>
    </div>

    <div class="flex flex-col my-4 px-4">

        <!-- Card Section -->
        <div class="space-y-4">


            <div wire:loading>
                <div class="grid sm:grid-cols-3 2xl:grid-cols-4 gap-3 sm:gap-4">
                    @for ($i = 0; $i < 6; $i++)
                        <div class="flex-col gap-3 items-center border bg-white shadow-sm rounded-lg dark:bg-gray-700 dark:border-gray-600 p-3 animate-pulse">
                            <div class="group flex flex-col gap-3 items-center">
                                <div class="flex gap-4 justify-between w-full">
                                    <div class="flex gap-3 space-y-1">
                                        <!-- Table/Order Type Icon -->
                                        <div class="p-3 rounded-lg bg-gray-200 dark:bg-gray-600 w-10 h-10"></div>

                                        <!-- Customer Info -->
                                        <div>
                                            <div class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-32 mb-1"></div>
                                            <div class="h-3 bg-gray-200 dark:bg-gray-600 rounded w-24"></div>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="ltr:text-right rtl:text-left">
                                        <div class="h-5 bg-gray-200 dark:bg-gray-600 rounded w-20 mb-1"></div>
                                        <div class="h-3 bg-gray-200 dark:bg-gray-600 rounded w-16"></div>
                                    </div>
                                </div>

                                <!-- Date and Items Count -->
                                <div class="flex w-full justify-between items-center">
                                    <div class="h-3 bg-gray-200 dark:bg-gray-600 rounded w-32"></div>
                                    <div class="h-3 bg-gray-200 dark:bg-gray-600 rounded w-16"></div>
                                </div>

                                <!-- Footer -->
                                <div class="flex w-full justify-between items-center border-t dark:border-gray-500 pt-3">
                                    <div class="h-5 bg-gray-200 dark:bg-gray-600 rounded w-24"></div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-20"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="grid sm:grid-cols-3 2xl:grid-cols-4 gap-3 sm:gap-4" wire:loading.remove>
                @foreach ($orders as $item)
                    <x-order.order-card :order='$item' wire:key='order-{{ $item->id . microtime() }}' />
                @endforeach
            </div>
        </div>
        <!-- End Card Section -->


    </div>

    @script
    <script>
        const datepickerEl1 = document.getElementById('datepicker-range-start');

        datepickerEl1.addEventListener('changeDate', (event) => {
            $wire.dispatch('setStartDate', { start: datepickerEl1.value });
        });

        const datepickerEl2 = document.getElementById('datepicker-range-end');

        datepickerEl2.addEventListener('changeDate', (event) => {
            $wire.dispatch('setEndDate', { end: datepickerEl2.value });
        });

        // Handle polling
        let pollingInterval = null;
        let pusherChannel = null;

        function startPolling() {
            console.log('🔄 Starting polling for orders...');
            if (pollingInterval) {
                console.log('🔄 Clearing existing polling interval');
                clearInterval(pollingInterval);
            }
            const interval = $wire.get('pollingInterval') * 1000;
            console.log('📊 Orders polling settings:', {
                interval: interval,
                intervalSeconds: $wire.get('pollingInterval'),
                pollingEnabled: $wire.get('pollingEnabled')
            });
            pollingInterval = setInterval(() => {
                if ($wire.get('pollingEnabled')) {
                    console.log('🔄 Orders polling: Refreshing data...');
                    $wire.$refresh();
                } else {
                    console.log('⏸️ Orders polling: Disabled, stopping...');
                    stopPolling();
                }
            }, interval);
            console.log('✅ Orders polling started');
        }

        function stopPolling() {
            console.log('🛑 Stopping polling for orders...');
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
                console.log('✅ Orders polling stopped');
            } else {
                console.log('⚠️ Orders polling was already stopped');
            }
        }

        function initializePusher() {
            try {
                console.log('🚀 Initializing Pusher for orders...');

                if (typeof window.PUSHER === 'undefined') {
                    console.error('❌ PUSHER is not defined for orders');
                    return;
                }

                console.log('📊 Pusher orders connection state:', window.PUSHER.connection.state);
                console.log('🔗 Pusher orders connection options:', {
                    encrypted: window.PUSHER.connection.options.encrypted,
                    cluster: window.PUSHER.connection.options.cluster,
                    key: window.PUSHER.connection.options.key ? '***' + window.PUSHER.connection.options.key.slice(-4) : 'undefined'
                });

                // Add comprehensive connection event listeners
                window.PUSHER.connection.bind('connected', () => {
                    console.log('✅ Pusher orders connected successfully!');
                    console.log('📊 Pusher orders connection ID:', window.PUSHER.connection.connection_id);
                    console.log('🔗 Pusher orders socket ID:', window.PUSHER.connection.socket_id);
                });

                window.PUSHER.connection.bind('disconnected', () => {
                    console.log('❌ Pusher orders disconnected!');
                });



                window.PUSHER.connection.bind('connecting', () => {
                    console.log('🔄 Pusher orders connecting...');
                });

                window.PUSHER.connection.bind('reconnecting', () => {
                    console.log('🔄 Pusher orders reconnecting...');
                });

                window.PUSHER.connection.bind('reconnected', () => {
                    console.log('✅ Pusher orders reconnected!');
                    console.log('📊 Pusher orders reconnection details:', {
                        socketId: window.PUSHER.connection.socket_id,
                        connectionId: window.PUSHER.connection.connection_id,
                        state: window.PUSHER.connection.state
                    });
                });

                // Add connection retry logic
                let connectionRetryCount = 0;
                const maxRetries = 3;

                    window.PUSHER.connection.bind('error', (error) => {
                    connectionRetryCount++;
                    console.error(`❌ Pusher orders connection error (attempt ${connectionRetryCount}/${maxRetries}):`, error);
                    console.error('❌ Pusher orders error details:', {
                        type: error.type,
                        error: error.error,
                        data: error.data,
                        message: error.message,
                        code: error.code
                    });

                    // Log additional debugging info
                    console.error('🔍 Pusher orders debugging info:', {
                        connectionState: window.PUSHER.connection.state,
                        socketId: window.PUSHER.connection.socket_id,
                        connectionId: window.PUSHER.connection.connection_id,
                        options: window.PUSHER.connection.options,
                        url: window.PUSHER.connection.options.wsHost || 'default',
                        encrypted: window.PUSHER.connection.options.encrypted,
                        cluster: window.PUSHER.connection.options.cluster
                    });

                    // Check if it's a WebSocket error
                    if (error.type === 'WebSocketError') {
                        console.error('🌐 WebSocket specific error:', {
                            wsError: error.error,
                            wsErrorType: error.error?.type,
                            wsErrorData: error.error?.data
                        });

                        // Check for quota exceeded error
                        if (error.error?.data?.code === 4004) {
                            console.error('❌ PUSHER QUOTA EXCEEDED: Account has exceeded its usage limits');
                            console.error('💡 Solutions:');
                            console.error('   1. Upgrade your Pusher plan');
                            console.error('   2. Reduce connection count');
                            console.error('   3. Switch to polling mode temporarily');

                            // Automatically fall back to polling after quota error
                            if (connectionRetryCount >= 2) {
                                console.error('🔄 Falling back to polling due to quota exceeded');
                                stopPusher();
                                if ($wire.get('pollingEnabled')) {
                                    startPolling();
                                }
                            }
                        }
                    }

                    if (connectionRetryCount >= maxRetries) {
                        console.error('❌ Pusher orders: Max retry attempts reached, falling back to polling');
                        // Fall back to polling
                        stopPusher();
                        if ($wire.get('pollingEnabled')) {
                            startPolling();
                        }
                    }
                });

                window.PUSHER.connection.bind('connected', () => {
                    connectionRetryCount = 0; // Reset retry count on successful connection
                    console.log('✅ Pusher orders connected successfully!');
                    console.log('📊 Pusher orders connection ID:', window.PUSHER.connection.connection_id);
                    console.log('🔗 Pusher orders socket ID:', window.PUSHER.connection.socket_id);

                    // Log connection for monitoring (optional - remove if not needed)
                    try {
                        fetch('/api/log-pusher-connection', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                socket_id: window.PUSHER.connection.socket_id,
                                connection_id: window.PUSHER.connection.connection_id,
                                component: 'orders',
                                timestamp: new Date().toISOString()
                            })
                        }).catch(err => console.log('📊 Connection logging failed (optional):', err));
                    } catch (err) {
                        console.log('📊 Connection logging not available');
                    }
                });

                // Subscribe to orders channel
                console.log('📡 Subscribing to orders channel...');
                pusherChannel = window.PUSHER.subscribe('orders');

                // Add comprehensive subscription event listeners
                pusherChannel.bind('pusher:subscription_succeeded', () => {
                    console.log('✅ Pusher orders: Successfully subscribed to orders channel!');
                    console.log('📊 Pusher orders channel state:', {
                        subscribed: pusherChannel.subscribed,
                        subscriptionPending: pusherChannel.subscriptionPending,
                        name: pusherChannel.name
                    });
                });

                pusherChannel.bind('pusher:subscription_error', (error) => {
                    console.error('❌ Pusher orders subscription error:', error);
                    console.error('❌ Pusher orders subscription error details:', {
                        error: error.error,
                        type: error.type,
                        data: error.data
                    });
                });

                // Bind to order events
                pusherChannel.bind('order.updated', function(data) {
                    console.log('🎉 Pusher orders: Order updated via Pusher:', data);
                    console.log('📊 Pusher orders: Order update details:', {
                        order_id: data.order_id,
                        timestamp: new Date().toISOString(),
                        event_type: 'order.updated'
                    });
                    $wire.$refresh();
                });

                pusherChannel.bind('order.created', function(data) {
                    console.log('🎉 Pusher orders: Order created via Pusher:', data);
                    console.log('📊 Pusher orders: Order creation details:', {
                        order_id: data.order_id,
                        timestamp: new Date().toISOString(),
                        event_type: 'order.created'
                    });
                    $wire.$refresh();
                });

                // Debug: show all event bindings on the channel
                if (pusherChannel && typeof pusherChannel.eventNames === 'function') {
                    console.log('📋 Pusher orders channel event bindings:', pusherChannel.eventNames());
                }

                // Check if the channel is actually subscribed
                if (pusherChannel && typeof pusherChannel.subscriptionPending !== 'undefined') {
                    if (pusherChannel.subscriptionPending) {
                        console.log('⏳ Pusher orders subscription is pending...');
                    } else if (pusherChannel.subscribed) {
                        console.log('✅ Pusher orders channel is subscribed.');
                    } else {
                        console.log('❌ Pusher orders channel is not subscribed yet.');
                    }
                }

                // Log channel properties
                console.log('📊 Pusher orders channel properties:', {
                    name: pusherChannel.name,
                    subscribed: pusherChannel.subscribed,
                    subscriptionPending: pusherChannel.subscriptionPending,
                    eventNames: typeof pusherChannel.eventNames === 'function' ? pusherChannel.eventNames() : 'N/A'
                });

                // Log connection details
                console.log('📊 Pusher orders connection details:', {
                    state: window.PUSHER.connection.state,
                    socket_id: window.PUSHER.connection.socket_id,
                    connection_id: window.PUSHER.connection.connection_id,
                    options: {
                        encrypted: window.PUSHER.connection.options.encrypted,
                        cluster: window.PUSHER.connection.options.cluster,
                        key: window.PUSHER.connection.options.key ? '***' + window.PUSHER.connection.options.key.slice(-4) : 'undefined'
                    }
                });

                console.log('✅ Pusher orders initialized successfully');

            } catch (error) {
                console.error('❌ Pusher orders initialization failed:', error);
                console.error('❌ Pusher orders error stack:', error.stack);
            }
        }

        function stopPusher() {
            console.log('🛑 Stopping Pusher for orders...');
            if (pusherChannel) {
                console.log('📊 Pusher orders channel state before unsubscribe:', {
                    name: pusherChannel.name,
                    subscribed: pusherChannel.subscribed,
                    subscriptionPending: pusherChannel.subscriptionPending
                });
                pusherChannel.unsubscribe();
                console.log('✅ Pusher orders channel unsubscribed');
                pusherChannel = null;
            } else {
                console.log('⚠️ Pusher orders channel was already null');
            }

            // Clean up any event listeners
            if (window.PUSHER && window.PUSHER.connection) {
                try {
                    window.PUSHER.connection.unbind('connected');
                    window.PUSHER.connection.unbind('disconnected');
                    window.PUSHER.connection.unbind('error');
                    window.PUSHER.connection.unbind('connecting');
                    window.PUSHER.connection.unbind('reconnecting');
                    window.PUSHER.connection.unbind('reconnected');
                    console.log('🧹 Pusher orders connection event listeners cleaned up');
                } catch (err) {
                    console.log('⚠️ Error cleaning up Pusher event listeners:', err);
                }
            }
        }

                function testPusherConnection() {
            console.log('🧪 Testing Pusher connection...');
            console.log('📊 Pusher settings:', {
                defined: typeof window.PUSHER !== 'undefined',
                settingsDefined: typeof window.PUSHER_SETTINGS !== 'undefined',
                broadcastEnabled: typeof window.PUSHER_SETTINGS !== 'undefined' ? window.PUSHER_SETTINGS.is_enabled_pusher_broadcast : 'undefined'
            });

            if (typeof window.PUSHER_SETTINGS !== 'undefined') {
                console.log('📊 PUSHER_SETTINGS details:', {
                    pusher_key: window.PUSHER_SETTINGS.pusher_key,
                    pusher_cluster: window.PUSHER_SETTINGS.pusher_cluster,
                    pusher_app_id: window.PUSHER_SETTINGS.pusher_app_id,
                    is_enabled_pusher_broadcast: window.PUSHER_SETTINGS.is_enabled_pusher_broadcast
                });
            }

            if (typeof window.PUSHER !== 'undefined') {
                console.log('📊 Pusher connection state:', window.PUSHER.connection.state);
                console.log('📊 Pusher connection options:', window.PUSHER.connection.options);
            }
        }

        function refreshPusherSettings() {
            console.log('🔄 Refreshing Pusher settings...');
            // Clear any cached settings and reload
            if (typeof window.PUSHER_SETTINGS !== 'undefined') {
                delete window.PUSHER_SETTINGS;
            }
            if (typeof window.PUSHER !== 'undefined') {
                delete window.PUSHER;
            }
            console.log('✅ Pusher settings cleared, reload page to refresh');
        }

                function disablePusherTemporarily() {
            console.log('🛑 Temporarily disabling Pusher due to quota issues...');
            // Send request to disable Pusher temporarily
            fetch('/api/disable-pusher-temporarily', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(() => {
                console.log('✅ Pusher disabled, switching to polling...');
                stopPusher();
                if ($wire.get('pollingEnabled')) {
                    startPolling();
                }
            }).catch(err => {
                console.log('❌ Failed to disable Pusher:', err);
            });
        }

        function forceDisconnectAllConnections() {
            console.log('🛑 Force disconnecting all Pusher connections...');

            // Disconnect global Pusher
            if (window.GLOBAL_PUSHER) {
                window.GLOBAL_PUSHER.disconnect();
                console.log('✅ Global Pusher disconnected');
            }

            // Disconnect local Pusher
            if (window.PUSHER) {
                window.PUSHER.disconnect();
                console.log('✅ Local Pusher disconnected');
            }

            // Clear all references
            window.GLOBAL_PUSHER = null;
            window.PUSHER = null;
            pusherChannel = null;

            console.log('🧹 All Pusher connections cleared');
            console.log('💡 Reload the page to reconnect with fresh connections');
        }

                // Initialize real-time updates
                document.addEventListener('livewire:initialized', () => {
            console.log('🚀 Livewire orders component initialized');
            console.log('📊 Pusher settings check:', {
                pusherSettingsDefined: typeof window.PUSHER_SETTINGS !== 'undefined',
                pusherBroadcastEnabled: typeof window.PUSHER_SETTINGS !== 'undefined' ? window.PUSHER_SETTINGS.is_enabled_pusher_broadcast : 'undefined'
            });

            // Test Pusher connection for debugging
            testPusherConnection();

            // Add manual refresh option for debugging
            window.refreshPusherSettings = refreshPusherSettings;
            window.disablePusherTemporarily = disablePusherTemporarily;
            window.forceDisconnectAllConnections = forceDisconnectAllConnections;
            console.log('🛠️ Debug: Use refreshPusherSettings() in console to clear cached settings');
            console.log('🛠️ Debug: Use disablePusherTemporarily() in console to disable Pusher due to quota issues');
            console.log('🛠️ Debug: Use forceDisconnectAllConnections() in console to force disconnect all connections');

            if (typeof window.PUSHER_SETTINGS !== 'undefined' && window.PUSHER_SETTINGS.is_enabled_pusher_broadcast) {
                console.log('✅ Pusher orders: Using Pusher for real-time updates');
                initializePusher();
            } else {
                console.log('📡 Pusher orders: Using polling for real-time updates');
                console.log('📊 Pusher orders polling settings:', {
                    pollingEnabled: $wire.get('pollingEnabled'),
                    pollingInterval: $wire.get('pollingInterval')
                });
                if ($wire.get('pollingEnabled')) {
                    startPolling();
                }
            }
        });

        // Watch for changes
        $wire.watch('pollingEnabled', (value) => {
            console.log('👀 Orders pollingEnabled changed:', value);
            if (typeof window.PUSHER_SETTINGS !== 'undefined' && !window.PUSHER_SETTINGS.is_enabled_pusher_broadcast) {
                if (value) {
                    console.log('🔄 Orders: Starting polling due to pollingEnabled change');
                    startPolling();
                } else {
                    console.log('🛑 Orders: Stopping polling due to pollingEnabled change');
                    stopPolling();
                }
            } else {
                console.log('📡 Orders: Pusher is enabled, ignoring polling changes');
            }
        });

        $wire.watch('pollingInterval', (value) => {
            console.log('👀 Orders pollingInterval changed:', value);
            if (typeof window.PUSHER_SETTINGS !== 'undefined' && !window.PUSHER_SETTINGS.is_enabled_pusher_broadcast && $wire.get('pollingEnabled')) {
                console.log('🔄 Orders: Restarting polling due to interval change');
                startPolling();
            } else {
                console.log('📡 Orders: Pusher is enabled or polling disabled, ignoring interval change');
            }
        });

        // Cleanup on component destroy
        document.addEventListener('livewire:initialized', () => {
            return () => {
                stopPolling();
                stopPusher();
            };
        });
    </script>
    @endscript

</div>
