
        <!-- 🟡 2. Order Type Selection -->
        <div x-show="currentScreen === 'order-type'" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform -translate-x-full"
             class="min-h-screen flex items-center justify-center bg-white">
            <div class="w-full max-w-4xl px-8">
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">How would you like to order?</h1>
                    <p class="text-xl text-gray-600">Choose your dining preference</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Dine-In -->
                    <button @click="selectOrderType('dine-in')" 
                            class="group bg-white border-2 border-gray-200 rounded-lg p-8 hover:border-skin-base hover:shadow-lg transition-all duration-200">
                        <div class="w-20 h-20 bg-skin-base rounded-lg mx-auto mb-6 flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Dine-In</h3>
                        <p class="text-gray-600">Enjoy your meal in our restaurant</p>
                    </button>

                    <!-- Takeaway -->
                    <button @click="selectOrderType('takeaway')" 
                            class="group bg-white border-2 border-gray-200 rounded-lg p-8 hover:border-skin-base hover:shadow-lg transition-all duration-200">
                        <div class="w-20 h-20 bg-skin-base rounded-lg mx-auto mb-6 flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Takeaway</h3>
                        <p class="text-gray-600">Pick up your order to go</p>
                    </button>

                    <!-- Delivery -->
                    <button @click="selectOrderType('delivery')" 
                            class="group bg-white border-2 border-gray-200 rounded-lg p-8 hover:border-skin-base hover:shadow-lg transition-all duration-200">
                        <div class="w-20 h-20 bg-skin-base rounded-lg mx-auto mb-6 flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Delivery</h3>
                        <p class="text-gray-600">Get it delivered to your door</p>
                    </button>
                </div>

                <!-- Back Button -->
                <div class="text-center mt-12">
                    <button @click="currentScreen = 'welcome'" 
                            class="text-gray-500 hover:text-gray-700 font-medium text-lg transition-colors duration-200">
                        Back
                    </button>
                </div>
            </div>
        </div>