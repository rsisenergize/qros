<div class="flex flex-col items-center">
    <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50 p-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1 truncate">
                            {{ $selectionStage === 'order_type' ? __('modules.order.selectOrderType') : __('modules.order.selectDeliveryPlatform') }}
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $selectionStage === 'order_type' ? __('modules.order.selectOrderTypeDescription') : __('modules.order.selectDeliveryPlatformDescription') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($selectionStage === 'delivery_platform')
                            <button type="button" wire:click="goBackToOrderTypes" class="p-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg transition-all" title="@lang('modules.order.backToOrderTypes')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                                </svg>
                            </button>
                        @endif
                        @if($selectedOrderTypeSlug)
                            <button type="button" wire:click="resetSelection" class="p-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg transition-all" title="@lang('modules.order.resetSelection')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                                </svg>
                            </button>
                        @endif
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 p-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg transition-all" title="@lang('menu.dashboard')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z"/>
                            </svg>
                            @lang('menu.dashboard')
                        </a>

                        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 p-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg transition-all" title="@lang('modules.order.closeModal')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 512.005 512.005"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <rect y="389.705" width="512.005" height="66.607"></rect> </g> </g> <g> <g> <path d="M297.643,131.433c4.862-7.641,7.693-16.696,7.693-26.404c0-27.204-22.132-49.336-49.336-49.336 c-27.204,0-49.336,22.132-49.336,49.337c0,9.708,2.831,18.763,7.693,26.404C102.739,149.772,15.208,240.563,1.801,353.747h508.398 C496.792,240.563,409.261,149.772,297.643,131.433z M256,118.415c-7.38,0-13.384-6.005-13.384-13.385S248.62,91.646,256,91.646 s13.384,6.004,13.384,13.384S263.38,118.415,256,118.415z"></path> </g> </g> </g></svg>
                            @lang('menu.orders')
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto p-6">
                @if($selectionStage === 'order_type')
                    <!-- Order Types Selection -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @forelse ($orderTypes as $orderType)
                        <button wire:click="selectOrderType({{ $orderType->id }}, '{{ $orderType->slug }}')"
                            class="group relative flex flex-col items-center justify-center p-6 rounded-xl border-2 transition-all duration-200 cursor-pointer
                            {{ $selectedOrderTypeSlug === $orderType->slug
                                ? 'border-skin-base bg-skin-base/5 dark:bg-skin-base/10'
                                : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-skin-base/50 hover:shadow-lg hover:scale-[1.02]' }}">

                            @if($selectedOrderTypeSlug === $orderType->slug)
                                <div class="absolute top-2 right-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-skin-base" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="mb-3 p-3 rounded-lg {{ $selectedOrderTypeSlug === $orderType->slug ? 'bg-skin-base/10' : 'bg-gray-50 dark:bg-gray-900 group-hover:bg-skin-base/5' }} transition-colors duration-200">
                                @if($orderType->slug === 'dine_in')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="text-skin-base" viewBox="0 0 16 16"><path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.37 2.37 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0M1.5 8.5A.5.5 0 0 1 2 9v6h1v-5a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v5h6V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5M4 15h3v-5H4zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/></svg>
                                @elseif($orderType->slug === 'delivery')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="text-skin-base" viewBox="0 0 16 16"><path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/></svg>
                                @elseif($orderType->slug === 'pickup')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="text-skin-base" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="text-skin-base" viewBox="0 0 16 16"><path d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27z"/></svg>
                                @endif
                            </div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white text-center">{{ $orderType->order_type_name }}</span>
                        </button>
                        @empty
                        <div class="col-span-full text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="text-gray-400" viewBox="0 0 16 16"><path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4zm-1.17-.437A1.5 1.5 0 0 1 4.98 3h6.04a1.5 1.5 0 0 1 1.17.563l3.7 4.625a.5.5 0 0 1 .106.374l-.39 3.124A1.5 1.5 0 0 1 14.117 13H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .106-.374z"/></svg>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">@lang('modules.order.noOrderTypesAvailable')</p>
                        </div>
                        @endforelse
                    </div>
                @else
                    <!-- Delivery Platform Selection -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <button type="button" wire:click="selectDeliveryPlatformAndProceed('default')"
                            class="group text-left p-4 rounded-xl border-2 transition-all duration-200
                            border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800
                            hover:border-skin-base/50 hover:shadow-lg hover:scale-[1.02]">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 rounded-lg bg-gray-50 dark:bg-gray-900 flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-skin-base" viewBox="0 0 16 16"><path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">@lang('modules.order.defaultDeliveryPlatform')</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('modules.order.standardDeliveryPricing')</p>
                                </div>
                            </div>
                        </button>

                        @foreach($deliveryPlatforms as $platform)
                        <button type="button" wire:click="selectDeliveryPlatformAndProceed({{ $platform->id }})"
                            class="group text-left p-4 rounded-xl border-2 transition-all duration-200
                            border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800
                            hover:border-skin-base/50 hover:shadow-lg hover:scale-[1.02]">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 rounded-lg bg-gray-50 dark:bg-gray-900 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    @if(!empty($platform->logo))
                                        <img src="{{ $platform->logo_url }}" alt="{{ $platform->name }}" class="h-12 w-12 object-contain"/>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-skin-base" viewBox="0 0 16 16"><path d="M0 3a2 2 0 0 1 2-2h7.5a2 2 0 0 1 1.6.8l3 4A2 2 0 0 1 15.5 7H14v6a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm11 0H2a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V7h2.5a1 1 0 0 0 .8-1.6l-3-4A1 1 0 0 0 11 3"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $platform->name }}</p>      
                                </div>
                            </div>
                        </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>