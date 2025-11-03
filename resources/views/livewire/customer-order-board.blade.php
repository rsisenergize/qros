<div class="h-screen w-screen overflow-hidden" @if(!pusherSettings()->is_enabled_pusher_broadcast) wire:poll.2s @endif>
    <div class="grid grid-cols-2 h-full">
        <!-- Preparing (left) -->
        <div class="bg-gray-700 text-white h-full flex flex-col">
            <div class="px-8 pt-8 pb-4">
                <h3 class="text-3xl font-semibold tracking-wide">
                    @lang('modules.order.preparing')
                </h3>
            </div>

            <div class="flex-1 px-8 pb-10">
                <div class="grid grid-cols-3 gap-5 place-content-start">
                    @forelse($preparingOrders as $o)
                        @php($num = $o['token'] ?? $o['display_number'])
                        <div class="rounded-md bg-gray-800 shadow-md">
                            <div class="px-6 py-5 text-center">
                                <div class="text-4xl font-extrabold tracking-wide">{{ $num }}</div>
                                @if(isset($o['order_type']))
                                    <div class="text-sm text-gray-300 mt-2">{{ $o['order_type'] }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center text-gray-300 text-xl mt-10">@lang('modules.order.noOrders')</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Ready (right) -->
        <div class="bg-black text-white h-full flex flex-col">
            <div class="px-8 pt-8 pb-4">
                <h3 class="text-3xl font-semibold tracking-wide">
                    @lang('modules.order.readyForPickup')
                </h3>
            </div>

            <div class="flex-1 px-8 pb-10">
                <div class="grid grid-cols-2 gap-6 place-content-start">
                    @forelse($readyOrders as $o)
                        @php($num = $o['token'] ?? $o['display_number'])
                        <div class="rounded-md bg-green-600 shadow-md">
                            <div class="px-8 py-6 text-center">
                                <div class="text-4xl font-extrabold tracking-wide">{{ $num }}</div>
                                @if(isset($o['order_type']))
                                    <div class="text-sm text-green-100 mt-2">{{ $o['order_type'] }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center text-gray-400 text-xl mt-10">@lang('modules.order.noOrders')</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @if(pusherSettings()->is_enabled_pusher_broadcast)
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const channel = PUSHER.subscribe('orders');
                    channel.bind('order.updated', function() {
                        @this.call('refreshBoard');
                    });
                });
            </script>
        @endif
    @endpush
</div>


