<div class="relative">
<a @if(!pusherSettings()->is_enabled_pusher_broadcast) wire:poll.15s @endif href="{{ route('orders.index') }}" wire:navigate
    class="hidden lg:inline-flex items-center px-2 py-1 text-sm font-medium text-center text-gray-600 bg-white border-skin-base border rounded-md focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-gray-800 dark:text-gray-300"
    data-tooltip-target="today-orders-tooltip-toggle"
    >
    <img src="{{ asset('img/checkout.svg') }}" alt="Today Orders" class="w-5 h-5">
    <span
        class="inline-flex items-center justify-center px-2 py-0.5 ms-2 text-xs font-semibold text-white bg-skin-base rounded-md">
        {{ $count }}
    </span>

</a>
<div id="today-orders-tooltip-toggle" role="tooltip"
    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
    @lang('modules.order.todayOrder')
    <div class="tooltip-arrow" data-popper-arrow></div>
</div>
</div>
@push('scripts')

    @if(pusherSettings()->is_enabled_pusher_broadcast)
        @script
            <script>
                document.addEventListener('DOMContentLoaded', function () {

                    const channel = PUSHER.subscribe('today-orders');
                    channel.bind('today-orders.updated', function(data) {
                        @this.call('refreshOrders');
                        new Audio("{{ asset('sound/new_order.wav')}}").play();
                        console.log('✅ Pusher received data for today orders!. Refreshing...');
                    });
                    PUSHER.connection.bind('connected', () => {
                        console.log('✅ Pusher connected for Today Orders!');
                    });
                    channel.bind('pusher:subscription_succeeded', () => {
                        console.log('✅ Subscribed to today-orders channel!');
                    });
                });
            </script>
        @endscript
    @elseif($playSound)
        @script
            <script>
                console.log('✅ Playing sound for today orders!', "{{ asset('sound/new_order.wav')}}");
                new Audio("{{ asset('sound/new_order.wav')}}").play();
            </script>
        @endscript
    @endif
@endpush
