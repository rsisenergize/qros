<div>
    <!-- 🟡 3. Menu Browsing Screen -->
    <div x-show="currentScreen === 'menu'" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-init="() => {
                window.addEventListener('showCart', (cartCount) => {
                console.log('cartUpdated');
                    showCart = true
                })
            }"
            class="min-h-screen flex flex-col lg:flex-row relative bg-white">
        <!-- Main Content Area -->
        <div class="flex-1 p-6">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ __('kiosk::modules.menu.title') }}</h1>
                        <p class="text-gray-600 mt-1 text-lg" 
                            x-text="orderType === 'dine_in' ? '{{ __('kiosk::modules.menu.order_type.dine_in') }}' : (orderType === 'pickup' ? '{{ __('kiosk::modules.menu.order_type.pickup') }}' : '{{ __('kiosk::modules.menu.order_type.delivery') }}')"></p>
                    </div>
                    <button @click="showCart = true" class="relative bg-skin-base p-4 rounded-lg hover:bg-skin-base transition-all duration-200">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        @if ($cartCount > 0)
                            <span class="absolute -top-2 -right-2 bg-yellow-400 text-black text-xs rounded-full h-6 w-6 flex items-center justify-center font-bold">{{ $cartCount }}</span>
                        @endif
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                            wire:model.live.debounce.300ms="search"
                            class="block w-full pl-12 pr-4 py-4 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-skin-base focus:border-skin-base text-lg"
                            placeholder="{{ __('kiosk::modules.menu.search_placeholder') }}">
                </div>
            </div>

            @if ($kioskAds->count() > 0)
            <!-- 🟡 Highlighted Deals / Limited Time Offers -->
            <div class="mb-8" x-data="{ 
                currentSlide: 0, 
                isTransitioning: false,
                changeSlide(index) {
                    if (this.isTransitioning) return;
                    this.isTransitioning = true;
                    this.currentSlide = index;
                    setTimeout(() => this.isTransitioning = false, 300);
                }
            }">
                <!-- Banner Slider -->
                <div class="relative rounded-lg overflow-hidden h-48 shadow-md">
                    @foreach ($kioskAds as $ad)
                        <div x-show="currentSlide === index"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="absolute inset-0 w-full h-full">
                            <div class="absolute inset-0 bg-black/60"></div>
                            <img src="{{ $ad->image_url }}" 
                                    alt="{{ $ad->heading }}" 
                                    class="w-full h-full object-cover">
                            <div class="absolute inset-0 p-6 flex flex-col justify-between">
                                <div>
                                    <h2 class="text-2xl font-bold text-white mb-2">{{ $ad->heading }}</h2>
                                    <p class="text-white/90 text-sm mb-3">{{ $ad->description }}</p>
                                    
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Navigation Dots -->
                    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
                        @foreach ($kioskAds as $ad)
                            <button @click="changeSlide({{ $ad->id }})"
                                    :class="{'bg-white': currentSlide === index, 'bg-white/50': currentSlide !== index}"
                                    class="w-2 h-2 rounded-full transition-colors duration-200"></button>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- 🟡 Categories: Burgers, Meals, Beverages, Desserts, etc. -->
            <div class="mb-8">
                <div class="flex space-x-4 overflow-x-auto pb-2 scrollbar-hide">
                    @foreach ($categoryList as $category)
                        <button
                            @class(['bg-skin-base text-white' => $selectedCategory === $category->id,
                            'bg-gray-100 text-gray-700 hover:bg-gray-200' => $selectedCategory !== $category->id,
                            'px-6 py-4 rounded-lg font-semibold whitespace-nowrap transition-all duration-200 flex-shrink-0 text-lg'
                            ])
                            wire:click="selectCategory({{ $category->id }})">
                            <span>{{ $category->category_name }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- 🟡 Menu Items Grid - Minimal Design -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($menuItems as $item)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-all duration-200" wire:key="menu-item-{{ $item->id . microtime() }}">
                        
                        <div class="relative">
                            <img src="{{ $item->item_photo_url }}" alt="{{ $item->item_name }}" class="w-full h-48 object-cover">                            
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-gray-900 text-lg">{{ $item->getTranslatedValue('item_name', session('locale')) }}</h3>
                                <span class="font-bold text-gray-900 text-xl">{{ currency_format($item->price, $restaurant->currency_id) }}</span>
                            </div>
                            <p class="text-gray-600 text-sm mb-4">{{ $item->getTranslatedValue('description', session('locale')) }}</p>
                            
                            <button wire:click="showItem({{ $item->id }})" @click="selectItem()"
                                    class="w-full bg-skin-base text-white py-3 rounded-lg font-bold hover:bg-skin-base transition-all duration-200">
                                {{ __('kiosk::modules.menu.select_item') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Cart Sidebar -->
        <div x-show="showCart" 
                x-cloak
                x-transition:enter="transition ease-out duration-200" 
                x-transition:enter-start="opacity-0 translate-x-full"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-full"
                class="lg:w-96 w-full lg:border-l border-t lg:border-t-0 border-gray-200 p-6 lg:relative fixed bottom-0 left-0 right-0 lg:h-auto h-[60vh] bg-white lg:rounded-none rounded-t-xl z-50 lg:shadow-none shadow-lg">
            <div class="flex items-center justify-between mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-semibold text-slate-900">{{ __('kiosk::modules.menu.your_order') }}</h2>
                <button @click="showCart = false" class="text-slate-400 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Cart Items -->
            <div class="space-y-3 mb-4 sm:mb-6 h-[calc(60vh-20rem)] lg:h-[calc(100vh-28rem)] overflow-y-auto">
                @foreach ($cartItemList['items'] as $item)
                    <div class="border border-slate-200 p-3 sm:p-4 rounded-lg animate-slide-in">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h3 class="font-medium text-slate-900 text-sm sm:text-base">{{ $item['menu_item']['name'] }}</h3>
                                
                                <!-- Variation Display -->
                                @if(!empty($item['variation']))
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $item['variation']['name'] }}
                                        </span>
                                    </div>
                                @endif
                                
                                <!-- Modifiers Display -->
                                @if(!empty($item['modifiers']) && count($item['modifiers']) > 0)
                                    <div class="mt-2 space-y-1">
                                        @foreach($item['modifiers'] as $modifier)
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-slate-600">+ {{ $modifier['name'] }}</span>
                                                <span class="text-slate-500">{{ currency_format($modifier['price'], $restaurant->currency_id) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <div class="mt-1">
                                    @if($taxMode === 'item' && !empty($item['tax_amount']) && $item['tax_amount'] > 0)
                                        <!-- Show display price (without tax) for inclusive, or base price for exclusive -->
                                        <p class="text-sm text-slate-600">{{ currency_format($item['display_price'], $restaurant->currency_id) }}</p>
                                        @if(!empty($item['tax_breakup']) && count($item['tax_breakup']) > 0)
                                            <div class="text-xs text-slate-500 mt-0.5">
                                                @foreach($item['tax_breakup'] as $taxName => $taxInfo)
                                                    <span>{{ $taxName }}: {{ currency_format($taxInfo['amount'], $restaurant->currency_id) }}</span>
                                                    @if(!$loop->last) | @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-sm text-slate-600">{{ currency_format($item['price'], $restaurant->currency_id) }}</p>
                                    @endif
                                </div>
                            </div>
                            <button wire:click="removeFromCart({{ $item['id'] }})" class="text-slate-400 hover:text-slate-600 ml-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center space-x-2 mt-3">
                            <button wire:click="updateQuantity({{ $item['id'] }}, -1)" class="text-slate-400 hover:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <span class="text-slate-900 font-medium">{{ $item['quantity'] }}</span>
                            <button wire:click="updateQuantity({{ $item['id'] }}, 1)" class="text-slate-400 hover:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="border-t border-slate-200 pt-4">
                <div class="flex justify-between mb-2 text-sm sm:text-base">
                    <span class="text-slate-600">{{ __('kiosk::modules.menu.subtotal') }}</span>
                    <span class="font-medium">{{ currency_format($subtotal, $restaurant->currency_id) }}</span>
                </div>
                
                @if($totalTaxAmount > 0)
                    @if($taxMode === 'order' && !empty($taxBreakdown))
                        <!-- Order-level taxes -->
                        @foreach($taxBreakdown as $taxName => $taxInfo)
                            <div class="flex justify-between mb-2 text-sm sm:text-base">
                                <span class="text-slate-600">{{ $taxName }} ({{ number_format($taxInfo['percent'], 2) }}%)</span>
                                <span class="font-medium">{{ currency_format($taxInfo['amount'], $restaurant->currency_id) }}</span>
                            </div>
                        @endforeach
                    @else
                        <!-- Item-level taxes or simple tax display -->
                        @if(!empty($taxBreakdown))
                            @foreach($taxBreakdown as $taxName => $taxInfo)
                                <div class="flex justify-between mb-2 text-sm sm:text-base">
                                    <span class="text-slate-600">{{ $taxName }} ({{ number_format($taxInfo['percent'], 2) }}%)</span>
                                    <span class="font-medium">{{ currency_format($taxInfo['amount'], $restaurant->currency_id) }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="flex justify-between mb-2 text-sm sm:text-base">
                                <span class="text-slate-600">{{ __('kiosk::modules.menu.tax') }}</span>
                                <span class="font-medium">{{ currency_format($totalTaxAmount, $restaurant->currency_id) }}</span>
                            </div>
                        @endif
                    @endif
                @endif
                
                <div class="flex justify-between mb-4 sm:mb-6 text-sm sm:text-base">
                    <span class="text-slate-600">{{ __('kiosk::modules.menu.total') }}</span>
                    <span class="font-semibold text-lg sm:text-xl text-slate-900">{{ currency_format($total, $restaurant->currency_id) }}</span>
                </div>
            </div>

            <!-- Checkout Button -->
            <div class="sticky bottom-0 bg-white pt-4">
                <button @click="proceedToCheckout" type="button"
                        {{ ($cartCount == 0) ? 'disabled' : '' }}
                        
                        class="w-full bg-slate-900 text-white py-3 rounded-lg font-medium hover:bg-slate-800 text-sm sm:text-base">
                    {{ __('kiosk::modules.menu.proceed_to_checkout') }}
                </button>
            </div>
        </div>
    </div>
</div>
