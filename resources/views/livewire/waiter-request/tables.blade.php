<div @if(!pusherSettings()->is_enabled_pusher_broadcast && $pollingEnabled) wire:poll.{{ $pollingInterval }}s @endif>
    <div class="p-4 bg-white block  dark:bg-gray-800 dark:border-gray-700">
        <div class="flex mb-4">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('menu.waiterRequest') ({{ $tables->sum(function($area) { return $area->tables->count(); }) }})</h1>
            <div class="ml-auto flex items-center gap-4">
                <div class="flex items-center gap-2">
                    @if(pusherSettings()->is_enabled_pusher_broadcast)
                        <div class="flex items-center gap-2 px-3 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            @lang('app.realTime')
                        </div>
                    @else
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" wire:model.live="pollingEnabled">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="text-sm font-medium text-gray-900 ms-3 dark:text-gray-300">@lang('app.autoRefresh')</span>
                        </label>
                        <x-select class="w-32 text-sm" wire:model.live="pollingInterval" :disabled="!$pollingEnabled">
                            <option value="5">5 @lang('app.seconds')</option>
                            <option value="10">10 @lang('app.seconds')</option>
                            <option value="15">15 @lang('app.seconds')</option>
                            <option value="30">30 @lang('app.seconds')</option>
                            <option value="60">1 @lang('app.minute')</option>
                        </x-select>
                    @endif



                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col px-4 my-4">


        <!-- Card Section -->
        <div class="space-y-8">
            @foreach ($tables as $area)

                <div class="flex flex-col gap-3 space-y-1 sm:gap-4" wire:key='area-{{ $area->id . microtime() }}'>
                    <h3 class="inline-flex items-center gap-2 font-medium f-15 dark:text-neutral-200">{{ $area->area_name }}
                        <span class="px-2 py-1 text-sm text-gray-800 border border-gray-300 rounded bg-slate-100 ">{{ $area->tables->count() }} @lang('modules.table.table')</span>
                    </h3>
                    <!-- Card -->

                    <div class="grid gap-3 sm:grid-cols-3 2xl:grid-cols-4 sm:gap-6">
                        @forelse ($area->tables as $item)
                        <a
                        @class(['group flex flex-col gap-2 border shadow-sm rounded-lg hover:shadow-md transition dark:bg-gray-700 dark:border-gray-600 p-3', 'bg-red-50' => ($item->status == 'inactive'), 'bg-white' => ($item->status == 'active')])

                        wire:key='table-{{ $item->id . microtime() }}'
                            href="javascript:;">
                            <div class="flex items-center justify-between w-full gap-4 cursor-pointer" wire:click='showTableOrder({{ $item->id }})'>
                                <div @class(['p-3 rounded-lg tracking-wide ',
                                'bg-green-100 text-green-600' => ($item->available_status == 'available'),
                                'bg-red-100 text-red-600' => ($item->available_status == 'reserved'),
                                'bg-blue-100 text-blue-600' => ($item->available_status == 'running')])>
                                    <h3 wire:loading.class.delay='opacity-50'
                                        @class(['font-semibold'])>
                                        {{ $item->table_code }}
                                    </h3>
                                </div>
                                <div class="space-y-1">
                                    <p
                                    @class(['text-xs font-medium dark:text-neutral-200 text-gray-500 inline-flex items-center gap-1'])>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-stopwatch" viewBox="0 0 16 16">
                                        <path d="M8.5 5.6a.5.5 0 1 0-1 0v2.9h-3a.5.5 0 0 0 0 1H8a.5.5 0 0 0 .5-.5z"/>
                                        <path d="M6.5 1A.5.5 0 0 1 7 .5h2a.5.5 0 0 1 0 1v.57c1.36.196 2.594.78 3.584 1.64l.012-.013.354-.354-.354-.353a.5.5 0 0 1 .707-.708l1.414 1.415a.5.5 0 1 1-.707.707l-.353-.354-.354.354-.013.012A7 7 0 1 1 7 2.071V1.5a.5.5 0 0 1-.5-.5M8 3a6 6 0 1 0 .001 12A6 6 0 0 0 8 3"/>
                                      </svg>
                                        {{ $item->activeWaiterRequest->created_at->diffForHumans() }}
                                    </p>

                                    <div class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 -2.89 122.88 122.88" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="enable-background:new 0 0 122.88 117.09" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css">.st0{fill-rule:evenodd;clip-rule:evenodd;}</style> <g> <path class="st0" d="M36.82,107.86L35.65,78.4l13.25-0.53c5.66,0.78,11.39,3.61,17.15,6.92l10.29-0.41c4.67,0.1,7.3,4.72,2.89,8 c-3.5,2.79-8.27,2.83-13.17,2.58c-3.37-0.03-3.34,4.5,0.17,4.37c1.22,0.05,2.54-0.29,3.69-0.34c6.09-0.25,11.06-1.61,13.94-6.55 l1.4-3.66l15.01-8.2c7.56-2.83,12.65,4.3,7.23,10.1c-10.77,8.51-21.2,16.27-32.62,22.09c-8.24,5.47-16.7,5.64-25.34,1.01 L36.82,107.86L36.82,107.86z M29.74,62.97h91.9c0.68,0,1.24,0.57,1.24,1.24v5.41c0,0.67-0.56,1.24-1.24,1.24h-91.9 c-0.68,0-1.24-0.56-1.24-1.24v-5.41C28.5,63.53,29.06,62.97,29.74,62.97L29.74,62.97z M79.26,11.23 c25.16,2.01,46.35,23.16,43.22,48.06l-93.57,0C25.82,34.23,47.09,13.05,72.43,11.2V7.14l-4,0c-0.7,0-1.28-0.58-1.28-1.28V1.28 c0-0.7,0.57-1.28,1.28-1.28h14.72c0.7,0,1.28,0.58,1.28,1.28v4.58c0,0.7-0.58,1.28-1.28,1.28h-3.89L79.26,11.23L79.26,11.23 L79.26,11.23z M0,77.39l31.55-1.66l1.4,35.25L1.4,112.63L0,77.39L0,77.39z"></path> </g> </g></svg>

                                        {{ $item->activeOrder->waiter->name ?? '--' }}
                                    </div>

                                    @if ($item->available_status == 'reserved')
                                        <div class="px-1 py-0.5 border bg-red-100 border-red-700 text-md text-red-700 rounded">@lang('modules.table.reserved')</div>
                                    @endif

                                    @if ($item->status == 'inactive')
                                        <div class="inline-flex gap-1 text-xs font-semibold text-red-600">
                                            @lang('app.inactive')
                                        </div>
                                    @endif


                                </div>
                            </div>
                            <div class="flex items-center justify-between w-full gap-4">
                                <x-secondary-button wire:click='markCompleted({{ $item->activeWaiterRequest->id }})' class="flex items-center gap-2 text-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                                            <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/>
                                            <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>
                                        </svg>
                                        @lang('modules.waiterRequest.markCompleted')
                                </x-secondary-button>

                                @if ($item->activeOrder)
                                    @if(user_can('Show Order'))
                                    <x-secondary-button wire:click='showTableOrderDetail({{ $item->id }})' class="text-xs">@lang('modules.order.showOrder')</x-secondary-button>
                                    @endif
                                @endif
                            </div>
                        </a>
                        <!-- End Card -->
                        @empty
                        <div class="flex flex-col items-center justify-center p-8 text-gray-500 bg-white rounded-lg col-span-full dark:text-gray-400 dark:bg-gray-800">

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="w-10 h-10 mb-4 opacity-50" viewBox="0 0 16 16">
                                <path d="M5.164 14H15c-.299-.199-.557-.553-.78-1-.9-1.8-1.22-5.12-1.22-6q0-.396-.06-.776l-.938.938c.02.708.157 2.154.457 3.58.161.767.377 1.566.663 2.258H6.164zm5.581-9.91a4 4 0 0 0-1.948-1.01L8 2.917l-.797.161A4 4 0 0 0 4 7c0 .628-.134 2.197-.459 3.742q-.075.358-.166.718l-1.653 1.653q.03-.055.059-.113C2.679 11.2 3 7.88 3 7c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0c.942.19 1.788.645 2.457 1.284zM10 15a2 2 0 1 1-4 0zm-9.375.625a.53.53 0 0 0 .75.75l14.75-14.75a.53.53 0 0 0-.75-.75z"/>
                            </svg>
                            <p class="text-sm">@lang('modules.waiterRequest.noWaiterRequest')</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            @endforeach

        </div>
        <!-- End Card Section -->


    </div>
     <script>
        // Handle polling
        let pollingInterval = null;
        let pusherChannel = null;

        function startPolling() {
            console.log('🔄 Starting polling for waiter requests...');

            // Check if $wire is available
            if (typeof $wire === 'undefined') {
                console.log('⚠️ $wire not available, cannot start polling');
                return;
            }

            if (pollingInterval) {
                console.log('🔄 Clearing existing polling interval');
                clearInterval(pollingInterval);
            }

            try {
                const interval = $wire.get('pollingInterval') * 1000;
                console.log('📊 Waiter requests polling settings:', {
                    interval: interval,
                    intervalSeconds: $wire.get('pollingInterval'),
                    pollingEnabled: $wire.get('pollingEnabled')
                });

                pollingInterval = setInterval(() => {
                    try {
                        if ($wire.get('pollingEnabled')) {
                            console.log('🔄 Waiter requests polling: Refreshing data...');
                            $wire.$refresh();
                        } else {
                            console.log('⏸️ Waiter requests polling: Disabled, stopping...');
                            stopPolling();
                        }
                    } catch (error) {
                        console.log('❌ Error during polling refresh:', error);
                        stopPolling();
                    }
                }, interval);

                console.log('✅ Waiter requests polling started');
            } catch (error) {
                console.log('❌ Error starting polling:', error);
            }
        }

        function stopPolling() {
            console.log('🛑 Stopping polling for waiter requests...');
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
                console.log('✅ Waiter requests polling stopped');
            } else {
                console.log('⚠️ Waiter requests polling was already stopped');
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

                // Subscribe to waiter requests channel
                console.log('📡 Subscribing to waiter requests channel...');
                pusherChannel = window.PUSHER.subscribe('active-waiter-requests');

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

                // Bind to waiter request events
                pusherChannel.bind('active-waiter-requests.created', function(data) {
                    console.log('🎉 Pusher waiter requests: Waiter request created via Pusher:', data);
                    console.log('📊 Pusher waiter requests: Waiter request creation details:', {
                        waiter_request_id: data.waiter_request_id,
                        timestamp: new Date().toISOString(),
                        event_type: 'active-waiter-requests.created'
                    });
                    $wire.$refresh();
                });

                pusherChannel.bind('active-waiter-requests.updated', function(data) {
                    console.log('🎉 Pusher waiter requests: Waiter request updated via Pusher:', data);
                    console.log('📊 Pusher waiter requests: Waiter request update details:', {
                        waiter_request_id: data.waiter_request_id,
                        timestamp: new Date().toISOString(),
                        event_type: 'active-waiter-requests.updated'
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
            console.log('🚀 Livewire waiter requests component initialized');
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

            // Wait for the component to be fully ready
            setTimeout(() => {
                if (typeof window.PUSHER_SETTINGS !== 'undefined' && window.PUSHER_SETTINGS.is_enabled_pusher_broadcast) {
                    console.log('✅ Pusher waiter requests: Using Pusher for real-time updates');
                    initializePusher();
                } else {
                    console.log('📡 Pusher waiter requests: Using polling for real-time updates');
                    try {
                        console.log('📊 Pusher waiter requests polling settings:', {
                            pollingEnabled: $wire.get('pollingEnabled'),
                            pollingInterval: $wire.get('pollingInterval')
                        });
                        if ($wire.get('pollingEnabled')) {
                            startPolling();
                        }
                    } catch (error) {
                        console.log('⚠️ Component not ready yet, will retry polling setup');
                        // Retry after a short delay
                        setTimeout(() => {
                            try {
                                if ($wire.get('pollingEnabled')) {
                                    startPolling();
                                }
                            } catch (retryError) {
                                console.log('❌ Failed to initialize polling:', retryError);
                            }
                        }, 1000);
                    }
                }
            }, 100);
        });

        // Watch for changes - only set up after component is ready
        document.addEventListener('livewire:initialized', () => {
            setTimeout(() => {
                try {
                    $wire.watch('pollingEnabled', (value) => {
                        console.log('👀 Waiter requests pollingEnabled changed:', value);
                        if (typeof window.PUSHER_SETTINGS !== 'undefined' && !window.PUSHER_SETTINGS.is_enabled_pusher_broadcast) {
                            if (value) {
                                console.log('🔄 Waiter requests: Starting polling due to pollingEnabled change');
                                startPolling();
                            } else {
                                console.log('🛑 Waiter requests: Stopping polling due to pollingEnabled change');
                                stopPolling();
                            }
                        } else {
                            console.log('📡 Waiter requests: Pusher is enabled, ignoring polling changes');
                        }
                    });

                    $wire.watch('pollingInterval', (value) => {
                        console.log('👀 Waiter requests pollingInterval changed:', value);
                        if (typeof window.PUSHER_SETTINGS !== 'undefined' && !window.PUSHER_SETTINGS.is_enabled_pusher_broadcast && $wire.get('pollingEnabled')) {
                            console.log('🔄 Waiter requests: Restarting polling due to interval change');
                            startPolling();
                        } else {
                            console.log('📡 Waiter requests: Pusher is enabled or polling disabled, ignoring interval change');
                        }
                    });
                } catch (error) {
                    console.log('⚠️ Component not ready for watchers yet:', error);
                }
            }, 200);
        });

        // Cleanup on component destroy
        document.addEventListener('livewire:initialized', () => {
            return () => {
                stopPolling();
                stopPusher();
            };
        });
    </script>

</div>
