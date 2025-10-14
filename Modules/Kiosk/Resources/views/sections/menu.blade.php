
        <!-- 🟡 3. Menu Browsing Screen -->
        <div x-show="currentScreen === 'menu'" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="min-h-screen flex flex-col lg:flex-row relative bg-white">
            <!-- Main Content Area -->
            <div class="flex-1 p-6">
                <!-- Header -->
                <div class="bg-white border-b border-gray-200 pb-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Menu</h1>
                            <p class="text-gray-600 mt-1 text-lg" 
                               x-text="orderType === 'dine-in' ? 'Table ' + tableNumber : (orderType === 'takeaway' ? 'Take Away' : 'Delivery')"></p>
                        </div>
                        <button @click="showCart = true" class="relative bg-skin-base p-4 rounded-lg hover:bg-skin-base transition-all duration-200">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span x-show="cart.length > 0" 
                                  class="absolute -top-2 -right-2 bg-yellow-400 text-black text-xs rounded-full h-6 w-6 flex items-center justify-center font-bold"
                                  x-text="cart.length"></span>
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
                               x-model="searchQuery"
                               class="block w-full pl-12 pr-4 py-4 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-skin-base focus:border-skin-base text-lg"
                               placeholder="Search menu items...">
                    </div>
                </div>

                <!-- 🟡 Highlighted Deals / Limited Time Offers -->
                <div class="mb-8" x-data="{ 
                    currentSlide: 0, 
                    offers: [
                        {
                            title: 'Mega Burger Feast',
                            description: 'Get 2 premium burgers + 2 large fries + 2 drinks for just $24.99',
                            image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80',
                            originalPrice: '$34.99',
                            discountPrice: '$24.99'
                        },
                        {
                            title: 'Happy Hour Special',
                            description: '20% off on all drinks and cocktails from 2 PM to 5 PM',
                            image: 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80',
                            discount: '20% OFF'
                        },
                        {
                            title: 'Family Feast',
                            description: '4 burgers + 4 fries + 4 drinks + 1 dessert for the whole family',
                            image: 'https://images.unsplash.com/photo-1553979459-d2229ba7433b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80',
                            originalPrice: '$45.99',
                            discountPrice: '$35.99'
                        }
                    ],
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
                        <template x-for="(offer, index) in offers" :key="index">
                            <div x-show="currentSlide === index"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-300"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="absolute inset-0 w-full h-full">
                                <div class="absolute inset-0 bg-black/60"></div>
                                <img :src="offer.image" 
                                     :alt="offer.title" 
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 p-6 flex flex-col justify-between">
                                    <div>
                                        <span class="inline-block px-3 py-1 bg-skin-base text-white text-sm font-bold rounded-full mb-3">
                                            Limited Time
                                        </span>
                                        <h2 class="text-2xl font-bold text-white mb-2" x-text="offer.title"></h2>
                                        <p class="text-white/90 text-sm mb-3" x-text="offer.description"></p>
                                        <div class="flex items-center space-x-3">
                                            <span x-show="offer.originalPrice" class="text-white/60 line-through text-lg" x-text="offer.originalPrice"></span>
                                            <span x-show="offer.discountPrice" class="text-2xl font-bold text-yellow-400" x-text="offer.discountPrice"></span>
                                            <span x-show="offer.discount" class="text-2xl font-bold text-yellow-400" x-text="offer.discount"></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <button class="bg-skin-base hover:bg-skin-base text-white px-6 py-3 rounded-lg text-sm font-bold transition-all duration-200">
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Navigation Dots -->
                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
                            <template x-for="(offer, index) in offers" :key="index">
                                <button @click="changeSlide(index)"
                                        :class="{'bg-white': currentSlide === index, 'bg-white/50': currentSlide !== index}"
                                        class="w-2 h-2 rounded-full transition-colors duration-200"></button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- 🟡 Categories: Burgers, Meals, Beverages, Desserts, etc. -->
                <div class="mb-8">
                    <div class="flex space-x-4 overflow-x-auto pb-2 scrollbar-hide">
                        <template x-for="(category, index) in categories" :key="category.id">
                            <button @click="selectedCategory = category.id"
                                    :class="{'bg-skin-base text-white': selectedCategory === category.id, 'bg-gray-100 text-gray-700 hover:bg-gray-200': selectedCategory !== category.id}"
                                    class="px-6 py-4 rounded-lg font-semibold whitespace-nowrap transition-all duration-200 flex-shrink-0 text-lg">
                                <span x-text="category.name"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- 🟡 Menu Items Grid - Minimal Design -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="(item, index) in filteredItems" :key="item.id">
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-all duration-200">
                            <div class="relative">
                                <img :src="item.image" :alt="item.name" class="w-full h-48 object-cover">
                               
                            </div>
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-bold text-gray-900 text-lg" x-text="item.name"></h3>
                                    <span class="font-bold text-gray-900 text-xl" x-text="'$' + item.price"></span>
                                </div>
                                <p class="text-gray-600 text-sm mb-4" x-text="item.description"></p>
                                
                                <button @click="selectItem(item)"
                                        class="w-full bg-skin-base text-white py-3 rounded-lg font-bold hover:bg-skin-base transition-all duration-200">
                                    Select Item
                                </button>
                            </div>
                        </div>
                    </template>
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
                    <h2 class="text-lg sm:text-xl font-semibold text-slate-900">Your Order</h2>
                    <button @click="showCart = false" class="text-slate-400 hover:text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Cart Items -->
                <div class="space-y-3 mb-4 sm:mb-6 h-[calc(60vh-20rem)] lg:h-[calc(100vh-28rem)] overflow-y-auto">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="border border-slate-200 p-3 sm:p-4 rounded-lg animate-slide-in">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="font-medium text-slate-900 text-sm sm:text-base" x-text="item.name"></h3>
                                    <p class="text-sm text-slate-600" x-text="'$' + item.price"></p>
                                </div>
                                <button @click="removeFromCart(index)" class="text-slate-400 hover:text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button @click="updateQuantity(index, -1)" class="text-slate-400 hover:text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <span x-text="item.quantity" class="text-slate-900"></span>
                                <button @click="updateQuantity(index, 1)" class="text-slate-400 hover:text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Order Summary -->
                <div class="border-t border-slate-200 pt-4">
                    <div class="flex justify-between mb-2 text-sm sm:text-base">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-medium" x-text="'$' + subtotal"></span>
                    </div>
                    <div class="flex justify-between mb-2 text-sm sm:text-base">
                        <span class="text-slate-600">Tax (8%)</span>
                        <span class="font-medium" x-text="'$' + tax"></span>
                    </div>
                    <div class="flex justify-between mb-4 sm:mb-6 text-sm sm:text-base">
                        <span class="text-slate-600">Total</span>
                        <span class="font-semibold text-lg sm:text-xl text-slate-900" x-text="'$' + total"></span>
                    </div>
                </div>

                <!-- Checkout Button -->
                <div class="sticky bottom-0 bg-white pt-4">
                    <button @click="proceedToCheckout" 
                            :disabled="cart.length === 0"
                            :class="{'opacity-50 cursor-not-allowed': cart.length === 0}"
                            class="w-full bg-slate-900 text-white py-3 rounded-lg font-medium hover:bg-slate-800 text-sm sm:text-base">
                        Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>
