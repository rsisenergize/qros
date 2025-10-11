<div class="px-4">

    <h2 class="text-2xl font-extrabold dark:text-white">@lang('modules.order.myOrders')</h2>

    <ul role="list" class=" space-y-2 dark:divide-gray-700 mt-4">
        @forelse ($orders as $order)
        <li class="p-3 border rounded-md">
            <a href="{{ route('order_success', $order->uuid) }}"  class="font-medium text-skin-base truncate dark:text-white">
                <div class="flex w-full  space-y-4">

                    <div class="flex w-full items-center justify-between" >
                        <div class="flex items-center">
                            <div>
                                    {{ $order->show_formatted_order_number }}

                                <div class="flex items-center flex-1 text-xs text-gray-500">
                                    {{ $order->items->count() }} @lang('modules.menu.item') | {{ $order->date_time->timezone($restaurant->timezone)->translatedFormat('M d, Y h:i A') }}
                                </div>
                            </div>
                        </div>
                        <div class="inline-flex flex-col text-right text-base font-semibold text-gray-900 dark:text-white">
                            <div>{{ currency_format($order->total, $restaurant->currency_id) }}</div>
                            <div class="text-xs text-gray-500 font-light">@lang('modules.order.includeTax')</div>
                        </div>
                    </div>


                </div>
            </a>
        </li>
        @empty
        <li class="p-8 border rounded-md text-center">
            <div class="flex flex-col items-center justify-center space-y-3">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">@lang('messages.noItemAdded')</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('messages.startShoppingNow')</p>
                <x-primary-link wire:navigate class="inline-flex items-center" href="{{ module_enabled('Subdomain')?url('/'):route('shop_restaurant',['hash' => $restaurant->hash]) }}">
                    @lang('modules.menu.browseMenu')
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </x-primary-link>
            </div>
        </li>
        @endforelse
    </ul>
</div>
