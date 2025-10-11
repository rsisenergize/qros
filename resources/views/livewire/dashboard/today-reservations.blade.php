<div class="relative">
    <a @if(pusherSettings()->is_enabled_pusher_broadcast) wire:poll.15s @endif
    href="{{ route('reservations.index') }}" wire:navigate
    class="hidden lg:inline-flex items-center px-2 py-1 text-sm font-medium text-center text-gray-600 bg-white border-skin-base border rounded-md focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-gray-800 dark:text-gray-300"
    data-tooltip-target="today-reservations-tooltip-toggle"
    >
    <img src="{{ asset('img/reservation.svg') }}" alt="Today Reservations" class="w-5 h-5">
    <span
        class="inline-flex items-center justify-center px-2 py-0.5 ms-2 text-xs font-semibold text-white bg-skin-base rounded-md">
        {{ $count }}
    </span>
</a>
<div id="today-reservations-tooltip-toggle" role="tooltip"
    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
    @lang('modules.reservation.newReservations')
    <div class="tooltip-arrow" data-popper-arrow></div>
</div>
</div>

@push('scripts')

    @if(pusherSettings()->is_enabled_pusher_broadcast)
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const channel = PUSHER.subscribe('today-reservations');
                channel.bind('today-reservations.created', function(data) {
                    @this.call('refreshReservations');
                    console.log('✅ Pusher received data for today reservations!. Refreshing...');
                });
                PUSHER.connection.bind('connected', () => {
                    console.log('✅ Pusher connected for Today Reservations!');
                });
                channel.bind('pusher:subscription_succeeded', () => {
                    console.log('✅ Subscribed to today-reservations channel!');
                });
            });
        </script>
    @endif
@endpush
