<div class="mx-2 mb-4 space-y-6 md:mx-0" @if(pusherSettings()->is_enabled_pusher_broadcast) wire:poll.15s @endif>
    <x-banner/>
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">@lang('modules.settings.orderDetails')</h2>

        @if ($this->shouldShowWaiterButtonMobile)
            <div class="block md:hidden">
                @livewire('forms.callWaiterButton', ['tableNumber' => $table->id ?? null, 'shopBranch' => $shopBranch])
            </div>
        @endif
    </div>

    <div class="overflow-hidden rounded-lg bg-gray-50 dark:bg-gray-800">
   
        <!-- Order Status Header -->
        <div class="px-4 py-3 border-gray-200 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
            @if ($order->order_status->value === 'cancelled')
                <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                    <div class="p-1.5 bg-red-100 rounded-full dark:bg-red-900/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold">
                            @lang('modules.order.info_cancelled')
                        </h3>
                        <p class="text-xs">
                            @lang('modules.order.orderCancelledMessage')
                        </p>
                    </div>
                </div>
            @else
                @php
                    $steps = match($order->order_type) {
                        'delivery' => ['placed', 'confirmed', 'preparing', 'out_for_delivery', 'delivered'],
                        'pickup' => ['placed', 'confirmed', 'preparing', 'ready_for_pickup', 'delivered'],
                        default => ['placed', 'confirmed', 'preparing', 'served']
                    };
                    $currentStepIndex = array_search($order->order_status->value, $steps);
                @endphp

                <div>
                    <!-- Progress Bar -->
                    <div class="left-0 hidden w-full h-0.5 bg-gray-200 rounded sm:block top-5 mt-5 dark:bg-gray-600">
                        <div class="h-0.5 progress-bar-fill rounded bg-skin-base/80 dark:bg-skin-base/90"
                            style="width: {{ min(100, ($currentStepIndex / (count($steps) - 1)) * 100) }}%"></div>
                    </div>

                    <!-- Status Steps -->
                    <div class="flex flex-col justify-between text-gray-500 sm:flex-row sm:items-center sm:-mt-5 dark:text-gray-400">
                        @foreach($steps as $index => $status)
                            <div @class([
                                'flex items-start gap-2 mb-3 sm:mb-0',
                                'flex-row sm:flex-col',
                                'sm:items-center' => !$loop->first && !$loop->last,
                                'sm:items-start' => $loop->first,
                                'sm:items-end' => $loop->last,
                                'w-full sm:w-auto',
                            ])>
                                <!-- Status Step Icon -->
                                <div @class([
                                    'flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full shadow-sm transition-all duration-300',
                                    'bg-skin-base text-white shadow-lg shadow-skin-base/25' => $index == $currentStepIndex,
                                    'bg-skin-base text-white' => $index < $currentStepIndex,
                                    'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' => $index > $currentStepIndex,
                                    'ring-2 ring-white dark:ring-gray-800' => true
                                ]) data-status-step="{{ $status }}">
                                    @if($index <= $currentStepIndex)
                                        <svg class="w-4 h-4 {{ $index < $currentStepIndex ? 'animate-checkmark' : '' }}" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @else
                                        @php
                                            $svgBase = 'class="w-4 h-4" fill="none" viewBox="0 0 24 24"';
                                            $pathBase = 'stroke="currentColor" stroke-linecap="round" stroke-width="2"';
                                        @endphp
                                        @switch($status)
                                            @case('placed')
                                                <svg {!! $svgBase !!}>
                                                    <path {!! $pathBase !!} d="M9 6a3 3 0 1 1 6 0v0a3 3 0 1 1-6 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                </svg>
                                            @break
                                            @case('confirmed')
                                                <svg class="w-4 h-4" viewBox="0 0 0.72 0.72" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0.51 0.21a0.16 0.16 0 0 0 -0.155 -0.12 0.16 0.16 0 0 0 -0.155 0.12H0.09v0.36a0.06 0.06 0 0 0 0.06 0.06h0.42a0.06 0.06 0 0 0 0.06 -0.06V0.21zm-0.042 0a0.12 0.12 0 0 0 -0.226 0zM0.15 0.27h0.42v0.3H0.15z" fill="currentColor"/><path d="m0.45 0.369 -0.021 -0.021L0.33 0.446 0.291 0.408l-0.021 0.021 0.06 0.06z" fill="currentColor"/></svg>
                                            @break
                                            @case('preparing')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 7.68 7.68" xmlns="http://www.w3.org/2000/svg"><path d="M7.584 3.072 6.72 3.72v1.8a0.961 0.961 0 0 1 -0.96 0.96H1.92a0.961 0.961 0 0 1 -0.96 -0.96v-1.8L0.096 3.072a0.24 0.24 0 0 1 0.288 -0.384L0.96 3.12V2.64a0.481 0.481 0 0 1 0.48 -0.48h4.8a0.481 0.481 0 0 1 0.48 0.48v0.48l0.576 -0.432a0.24 0.24 0 0 1 0.288 0.384M4.8 1.68a0.24 0.24 0 0 0 0.24 -0.24V0.48a0.24 0.24 0 0 0 -0.48 0v0.96a0.24 0.24 0 0 0 0.24 0.24m-0.96 0a0.24 0.24 0 0 0 0.24 -0.24V0.48a0.24 0.24 0 0 0 -0.48 0v0.96a0.24 0.24 0 0 0 0.24 0.24m-0.96 0a0.24 0.24 0 0 0 0.24 -0.24V0.48a0.24 0.24 0 0 0 -0.48 0v0.96a0.24 0.24 0 0 0 0.24 0.24"/></svg>
                                            @break
                                            @case('out_for_delivery')
                                                <svg class="w-4 h-4" fill="currentColor" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" xml:space="preserve"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><path d="M17.6 22c-1.8 0-3.2-1.3-3.5-3H9c-.2 1.7-1.7 3-3.5 3S2.3 20.7 2 19H0V3h16v4h3.4l4.6 4.6V19h-3c-.2 1.7-1.7 3-3.4 3m-1.5-3.5c0 .8.7 1.5 1.5 1.5s1.5-.7 1.5-1.5-.7-1.5-1.5-1.5-1.5.7-1.5 1.5M5.6 17c-.8 0-1.5.7-1.5 1.5S4.8 20 5.6 20s1.5-.7 1.5-1.5S6.4 17 5.6 17m15.1 0H22v-4.6L18.7 9h-2.6v6.3q.75-.3 1.5-.3c1.4 0 2.6.8 3.1 2m-12 0H14V5H2v12h.3c.6-1.2 1.8-2 3.2-2s2.7.8 3.2 2"/></svg>
                                            @break
                                            @case('ready_for_pickup')
                                                <svg {!! $svgBase !!}>
                                                    <path {!! $pathBase !!} d="M9 10a3 3 0 1 1 6 0v0a3 3 0 1 1-6 0m12 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                </svg>
                                            @break
                                            @case('delivered')
                                                <svg {!! $svgBase !!}>
                                                    <path {!! $pathBase !!} d="M20 12a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-3v3l2 2"/>
                                                </svg>
                                            @break
                                            @case('served')
                                                <svg {!! $svgBase !!}>
                                                    <path {!! $pathBase !!} d="M7 17.5h10M7 11v3m10-3v3m-5-3v3m-7.5-8h15c.8 0 1.5.7 1.5 1.5v10c0 .8-.7 1.5-1.5 1.5h-15c-.8 0-1.5-.7-1.5-1.5v-10c0-.8.7-1.5 1.5-1.5Z"/>
                                                </svg>
                                            @break
                                            @default
                                                <svg {!! $svgBase !!}>
                                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                                </svg>
                                        @endswitch
                                    @endif
                                </div>

                                <!-- Mobile only vertical line connecting steps -->
                                @if(!$loop->last)
                                    <div class="top-8 left-4 h-full w-0.5 bg-gray-200 dark:bg-gray-600 hidden">
                                        @if($index < $currentStepIndex)
                                            <div class="h-full transition-all duration-700 ease-out bg-skin-base/60 dark:bg-skin-base/80"></div>
                                        @endif
                                    </div>
                                @endif

                                <div @class([
                                    'flex-1 mt-0 sm:mt-2',
                                    'text-left sm:text-center' => !$loop->first && !$loop->last,
                                    'text-left' => $loop->first || true,
                                    'sm:text-right' => $loop->last,
                                    'pt-1 sm:pt-0',
                                ])>
                                    <p @class([
                                        'text-xs font-semibold transition-all duration-300',
                                        'text-skin-base dark:text-skin-base/90' => $index == $currentStepIndex,
                                        'text-skin-base dark:text-skin-base/90' => $index <= $currentStepIndex,
                                        'text-gray-500 dark:text-gray-400' => $index > $currentStepIndex,
                                    ])>
                                        {{ __('modules.order.' . App\Enums\OrderStatus::from($status)->label()) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="p-3 mb-3 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-800">
        <!-- Order Header -->
        <div class="flex flex-col justify-between gap-3 mb-3 md:flex-row">
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $order->show_formatted_order_number }}
                    </h3>
                </div>

                <!-- Order Meta Information -->
                <div class="flex flex-wrap gap-3 text-xs text-gray-600 dark:text-gray-400">
                    <div class="flex items-center gap-1.5">
                        @php
                            $svgPath = match($order->order_type) {
                                'delivery' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m7-5l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2',
                                'pickup' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                                default => 'M15 11v.01M16 20h-8c-2.76 0-5-2.24-5-5s2.24-5 5-5h8c2.76 0 5 2.24 5 5s-2.24 5-5 5zM3 11h18M12 4v3M8.5 7h7'
                            };
                        @endphp
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $svgPath }}"/>
                        </svg>
                        {{ Str::of($order->order_type)->replace('_', ' ')->title() }}
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $order->date_time->timezone(timezone())->translatedFormat('M d, Y h:i A') }}
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        {{ $order->items->count() }} @lang('modules.menu.item')
                    </div>
                    @if($order->table_id)
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M3 9v8a2 2 0 0 0 2 2h4m-6-10h18M9 19h10a2 2 0 0 0 2-2V9" />
                            </svg>
                            @lang('modules.settings.tableNumber')
                            <span class="px-1.5 py-0.5 bg-skin-base text-gray-100 rounded-full text-xs font-medium">
                                {{ $order->table->table_code ?? '--' }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Amount -->
            <div class="flex items-center gap-1 md:flex-col md:items-end">
                <div class="text-lg font-bold text-gray-900 md:text-xl dark:text-white">
                    {{ currency_format($order->total, $restaurant->currency_id) }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 sm:ms-2">
                    @lang('modules.order.includeTax')
                </div>
            </div>
        </div>

        <!-- Delivery Address -->
        @if($order->order_type === 'delivery' && $order->delivery_address)
            <div class="p-2 mt-3 rounded-lg bg-skin-base/5 dark:bg-gray-700/30" wire:key="delivery-address">
                <div class="flex items-center gap-2">
                    <svg class="flex-shrink-0 w-3.5 h-3.5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zm-2.657-5.657a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        {{ $order->delivery_address }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Delivery ETA or Preparation Time -->
        <div class="p-2 mt-3 rounded-lg bg-skin-base/15">
            <div class="flex items-center gap-2 text-skin-base">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-medium text-skin-base">
                    @php
                        $timeValue = null;
                        $timeLabel = null;

                        if ($order->order_type === 'delivery' && !is_null($order->estimated_eta_max)) {
                            $timeValue = $order->estimated_eta_max;
                            $timeLabel = 'modules.order.estimatedDeliveryTime';
                        } else {
                            $maxPreparationTime = $order->items->max(function($item) {
                                return $item->menuItem->preparation_time;
                            });

                            if ($maxPreparationTime) {
                                $timeValue = $maxPreparationTime;
                                $timeLabel = 'modules.menu.preparationTime';
                            }
                        }
                    @endphp

                    @if ($timeValue)
                        @lang($timeLabel): {{ $timeValue }} @lang('modules.menu.minutes')
                        <span>(@lang('app.approx'))</span>
                    @else
                        @lang('modules.delivery.estimatedTimeUnavailable')
                    @endif
                </span>
            </div>
        </div>
    </div>

        <!-- Order Details Section -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <!-- Order Items -->
            <div class="border-b border-gray-200 divide-y divide-gray-200 dark:border-gray-700 dark:divide-gray-700">
                @foreach ($order->items as $item)
                    <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <div class="flex items-center gap-3">
                            <!-- Item Image -->
                            @if ($restaurant && !$restaurant->hide_menu_item_image_on_customer_site)
                                <div class="flex items-center justify-center flex-shrink-0">
                                    <img class="object-cover w-12 h-12 rounded-lg shadow-sm ring-1 ring-gray-200 dark:ring-gray-700"
                                        src="{{ $item->menuItem->item_photo_url }}"
                                        alt="{{ $item->menuItem->item_name }}" />
                                </div>
                            @endif

                            <!-- Item Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <div class="inline-flex items-center text-sm font-semibold text-gray-900 dark:text-white">
                                                <img src="{{ asset('img/' . $item->menuItem->type . '.svg') }}" class="h-3.5 mr-1.5"
                                                    title="@lang('modules.menu.' . $item->menuItem->type)" alt="" />
                                                {{ $item->menuItem->item_name }}
                                            </div>

                                            @if(isset($item->menuItemVariation))
                                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-sm text-xs font-sm">
                                                    {{ $item->menuItemVariation->variation }}
                                                </span>
                                            @endif
                                        </div>



                                        <div class="flex flex-wrap items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                            @if($taxMode === 'item' && $restaurant?->tax_inclusive && $item->tax_amount > 0)
                                                <span class="font-medium">
                                                    {{ currency_format(($item->price + $item->modifierOptions->sum('price')) - ($item->tax_amount / $item->quantity), $restaurant->currency_id) }}
                                                </span>
                                                <span class="text-gray-400 dark:text-gray-500">+ tax</span>
                                                <span class="text-gray-400 dark:text-gray-500">=</span>
                                            @endif
                                            <span class="font-medium">
                                                {{ currency_format($item->price + $item->modifierOptions->sum('price'), $restaurant->currency_id) }}
                                            </span>
                                            <span class="text-gray-400 dark:text-gray-500">×</span>
                                            <span class="px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded-full text-xs font-medium">
                                                {{ $item->quantity }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="space-y-1 text-xs">

                                        @if($order->kot->isNotEmpty())
                                            @php
                                                $kotItem = $order->kot->flatMap->items->where('menu_item_id', $item->menu_item_id)
                                                    ->where('menu_item_variation_id', $item->menu_item_variation_id)
                                                    ->first();
                                                $status = $kotItem ? $kotItem->status : null;
                                            @endphp
                                            @if($status)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    @if($status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300
                                                    @elseif($status === 'cooking') bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300
                                                    @elseif($status === 'ready') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300
                                                    @endif">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ currency_format(($item->price + $item->modifierOptions->sum('price')) * $item->quantity, $restaurant->currency_id) }}
                                    </span>
                                </div>

                                @if($item->modifierOptions->isNotEmpty())
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        @foreach ($item->modifierOptions as $modifier)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-skin-base/10 text-skin-base">
                                                {{ $modifier->name }}
                                                <span class="ml-1 text-skin-base">
                                                    ({{ currency_format($modifier->price, $restaurant->currency_id) }})
                                                </span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                @if($item->note)
                                    <div class="mt-1 text-xs text-gray-800 dark:text-gray-200 line-clamp-2">
                                        <span class="font-medium">@lang('modules.order.note'):</span> {{ $item->note }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="p-3 rounded-b-lg bg-gray-50 dark:bg-gray-700/50">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">@lang('modules.order.subTotal')</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ currency_format($order->sub_total, $restaurant->currency_id) }}
                        </span>
                    </div>

                    @if (!is_null($order->discount_amount))
                        <div wire:key="discountAmount"
                            class="flex justify-between text-xs text-green-500 dark:text-green-400">
                            <div>
                                @lang('modules.order.discount') @if ($order->discount_type == 'percent')
                                    ({{ rtrim(rtrim(number_format($order->discount_value, 2), '0'), '.') }}%)
                                @endif
                            </div>
                            <div>
                                -{{ currency_format($order->discount_amount, $restaurant->currency_id) }}
                            </div>
                        </div>
                    @endif


                    @foreach ($order->charges as $item)
                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                            <div class="inline-flex items-center gap-x-1">
                                {{ $item->charge->charge_name }}
                                @if ($item->charge->charge_type == 'percent')
                                    ({{ $item->charge->charge_value }}%)
                                @endif
                            </div>
                            <div class="text-gray-900 dark:text-white">
                                {{ currency_format(($item->charge->getAmount($order->sub_total - ($order->discount_amount ?? 0))) , $restaurant->currency_id) }}
                            </div>
                        </div>
                    @endforeach

                    @if ($taxMode == 'order')
                        @foreach ($order->taxes as $item)
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-600 dark:text-gray-400">
                                    {{ $item->tax->tax_name }} ({{ $item->tax->tax_percent }}%)
                                </span>
                                <span class="text-gray-900 dark:text-white">
                                    {{ currency_format(($item->tax->tax_percent / 100) * ($order->sub_total - ($order->discount_amount ?? 0)), $restaurant->currency_id) }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        @if($order->total_tax_amount > 0)
                        @php
                            $taxTotals = [];
                            $totalTax = 0;
                            foreach ($order->items as $item) {
                                $qty = $item->quantity ?? 1;
                                $taxBreakdown = is_array($item->tax_breakup) ? $item->tax_breakup : (json_decode($item->tax_breakup, true) ?? []);
                                foreach ($taxBreakdown as $taxName => $taxInfo) {
                                    if (!isset($taxTotals[$taxName])) {
                                        $taxTotals[$taxName] = [
                                            'percent' => $taxInfo['percent'] ?? 0,
                                            'amount' => ($taxInfo['amount'] ?? 0) * $qty
                                        ];
                                    } else {
                                        $taxTotals[$taxName]['amount'] += ($taxInfo['amount'] ?? 0) * $qty;
                                    }
                                }
                                $totalTax += $item->tax_amount ?? 0;
                            }
                        @endphp
                        <div class="mt-2">
                            <div class="space-y-1">
                                @foreach ($taxTotals as $taxName => $taxInfo)
                                <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500">
                                    <span>{{ $taxName }} ({{ $taxInfo['percent'] }}%)</span>
                                    <span>{{ currency_format($taxInfo['amount'], $restaurant->currency_id) }}</span>
                                </div>
                                @endforeach
                            </div>
                            <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                                <div>
                                    @lang('modules.order.totalTax')
                                    <span
                                        class="ml-2 px-2 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300">
                                        @lang($restaurant?->tax_inclusive ? 'modules.settings.taxInclusive' : 'modules.settings.taxExclusive')
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-900 dark:text-white">{{ currency_format($totalTax, $restaurant->currency_id) }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif

                    @if ($order->order_type === 'delivery' && !is_null($order->delivery_fee))
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                            <div>
                                @lang('modules.delivery.deliveryFee')
                            </div>
                            <div>
                                @if($order->delivery_fee > 0)
                                    <span class="text-gray-900 dark:text-white">
                                        {{ currency_format($order->delivery_fee, $restaurant->currency_id) }}
                                    </span>
                                @else
                                    <span class="font-medium text-green-500 dark:text-green-400">@lang('modules.delivery.freeDelivery')</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($canAddTip || $order->tip_amount > 0)
                        <div class="border-t border-gray-200 dark:border-gray-700" wire:key="tip-details-and-amounts">
                            <div class="flex items-center justify-between gap-3 mt-2">
                                <!-- Left Section: Icon and Text -->
                                <div class="flex items-center gap-2">
                                    <div class="p-1.5 rounded-lg bg-skin-base/10">
                                        <svg class="w-3.5 h-3.5 text-skin-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>

                                    <div>
                                        <span class="text-xs font-medium text-gray-900 dark:text-white">
                                            @lang('modules.order.tip')
                                        </span>
                                        @if($order->tip_amount > 0 && $order->tip_note)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">
                                                "{{ $order->tip_note }}"
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Right Section: Amount and/or Action Button -->
                                <div class="flex items-center gap-2">
                                    @if($order->tip_amount > 0 && !$canAddTip)
                                        <span class="text-xs font-medium dark:text-white">{{ currency_format($order->tip_amount, $restaurant->currency_id) }}</span>
                                    @endif

                                    @if($canAddTip)
                                        <x-button wire:click="addTipModal" class="flex items-center text-xs">
                                            @if($order->tip_amount > 0)
                                                <span class="font-medium">{{ currency_format($order->tip_amount, $restaurant->currency_id) }}</span>
                                                <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            @else
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                <span>@lang('modules.order.addTip')</span>
                                            @endif
                                        </x-button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="pt-2 mt-2 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <span class="text-base font-semibold text-gray-900 dark:text-white">
                                @lang('modules.order.total')
                            </span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ currency_format($order->total, $restaurant->currency_id) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information Section -->
        <div class="p-3 my-3 shadow-sm bg-gray-50 dark:bg-gray-800 rounded-xl">
            <!-- Section Header -->
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2m2 4h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2m7-5a2 2 0 1 1-4 0 2 2 0 0 1 4 0"/></svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    @lang('modules.order.paymentInformation')
                </h3>
            </div>

            @php
                $isSubdomainEnabled = function_exists('module_enabled') && module_enabled('Subdomain');
                if ($order->order_type === 'delivery' || in_array($order->status, ['paid', 'pending_verification', 'canceled', 'delivered'])) {
                    $newOrderLink = $order->table_id
                        ? route('table_order', [$order->table->hash])
                        : ($isSubdomainEnabled ? url('/') : route('shop_restaurant', ['hash' => $restaurant->hash]));
                } else {
                    $newOrderLink = ($isSubdomainEnabled ? url('/') : route('shop_restaurant', ['hash' => $restaurant->hash])) . '?current_order=' . $order->id;
                }
            @endphp

            <!-- Payment History -->
            @if($order->payments->count())
                <div class="mb-3 space-y-3">
                    @foreach($order->payments as $payment)
                        <div class="p-3 transition-shadow bg-white border border-gray-200 rounded-lg dark:bg-gray-700/50 dark:border-gray-600 hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <!-- Payment Method Details -->
                                <div class="flex items-start gap-2">
                                    <!-- Payment Icon based on method -->
                                    <div class="p-1.5 bg-gray-100 rounded-full dark:bg-gray-500">
                                        @switch($payment->payment_method)
                                            @case('stripe')
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="24" height="24" fill="#6772e5"><path d="M111.328 15.602c0-4.97-2.415-8.9-7.013-8.9s-7.423 3.924-7.423 8.863c0 5.85 3.32 8.8 8.036 8.8 2.318 0 4.06-.528 5.377-1.26V19.22a10.25 10.25 0 0 1-4.764 1.075c-1.9 0-3.556-.67-3.774-2.943h9.497a40 40 0 0 0 .063-1.748zm-9.606-1.835c0-2.186 1.35-3.1 2.56-3.1s2.454.906 2.454 3.1zM89.4 6.712a5.43 5.43 0 0 0-3.801 1.509l-.254-1.208h-4.27v22.64l4.85-1.032v-5.488a5.43 5.43 0 0 0 3.444 1.265c3.472 0 6.64-2.792 6.64-8.957.003-5.66-3.206-8.73-6.614-8.73zM88.23 20.1a2.9 2.9 0 0 1-2.288-.906l-.03-7.2a2.93 2.93 0 0 1 2.315-.96c1.775 0 2.998 2 2.998 4.528.003 2.593-1.198 4.546-2.995 4.546zM79.25.57l-4.87 1.035v3.95l4.87-1.032z" fill-rule="evenodd"/><path d="M74.38 7.035h4.87V24.04h-4.87z"/><path d="m69.164 8.47-.302-1.434h-4.196V24.04h4.848V12.5c1.147-1.5 3.082-1.208 3.698-1.017V7.038c-.646-.232-2.913-.658-4.048 1.43zm-9.73-5.646L54.698 3.83l-.02 15.562c0 2.87 2.158 4.993 5.038 4.993 1.585 0 2.756-.302 3.405-.643v-3.95c-.622.248-3.683 1.138-3.683-1.72v-6.9h3.683V7.035h-3.683zM46.3 11.97c0-.758.63-1.05 1.648-1.05a10.9 10.9 0 0 1 4.83 1.25V7.6a12.8 12.8 0 0 0-4.83-.888c-3.924 0-6.557 2.056-6.557 5.488 0 5.37 7.375 4.498 7.375 6.813 0 .906-.78 1.186-1.863 1.186-1.606 0-3.68-.664-5.307-1.55v4.63a13.5 13.5 0 0 0 5.307 1.117c4.033 0 6.813-1.992 6.813-5.485-.1-5.796-7.417-4.76-7.417-6.943zM13.88 9.515c0-1.37 1.14-1.9 2.982-1.9A19.66 19.66 0 0 1 25.6 9.876v-8.27A23.2 23.2 0 0 0 16.862.001C9.762.001 5 3.72 5 9.93c0 9.716 13.342 8.138 13.342 12.326 0 1.638-1.4 2.146-3.37 2.146-2.905 0-6.657-1.202-9.6-2.802v8.378A24.4 24.4 0 0 0 14.973 32C22.27 32 27.3 28.395 27.3 22.077c0-10.486-13.42-8.613-13.42-12.56z" fill-rule="evenodd"/></svg>
                                            @break
                                            @case('razorpay')
                                            <svg class="w-5 h-5" width="24" height="24" viewBox="0 0 24 24"><defs><linearGradient id="a" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#0d3e8e"/><stop offset="100%" stop-color="#00c3f3"/></linearGradient></defs><path fill="url(#a)" d="m22.436 0-11.91 7.773-1.174 4.276 6.625-4.297L11.65 24h4.391z"/><path fill="#0D3E8E" d="M14.26 10.098 3.389 17.166 1.564 24h9.008z"/></svg>
                                            @break
                                             @case('paystack')
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#0AA5FF">
                                                <path d="M2 3.6c0-.331.269-.6.6-.6H21.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6V3.6Zm0 4.8c0-.331.269-.6.6-.6H15.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6V8.4Zm0 4.8c0-.331.269-.6.6-.6H21.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6v-1.8Zm0 4.8c0-.331.269-.6.6-.6H15.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6v-1.8Z" fill-rule="evenodd"/>
                                            </svg>
                                             @break
                                            @case('paypal')
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" aria-label="PayPal" viewBox="0 0 512 512" width="64" height="64"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><path fill="#002C8A" d="M377 184.8 180.7 399h-72c-5 0-9-5-8-10l48-304c1-7 7-12 14-12h122c84 3 107 46 92 112z"/><path fill="#009BE1" d="M380.2 165c30 16 37 46 27 86-13 59-52 84-109 85l-16 1c-6 0-10 4-11 10l-13 79c-1 7-7 12-14 12h-60c-5 0-9-5-8-10l22-143c1-5 182-120 182-120"/><path fill="#001F6B" d="m197 292 20-127a14 14 0 0 1 13-11h96c23 0 40 4 54 11-5 44-26 115-128 117h-44c-5 0-10 4-11 10"/></svg>
                                            @break
                                            @case('payfast')
                                            <svg class="w-5 h-5" width="24" height="24" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" fill="none"><g fill="#E63946"><ellipse cx="32" cy="12" rx="20" ry="8"/><path d="M12 12v4c0 4.42 8.95 8 20 8s20-3.58 20-8v-4c0 4.42-8.95 8-20 8s-20-3.58-20-8m0 12v4c0 4.42 8.95 8 20 8s20-3.58 20-8v-4c0 4.42-8.95 8-20 8s-20-3.58-20-8m0 12v4c0 4.42 8.95 8 20 8s20-3.58 20-8v-4c0 4.42-8.95 8-20 8s-20-3.58-20-8"/></g></svg>
                                            @break
                                            @case('flutterwave')
                                                <svg class="w-5 h-5" width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 176 144.7" xml:space="preserve"><path d="M0 31.6c0-9.4 2.7-17.4 8.5-23.1l10 10C7.4 29.6 17.1 64.1 48.8 95.8s66.2 41.4 77.3 30.3l10 10c-18.8 18.8-61.5 5.4-97.3-30.3C14 80.9 0 52.8 0 31.6" style="fill:#009a46"/><path d="M63.1 144.7c-9.4 0-17.4-2.7-23.1-8.5l10-10c11.1 11.1 45.6 1.4 77.3-30.3s41.4-66.2 30.3-77.3l10-10c18.8 18.8 5.4 61.5-30.3 97.3-24.9 24.8-53.1 38.8-74.2 38.8" style="fill:#ff5805"/><path d="M140.5 91.6C134.4 74.1 122 55.4 105.6 39 69.8 3.2 27.1-10.1 8.3 8.6 7 10 8.2 13.3 10.9 16s6.1 3.9 7.4 2.6c11.1-11.1 45.6-1.4 77.3 30.3 15 15 26.2 31.8 31.6 47.3 4.7 13.6 4.3 24.6-1.2 30.1-1.3 1.3-.2 4.6 2.6 7.4s6.1 3.9 7.4 2.6c9.6-9.7 11.2-25.6 4.5-44.7" style="fill:#f5afcb"/><path d="M167.5 8.6C157.9-1 142-2.6 122.9 4c-17.5 6.1-36.2 18.5-52.6 34.9-35.8 35.8-49.1 78.5-30.3 97.3 1.3 1.3 4.7.2 7.4-2.6s3.9-6.1 2.6-7.4c-11.1-11.1-1.4-45.6 30.3-77.3 15-15 31.8-26.2 47.2-31.6 13.6-4.7 24.6-4.3 30.1 1.2 1.3 1.3 4.6.2 7.4-2.6s3.9-5.9 2.5-7.3" style="fill:#ff9b00"/></svg>
                                            @break
                                            @case('upi')
                                                <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4zm10 10h6v6h-6zm0-10h6v6h-6zm-4 10h.01v.01H10zm0 4h.01v.01H10zm-3 2h.01v.01H7zm0-4h.01v.01H7zm-3 2h.01v.01H4zm0-4h.01v.01H4z"/><path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M7 7h.01v.01H7zm10 10h.01v.01H17z"/></svg>
                                            @break
                                            @default
                                            <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2m2 4h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2m7-5a2 2 0 1 1-4 0 2 2 0 0 1 4 0"/></svg>
                                        @endswitch
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            @lang('modules.order.' . $payment->payment_method)
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $payment->created_at->timezone(timezone())->translatedFormat('M d, Y h:i A') }}
                                        </p>
                                        @if($payment->transaction_id)
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                <span class="font-medium">@lang('modules.order.transactionId'):</span>
                                                <span class="font-mono">{{ $payment->transaction_id }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Payment Amount -->
                                <div class="text-right">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ currency_format($payment->amount, $restaurant->currency_id) }}
                                    </span>
                                    @if($payment->balance > 0)
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            @lang('modules.order.balanceReturn'):
                                            <span class="font-medium">
                                                {{ currency_format($payment->balance, $restaurant->currency_id) }}
                                            </span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Payment Status Banner -->
            @if($order->status == 'paid')
                <x-alert type="success">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 12 2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0"/></svg>
                    @lang('modules.order.paid')
                </x-alert>

                <x-secondary-link wire:navigate class="inline-flex items-center justify-center w-1/2 gap-2" href="{{ $newOrderLink }}">
                    @lang('modules.order.newOrder')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </x-secondary-link>
            @else
                <x-alert type="warning">
                    <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3"/></svg>
                    @if ($order->status === 'pending_verification')
                        @lang('modules.order.pendingPaymentVerification')
                    @else
                        @lang('modules.order.paymentPending')
                    @endif
                </x-alert>

                <!-- Action Buttons -->
                <div class="flex gap-4">
                    @if (is_null($customer) && ($restaurant->customer_login_required || $orderType == 'delivery'))
                        <x-button class="justify-center w-full" wire:click="$dispatch('showSignup')">
                            @lang('app.next')
                        </x-button>
                    @else
                        <div class="grid w-full grid-cols-2 gap-4">
                            @if ($paymentGateway->is_qr_payment_enabled || $paymentGateway->stripe_status || $paymentGateway->razorpay_status || $paymentGateway->is_offline_payment_enabled || $paymentGateway->flutterwave_status || $paymentGateway->paypal_status)
                                @if ($order && $order->order_status->value !== 'cancelled' && $order->status !== 'pending_verification')
                                    <x-button class="items-center w-full" wire:click="InitializePayment" wire:loading.attr="disabled">
                                        <span class="flex items-center justify-center gap-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                            </svg>
                                            @lang('modules.order.payNow')
                                        </span>
                                    </x-button>
                                @endif

                            @endif
                            <x-secondary-link wire:navigate class="inline-flex items-center justify-center gap-2" href="{{ $newOrderLink }}">
                                @lang('modules.order.newOrder')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </x-secondary-link>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <x-dialog-modal wire:model.live="showPaymentModal" maxWidth="md">
            <x-slot name="title">
                @lang('modules.order.chooseGateway')
            </x-slot>

            <x-slot name="content">
                <div class="flex items-center justify-between p-2 mb-4 rounded-md cursor-pointer bg-gray-50 dark:bg-gray-800">
                    <div class="flex items-center min-w-0">
                        <div>
                            <div class="font-medium text-gray-700 truncate dark:text-white">
                                    {{ $paymentOrder->show_formatted_order_number }}
                            </div>
                        </div>
                    </div>
                    <div class="inline-flex flex-col text-base font-semibold text-right text-gray-900 dark:text-white">
                        <div>{{ currency_format($total, $restaurant->currency_id) }}</div>
                    </div>
                </div>

                @if ($showQrCode || $showPaymentDetail)
                    <x-secondary-button wire:click="{{ $showQrCode ? 'toggleQrCode' : 'togglePaymentDetail' }}">
                        <span class="ml-2">@lang('modules.billing.showOtherPaymentOption')</span>
                    </x-secondary-button>

                    <div class="flex items-center mt-2">
                        @if ($showQrCode)
                            <img src="{{ $paymentGateway->qr_code_image_url }}" alt="QR Code Preview"
                                class="object-cover rounded-md h-30 w-30">
                        @else
                            <span class="p-3 font-bold text-gray-700 rounded dark:text-gray-400">@lang('modules.billing.accountDetails')</span>
                            <span>{{ $paymentGateway->offline_payment_detail }}</span>
                        @endif
                    </div>
                @else
                    <div class="grid items-center w-full grid-cols-1 gap-4 mt-4 md:grid-cols-2">
                        @if ($paymentGateway->stripe_status)

                                <x-secondary-button wire:click='initiateStripePayment({{ $paymentOrder->id }})'>
                                    <span class="inline-flex items-center">
                                        <svg height="21" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 468 222.5" xml:space="preserve"><path d="M414 113.4c0-25.6-12.4-45.8-36.1-45.8-23.8 0-38.2 20.2-38.2 45.6 0 30.1 17 45.3 41.4 45.3 11.9 0 20.9-2.7 27.7-6.5v-20c-6.8 3.4-14.6 5.5-24.5 5.5-9.7 0-18.3-3.4-19.4-15.2h48.9c0-1.3.2-6.5.2-8.9m-49.4-9.5c0-11.3 6.9-16 13.2-16 6.1 0 12.6 4.7 12.6 16zm-63.5-36.3c-9.8 0-16.1 4.6-19.6 7.8l-1.3-6.2h-22v116.6l25-5.3.1-28.3c3.6 2.6 8.9 6.3 17.7 6.3 17.9 0 34.2-14.4 34.2-46.1-.1-29-16.6-44.8-34.1-44.8m-6 68.9c-5.9 0-9.4-2.1-11.8-4.7l-.1-37.1c2.6-2.9 6.2-4.9 11.9-4.9 9.1 0 15.4 10.2 15.4 23.3 0 13.4-6.2 23.4-15.4 23.4m-71.3-74.8 25.1-5.4V36l-25.1 5.3zm0 7.6h25.1v87.5h-25.1zm-26.9 7.4-1.6-7.4h-21.6v87.5h25V97.5c5.9-7.7 15.9-6.3 19-5.2v-23c-3.2-1.2-14.9-3.4-20.8 7.4m-50-29.1-24.4 5.2-.1 80.1c0 14.8 11.1 25.7 25.9 25.7 8.2 0 14.2-1.5 17.5-3.3V135c-3.2 1.3-19 5.9-19-8.9V90.6h19V69.3h-19zM79.3 94.7c0-3.9 3.2-5.4 8.5-5.4 7.6 0 17.2 2.3 24.8 6.4V72.2c-8.3-3.3-16.5-4.6-24.8-4.6C67.5 67.6 54 78.2 54 95.9c0 27.6 38 23.2 38 35.1 0 4.6-4 6.1-9.6 6.1-8.3 0-18.9-3.4-27.3-8v23.8c9.3 4 18.7 5.7 27.3 5.7 20.8 0 35.1-10.3 35.1-28.2-.1-29.8-38.2-24.5-38.2-35.7" style="fill-rule:evenodd;clip-rule:evenodd;fill:#635bff"/></svg>
                                    </span>
                                </x-secondary-button>
                            @endif

                            @if ($paymentGateway->razorpay_status)
                                <x-secondary-button wire:click='initiatePayment({{ $paymentOrder->id }})'>
                                    <span class="inline-flex items-center">
                                        <svg height="21" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 26.53" xml:space="preserve">
                                            <path d="M11.19 9.03 7.94 21.47H0l1.61-6.12zm16.9-3.95c1.86.01 3.17.42 3.91 1.25s.92 2.01.51 3.56a6.1 6.1 0 0 1-1.59 2.8c-.8.8-1.78 1.38-2.87 1.68.83.19 1.34.78 1.5 1.79l.03.22.6 5.09h-3.7l-.62-5.48a1.2 1.2 0 0 0-.15-.52c-.09-.16-.22-.29-.37-.39a2.3 2.3 0 0 0-1-.25h-2.49l-1.74 6.63h-3.46l4.3-16.38zm94.79 4.29-4.4 6.34-5.19 7.52-.04.04-1.16 1.68-.04.06-.05.08-1 1.44h-3.44l4.02-5.67-1.82-11.09h3.57l.9 7.23 4.36-6.19.06-.09.07-.1.07-.09.54-1.15h3.55zm-30.48.88a3.68 3.68 0 0 1 1.24 2.19c.18 1.07.1 2.18-.21 3.22a9.5 9.5 0 0 1-1.46 3.19 7.15 7.15 0 0 1-2.35 2.13c-.88.48-1.85.73-2.85.73a3.67 3.67 0 0 1-2.02-.51c-.47-.28-.83-.71-1.03-1.22l-.06-.2-1.77 6.75h-3.43l3.51-13.4.02-.06.01-.06.86-3.25h3.35l-.57 1.88-.01.08c.49-.7 1.15-1.27 1.91-1.64.76-.4 1.6-.6 2.45-.6.85-.05 1.71.23 2.41.77m-4.14 1.86a3 3 0 0 0-2.18.88c-.68.7-1.15 1.59-1.36 2.54-.3 1.11-.28 1.95.02 2.53s.87.88 1.72.88c.81.02 1.59-.29 2.18-.86.66-.69 1.12-1.55 1.33-2.49.29-1.09.27-1.96-.03-2.57s-.86-.91-1.68-.91m15.4-2.12c.46.29.82.72 1.02 1.23l.07.19.44-1.66h3.36l-3.08 11.7h-3.37l.45-1.73c-.51.61-1.15 1.09-1.87 1.42-.7.32-1.45.49-2.21.49-.88.04-1.76-.21-2.48-.74-.66-.52-1.1-1.28-1.24-2.11a6.94 6.94 0 0 1 .19-3.17 9.8 9.8 0 0 1 1.49-3.21c.63-.89 1.44-1.64 2.38-2.18.86-.5 1.84-.77 2.83-.77.72-.02 1.42.16 2.02.54m-1.74 2.15c-.41 0-.82.08-1.19.24-.38.16-.72.39-1.01.68-.67.71-1.15 1.59-1.36 2.55-.3 1.08-.28 1.9.04 2.49.31.59.89.87 1.75.87.4.01.8-.07 1.18-.22s.71-.38 1-.66a5.4 5.4 0 0 0 1.26-2.22l.08-.31c.3-1.11.29-1.96-.03-2.53-.31-.59-.88-.89-1.72-.89M81.13 9.63l.22.09-.86 3.19c-.49-.26-1.03-.39-1.57-.39-.82-.03-1.62.24-2.27.75-.56.48-.97 1.12-1.18 1.82l-.07.27-1.6 6.11h-3.42l3.1-11.7h3.37l-.44 1.72c.42-.58.96-1.05 1.57-1.4.68-.39 1.44-.59 2.22-.59.31-.02.63.02.93.13m-12.63.56c.76.48 1.31 1.24 1.52 2.12.25 1.06.21 2.18-.11 3.22-.3 1.18-.83 2.28-1.58 3.22-.71.91-1.61 1.63-2.64 2.12a7.75 7.75 0 0 1-3.35.73c-1.22 0-2.22-.24-3-.73a3.5 3.5 0 0 1-1.54-2.12 6.4 6.4 0 0 1 .11-3.22c.3-1.17.83-2.27 1.58-3.22.71-.9 1.62-1.63 2.66-2.12a7.8 7.8 0 0 1 3.39-.73 5.4 5.4 0 0 1 2.96.73m-3.66 1.91c-.81-.01-1.59.3-2.18.86-.61.58-1.07 1.43-1.36 2.57-.6 2.29-.02 3.43 1.74 3.43.8.02 1.57-.29 2.15-.85.6-.57 1.04-1.43 1.34-2.58.3-1.13.31-1.98.01-2.57-.29-.59-.86-.86-1.7-.86m-6.95-2.34-.6 2.32-7.55 6.67h6.06l-.72 2.73H45.05l.63-2.41 7.43-6.57h-5.65l.72-2.73h9.71zm-16.93.23c.46.29.82.72 1.02 1.23l.07.19.44-1.66h3.37l-3.07 11.7h-3.37l.45-1.73c-.51.6-1.14 1.08-1.85 1.41s-1.48.5-2.27.5a3.84 3.84 0 0 1-2.45-.74c-.66-.52-1.1-1.28-1.24-2.11a6.94 6.94 0 0 1 .19-3.17 9.6 9.6 0 0 1 1.49-3.21c.63-.89 1.44-1.64 2.37-2.18.86-.5 1.84-.76 2.83-.76.72-.02 1.42.16 2.02.53m-1.73 2.15c-.41 0-.81.08-1.19.24s-.72.39-1.01.68a5.33 5.33 0 0 0-1.36 2.55c-.28 1.08-.27 1.9.04 2.49s.89.87 1.75.87a3 3 0 0 0 2.18-.88 5.2 5.2 0 0 0 1.26-2.22l.08-.31c.29-1.11.26-1.94-.03-2.53-.31-.59-.89-.89-1.72-.89M26.85 7.81h-3.21l-1.13 4.28h3.21c1.01 0 1.81-.17 2.35-.52.57-.37.98-.95 1.13-1.63.2-.72.11-1.27-.27-1.62-.38-.33-1.07-.51-2.08-.51" style="fill:#072654"/>
                                            <path style="fill:#3395ff" d="m18.4 0-5.64 21.47H8.89L12.7 6.93l-5.84 3.85L7.9 6.95z"/>
                                        </svg>
                                    </span>
                                </x-secondary-button>
                            @endif

                            @if ($paymentGateway->flutterwave_status)
                            <x-secondary-button wire:click="initiateFlutterwavePayment({{ $paymentOrder->id }})">
                                <span class="inline-flex items-center">
                                    <svg class="h-5 dark:mix-blend-plus-lighter" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 916.7 144.7"><path d="M280.5 33.8h16.1v82.9h-16.1zM359 87.3c0 11.4-7.4 16.6-17.2 16.6s-16.4-5.1-16.4-16V58.3h-16.1v33.3c0 16.6 10.4 26.3 27.7 26.3 10.9 0 16.9-4 21-8.5h.9l1.4 7.4h14.8V58.3H359zm158 17.9c-11.8 0-18.4-5.4-19.5-13.2h51.1c.2-1.6.4-3.3.3-4.9-.1-21-16-29.9-33-29.9-19.7 0-34.6 11.8-34.6 30.8 0 18.1 14.2 29.9 35.6 29.9 17.9 0 29.8-7.9 32.2-20.1h-15.9c-1.8 4.8-7.5 7.4-16.2 7.4m-1-35.3c10.3 0 16.2 4.6 17.2 11h-35.3c1.5-6.2 7.5-11 18.1-11m60.4-3.2h-1l-1.5-8.4h-14.6v58.4h16.1V91.6c0-11.3 6.5-17.6 18.7-17.6q3.3 0 6.6.6V58.3h-2.2c-10.9 0-17.5 2.3-22.1 8.4m103.3 31.8h-.9L665 62h-16.6l-13.5 36.4h-1.1L621 58.3h-16l19.7 58.4h17.5l14-37.2h1l13.8 37.2h17.6l19.7-58.4h-16zm92.7 1.2V80.2c0-15.9-13.4-23-30.1-23-17.7 0-28.8 8.4-30.3 21h16.1c1.2-5.5 5.8-8.5 14.2-8.5s14 3.2 14 9.6v1.5l-26.3 2c-12.1.9-21 6.3-21 17.8 0 11.8 10.2 17.4 25.1 17.4 12.1 0 19.4-3.4 23.9-8.4h.8c2.5 5.7 7.7 7.3 13.2 7.3h6.8V105h-1.5c-3.3-.2-4.9-1.8-4.9-5.3m-16.1-6.2c0 9.2-11 12.3-20.4 12.3-6.4 0-10.6-1.6-10.6-6.1 0-4 3.6-5.9 9-6.4l22.1-1.6zM832 58.3l-18.8 42.3h-1l-19.1-42.3h-17.4l27.2 58.4h19.3l27.1-58.4zm68.8 39.5c-2 4.8-7.7 7.4-16.3 7.4-11.8 0-18.4-5.4-19.5-13.2h51.1c.2-1.6.4-3.3.3-4.9-.1-21-16-29.9-33-29.9-19.7 0-34.5 11.8-34.5 30.8 0 18.1 14.2 29.9 35.6 29.9 17.9 0 29.8-7.9 32.2-20.1zm-17.4-27.9c10.3 0 16.2 4.6 17.2 11h-35.3c1.5-6.2 7.4-11 18.1-11M254.4 54c0-5.1 3.6-7.3 8.3-7.3 2.2 0 4.3.3 6.4.9l2.7-11.7c-3.9-1.4-8-2.1-12.1-2.1-11.9 0-21.5 6.3-21.5 19.4v5.1h-13.9v12.8h13.9v45.6h16.2V71.1h18.2V58.3h-18.2zm156.4-12.1h-15l-.8 16.5h-12.7v12.8h12.4V100c0 9.8 5 18 20 18 3.9 0 7.8-.4 11.6-1.3v-12.3c-2.2.5-4.4.8-6.7.8-8 0-8.8-4.6-8.8-8.1v-26h16V58.3h-16zm50.6 0h-14.9l-.8 16.5H433v12.8h12.4V100c0 9.8 5 18 20 18 3.9 0 7.7-.5 11.5-1.3v-12.3c-2.2.5-4.4.8-6.7.8-8 0-8.8-4.6-8.8-8.1v-26h16V58.3h-16.1V41.9z" style="fill:#2a3362"/><path d="M0 31.6c0-9.4 2.7-17.4 8.5-23.1l10 10C7.4 29.6 17.1 64.1 48.8 95.8s66.2 41.4 77.3 30.3l10 10c-18.8 18.8-61.5 5.4-97.3-30.3C14 80.9 0 52.8 0 31.6" style="fill:#009a46"/><path d="M63.1 144.7c-9.4 0-17.4-2.7-23.1-8.5l10-10c11.1 11.1 45.6 1.4 77.3-30.3s41.4-66.2 30.3-77.3l10-10c18.8 18.8 5.4 61.5-30.3 97.3-24.9 24.8-53.1 38.8-74.2 38.8" style="fill:#ff5805"/><path d="M140.5 91.6C134.4 74.1 122 55.4 105.6 39 69.8 3.2 27.1-10.1 8.3 8.6 7 10 8.2 13.3 10.9 16s6.1 3.9 7.4 2.6c11.1-11.1 45.6-1.4 77.3 30.3 15 15 26.2 31.8 31.6 47.3 4.7 13.6 4.3 24.6-1.2 30.1-1.3 1.3-.2 4.6 2.6 7.4s6.1 3.9 7.4 2.6c9.6-9.7 11.2-25.6 4.5-44.7" style="fill:#f5afcb"/><path d="M167.5 8.6C157.9-1 142-2.6 122.9 4c-17.5 6.1-36.2 18.5-52.6 34.9-35.8 35.8-49.1 78.5-30.3 97.3 1.3 1.3 4.7.2 7.4-2.6s3.9-6.1 2.6-7.4c-11.1-11.1-1.4-45.6 30.3-77.3 15-15 31.8-26.2 47.2-31.6 13.6-4.7 24.6-4.3 30.1 1.2 1.3 1.3 4.6.2 7.4-2.6s3.9-5.9 2.5-7.3" style="fill:#ff9b00"/></svg>
                                </span>
                            </x-secondary-button>
                            @endif

                            @if ($paymentGateway->paypal_status)
                            <x-secondary-button wire:click='initiatePaypalPayment({{ $paymentOrder->id }})'>
                                <span class="inline-flex items-center">
                                    <svg  height="21" viewBox="0 0 916.7 144.7" class="h-6 w-22" xmlns="http://www.w3.org/2000/svg" >
                                        <defs>
                                          <style>
                                            .text { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 80px; font-weight: bold; }
                                            .dark-blue { fill: #002E6D; }
                                            .blue { fill: #009CDE; }
                                          </style>
                                        </defs>
                                        <!-- P Shape -->
                                        <path class="dark-blue" d="M60,30 h50 a30,30 0 0 1 0,60 h-35 l-10,60 h-30z"/>
                                        <!-- Overlay light P -->
                                        <path class="blue" d="M75,40 h25 a20,20 0 0 1 0,40 h-20 l-8,40 h-20z"/>
                                        <!-- PayPal Text -->
                                        <text x="140" y="95" class="text">
                                          <tspan class="dark-blue">Pay</tspan><tspan class="blue">Pal</tspan>
                                        </text>
                                      </svg>

                                </span>
                            </x-secondary-button>
                            @endif

                             @if($paymentGateway->payfast_status)
                                <x-secondary-button wire:click='initiatePayfastPayment({{  $paymentOrder->id }})'>
                                    <span class="inline-flex items-center">
                                        <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8 6 L14 12 L8 18" fill="none" stroke="#E63950" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                         @lang('modules.billing.payfast')

                                    </span>
                                </x-secondary-button>
                            @endif


                        @if($paymentGateway->paystack_status)
                            <x-secondary-button wire:click='initiatePaystackPayment({{  $paymentOrder->id  }})'>
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#0AA5FF">
                                        <path d="M2 3.6c0-.331.269-.6.6-.6H21.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6V3.6Zm0 4.8c0-.331.269-.6.6-.6H15.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6V8.4Zm0 4.8c0-.331.269-.6.6-.6H21.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6v-1.8Zm0 4.8c0-.331.269-.6.6-.6H15.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6v-1.8Z" fill-rule="evenodd"/>
                                    </svg>
                                    @lang('modules.billing.paystack')

                                </span>
                            </x-secondary-button>
                        @endif

                        @if($paymentGateway->xendit_status)
                            <x-secondary-button wire:click='initiateXenditPayment({{  $paymentOrder->id  }})'>
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" id="Xendit--Streamline-Simple-Icons" height="24" width="24">
                                        <desc>
                                        Xendit Streamline Icon: https://streamlinehq.com
                                        </desc>
                                        <title>Xendit</title>
                                        <path d="M11.781 2.743H7.965l-5.341 9.264 5.341 9.263 -1.312 2.266L0 12.007 6.653 0.464h6.454l-1.326 2.279Zm-5.128 2.28 1.312 -2.28L9.873 6.03 8.561 8.296 6.653 5.023Zm9.382 -2.28 1.312 2.28L7.965 21.27l-1.312 -2.279 9.382 -16.248Zm-5.128 20.793 1.298 -2.279h3.83L14.1 17.931l1.312 -2.267 1.926 3.337 4.038 -6.994 -5.341 -9.264L17.347 0.464 24 12.007l-6.653 11.529h-6.44Z" fill="#000000" stroke-width="1"></path>
                                    </svg>
                                    @lang('modules.billing.xendit')
                                </span>
                            </x-secondary-button>
                        @endif

                            @if ($paymentGateway->is_qr_payment_enabled && $paymentGateway->qr_code_image_url)
                                <!-- Button -->
                                <x-secondary-button wire:click="toggleQrCode">
                                    <span class="inline-flex items-center">
                                        <svg class="w-5 h-5" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><path fill="none" d="M0 0h24v24H0z"/><path d="M16 17v-1h-3v-3h3v2h2v2h-1v2h-2v2h-2v-3h2v-1zm5 4h-4v-2h2v-2h2zM3 3h8v8H3zm2 2v4h4V5zm8-2h8v8h-8zm2 2v4h4V5zM3 13h8v8H3zm2 2v4h4v-4zm13-2h3v2h-3zM6 6h2v2H6zm0 10h2v2H6zM16 6h2v2h-2z"/></svg>
                                        <span class="ml-2">@lang('modules.billing.paybyQr')</span>
                                    </span>
                                </x-secondary-button>
                            @endif

                            @if ($paymentGateway->is_offline_payment_enabled && $paymentGateway->offline_payment_detail)
                                <!-- Button -->
                                <x-secondary-button wire:click="togglePaymentDetail">
                                    <span class="inline-flex items-center">
                                        <svg class="w-5 h-5" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 15V17M6 7H18C18.5523 7 19 7.44772 19 8V16C19 16.5523 18.5523 17 18 17H6C5.44772 17 5 16.5523 5 16V8C5 7.44772 5.44772 7 6 7ZM6 7L18 7C18.5523 7 19 6.55228 19 6V4C19 3.44772 18.5523 3 18 3H6C5.44772 3 5 3.44772 5 4V6C5 6.55228 5.44772 7 6 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 11C13.1046 11 14 10.1046 14 9C14 7.89543 13.1046 7 12 7C10.8954 7 10 7.89543 10 9C10 10.1046 10.8954 11 12 11Z" stroke="currentColor" stroke-width="2"/>
                                        </svg>

                                        <span class="ml-2">@lang('modules.billing.bankTransfer')</span>
                                    </span>
                                </x-secondary-button>
                            @endif

                            @if($paymentGateway->is_cash_payment_enabled)
                            <x-secondary-button wire:click="makePayment({{ $paymentOrder->id }}, 'cash')">
                                <span class="inline-flex items-center">
                                    <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M8 7V6a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1M3 18v-7a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/></svg>
                                    <span class="ml-2">@lang('modules.order.payViaCash')</span>
                                </span>
                            </x-secondary-button>
                            @endif
                    </div>
                @endif
            </x-slot>

            <x-slot name="footer">
                <x-button-cancel wire:click="hidePaymentModal" wire:loading.attr="disabled" />
                @if ($showQrCode || $showPaymentDetail)
                <x-button class="ml-3" wire:click="makePayment({{ $paymentOrder->id }}, '{{ $showQrCode ? 'upi' : 'others' }}')" wire:loading.attr="disabled">@lang('modules.billing.paymentDone')</x-button>
                @endif
            </x-slot>
        </x-dialog-modal>

        <x-dialog-modal wire:model.live="showTipModal" maxWidth="md">
            <x-slot name="title">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-skin-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>@lang('modules.order.addTip')</span>
                </div>
            </x-slot>

            <x-slot name="content">
                <!-- Current Order Summary -->
                <div class="p-4 mb-6 rounded-lg bg-gray-50 dark:bg-gray-700">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600 dark:text-gray-400">@lang('modules.order.currentTotal')</span>
                        <span class="font-medium">{{ currency_format($order->total - $order->tip_amount, $restaurant->currency_id) }}</span>
                    </div>
                    <div class="flex justify-between text-skin-base">
                        <span>@lang('modules.order.tipAmount')</span>
                        <span class="font-medium">+ {{ currency_format($tipAmount ?? 0, $restaurant->currency_id) }}</span>
                    </div>
                    <div class="pt-2 mt-2 border-t border-gray-200 dark:border-gray-800">
                        <div class="flex justify-between">
                            <span class="font-medium">@lang('modules.order.newTotal')</span>
                            <span class="text-lg font-bold">
                                {{ currency_format(($order->total - $order->tip_amount + ($tipAmount ?: 0)), $restaurant->currency_id) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Preset Tip Options -->
                <div class="mb-6">
                    <x-label for="suggestedTip" value="{{ __('modules.order.suggestedTip') }}" />
                    <div class="grid grid-cols-4 gap-3">
                        @foreach ([5, 10, 15, 20] as $percentage)
                            @php
                                $calculatedTip = round($order->sub_total * $percentage / 100, 2);
                            @endphp
                            <button type="button"
                                wire:click="$set('tipAmount', {{ $calculatedTip }})"
                                class="relative px-4 py-3 text-sm font-medium rounded-xl border-2
                                    {{ $tipAmount == $calculatedTip
                                        ? 'border-skin-base bg-skin-base/10 text-skin-base shadow-sm'
                                        : 'border-gray-200 dark:border-gray-600 hover:border-skin-base hover:bg-skin-base/5'
                                    }}"
                            >
                                <div class="font-bold">{{ $percentage }}%</div>
                                <div class="text-xs text-gray-500 break-words dark:text-gray-400">
                                    {{ currency_format($calculatedTip, $restaurant->currency_id) }}
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Custom Amount Input -->
                <div class="mb-6">
                    <x-label for="tipAmount" value="{{ __('modules.order.customAmount') }}" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400">{{ $restaurant->currency->currency_symbol }}</span>
                        </div>
                        <x-input
                            id="tipAmount"
                            type="number"
                            step="0.01"
                            class="block w-full {{ strlen(currency()) > 2 ? 'pl-12' : 'pl-8' }} pr-12 rounded-xl"
                            wire:model.live="tipAmount"
                            placeholder="{{__('placeholders.enterCustomAmountPlaceholder')}}"

                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <button type="button"
                                wire:click="$set('tipAmount', 0)"
                                class="text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-300"
                                title="Clear amount">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tip Note -->
                <div>
                    <label for="tipNote" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        @lang('modules.order.tipNote')
                        <span class="text-xs text-gray-400">(@lang('app.optional'))</span>
                    </label>
                    <x-textarea
                        id="tipNote"
                        data-gramm="false"
                        class="block w-full rounded-xl"
                        wire:model="tipNote"
                        placeholder="{{__('placeholders.addNotePlaceholder')}}"
                    />
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex justify-end gap-3">
                    <x-button-cancel wire:click="$toggle('showTipModal')" wire:loading.attr="disabled">
                        @lang('app.cancel')
                    </x-button-cancel>
                    <x-button wire:click="addTip" wire:loading.attr="disabled" class="min-w-[100px]">
                        <span wire:loading.remove wire:target="addTip">@lang('app.save')</span>
                        <span wire:loading wire:target="addTip">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </x-button>
                </div>
            </x-slot>
        </x-dialog-modal>

        @script
        <script>
            $wire.on('paymentInitiated', (payment) => {
                        payViaRazorpay(payment);
                    });

                    $wire.on('stripePaymentInitiated', (payment) => {
                        document.getElementById('order_payment').value = payment.payment.id;
                        document.getElementById('order-payment-form').submit();
                    });

                    function payViaRazorpay(payment) {

                        var options = {
                            "key": "{{ $restaurant->paymentGateways->razorpay_key }}", // Enter the Key ID generated from the Dashboard
                            "amount": (parseFloat(payment.payment.amount) * 100),
                            "currency": "{{ $restaurant->currency->currency_code }}",
                            "description": "Order Payment",
                            "image": "{{ $restaurant->logoUrl }}",
                            "order_id": payment.payment.razorpay_order_id,
                            "handler": function(response) {
                                Livewire.dispatch('razorpayPaymentCompleted', [response.razorpay_payment_id, response
                                    .razorpay_order_id,
                                    response.razorpay_signature
                                ]);
                            },
                            "modal": {
                                "ondismiss": function() {
                                    if (confirm("Are you sure, you want to close the form?")) {
                                        txt = "You pressed OK!";
                                        console.log("Checkout form closed by the user");
                                    } else {
                                        txt = "You pressed Cancel!";
                                        console.log("Complete the Payment")
                                    }
                                }
                            }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.on('payment.failed', function(response) {
                            console.log(response);
                        });
                        rzp1.open();
                    }
        </script>
        @endscript

</div>

@push('scripts')

    @if(pusherSettings()->is_enabled_pusher_broadcast)
        <script>

            document.addEventListener('DOMContentLoaded', function () {

                const channel = PUSHER.subscribe('order-success');
                channel.bind('order-success.created', function(data) {
                    // @this.call('refreshOrderSuccess');

                    console.log(data);
                    new Audio("{{ asset('sound/new_order.wav')}}").play();
                    console.log('✅ Pusher received data for order success!. Refreshing...');
                    window.location.reload();
                });
                PUSHER.connection.bind('connected', () => {
                    console.log('✅ Pusher connected for Order Success!');
                });
                channel.bind('pusher:subscription_succeeded', () => {
                    console.log('✅ Subscribed to order-success channel!');
                });
            });
        </script>
    @endif

    <script>
        // Status change animation effects
        document.addEventListener('DOMContentLoaded', function() {
            let previousStatus = null;

            // Function to add status change effects
            function addStatusChangeEffects() {
                const statusSteps = document.querySelectorAll('[data-status-step]');
                const currentStep = document.querySelector('.animate-bounce');

                if (currentStep) {
                    const currentStatus = currentStep.getAttribute('data-status-step');

                    if (previousStatus && previousStatus !== currentStatus) {
                        // Add celebration effect for status change
                        addCelebrationEffect(currentStep);
                    }

                    previousStatus = currentStatus;
                }
            }

                        // Function to add celebration effect
            function addCelebrationEffect(element) {
                // Add a temporary celebration class
                element.classList.add('status-change-celebration');

                // Play notification sound
                try {
                    const audio = new Audio("{{ asset('sound/sound_beep-29.mp3') }}");
                    audio.volume = 0.3;
                    audio.play();
                } catch (e) {
                    console.log('Audio play failed:', e);
                }

                // Remove the celebration class after animation
                setTimeout(() => {
                    element.classList.remove('status-change-celebration');
                }, 1000);
            }

            // Watch for Livewire updates
            document.addEventListener('livewire:updated', function() {
                setTimeout(addStatusChangeEffects, 100);
            });

            // Initial check
            addStatusChangeEffects();
        });
    </script>
@endpush
