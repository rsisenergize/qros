<div class="relative">
    <a @if(pusherSettings()->is_enabled_pusher_broadcast) wire:poll.15s @else wire:poll.10s @endif
    href="{{ route('waiter-requests.index') }}" wire:navigate
    class="hidden lg:inline-flex items-center px-2 py-1 text-sm font-medium text-center text-gray-600 bg-white border-skin-base border rounded-md focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-gray-800 dark:text-gray-300"
    data-tooltip-target="active-waiter-requests-tooltip-toggle"
    >
    <img src="{{ asset('img/waiter.svg') }}" alt="Active Waiter Requests" class="w-5 h-5">
    <span
        class="inline-flex items-center justify-center px-2 py-0.5 ms-2 text-xs font-semibold text-white bg-skin-base rounded-md">
        {{ $count }}
    </span>
</a>
<div id="active-waiter-requests-tooltip-toggle" role="tooltip"
    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
    @lang('modules.waiterRequest.newWaiterRequests')
    <div class="tooltip-arrow" data-popper-arrow></div>
</div>
</div>

@push('scripts')

    @if ($playSound)
    <script>
        new Audio("{{ asset('sound/new_order.wav')}}").play();
    </script>
    @endif

    @if(pusherSettings()->is_enabled_pusher_broadcast)
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const channel = PUSHER.subscribe('active-waiter-requests');
                channel.bind('active-waiter-requests.created', function(data) {
                    @this.call('refreshActiveWaiterRequests');
                    new Audio("{{ asset('sound/new_order.wav')}}").play();
                    console.log('✅ Pusher received data for active waiter requests!. Refreshing...');
                });
                PUSHER.connection.bind('connected', () => {
                    console.log('✅ Pusher connected for Active Waiter Requests!');
                });
                channel.bind('pusher:subscription_succeeded', () => {
                    console.log('✅ Subscribed to active-waiter-requests channel!');
                });
            });
        </script>
    @endif

    <script>
        // Listen for Livewire events for immediate notifications (works even without Pusher)
        document.addEventListener('livewire:init', () => {
            console.log('🔧 Setting up waiter request event listeners...');
            
            Livewire.on('waiterRequestCreated', (data) => {
                console.log('✅ Livewire event received for waiter request!', data);
                // Play sound immediately for new request
                new Audio("{{ asset('sound/new_order.wav')}}").play();
                // Refresh the component to show new count
                @this.call('refreshActiveWaiterRequests');
            });
            
            // Debug: Test if events are working
            console.log('🔧 Waiter request component event listeners ready!');
        });
        
        // Also listen for events on the document level as backup
        document.addEventListener('livewire:init', () => {
            Livewire.on('waiterRequestCreated', (data) => {
                console.log('🌐 Global Livewire event received for waiter request!', data);
                // This ensures the event is caught even if the component listener fails
            });
        });
    </script>
@endpush
