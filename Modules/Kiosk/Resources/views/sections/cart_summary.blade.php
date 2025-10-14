  

        <!-- 🟡 5. Cart Summary & Customer Information -->
        <div x-show="currentScreen === 'customer-info'" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform -translate-x-full"
             class="min-h-screen flex items-center justify-center bg-white">
            <div class="w-full max-w-6xl px-6">
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">Order Summary</h1>
                    <p class="text-xl text-gray-600">Review your order and provide your information</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Order Summary -->
                    <div class="bg-white border border-gray-200 rounded-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            Your Order
                            <span class="text-gray-500 ml-2" x-text="'(' + cart.length + ' items)'"></span>
                        </h2>
                        
                        <!-- Cart Items -->
                        <div class="space-y-4 mb-8 max-h-96 overflow-y-auto">
                            <template x-for="(item, index) in cart" :key="index">
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <img :src="item.image" :alt="item.name" class="w-16 h-16 rounded-lg object-cover">
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-lg" x-text="item.name"></h4>
                                            <div class="text-sm text-gray-600">
                                                <span x-show="item.variant" x-text="item.variant + ' • '"></span>
                                                <span x-show="item.addons && item.addons.length > 0" x-text="item.addons.join(', ') + ' • '"></span>
                                                <span x-show="item.removals && item.removals.length > 0" x-text="No: ' + item.removals.join(', ')"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-gray-900 text-lg" x-text="'$' + (item.price * item.quantity).toFixed(2)"></div>
                                        <div class="text-sm text-gray-500" x-text="'Qty: ' + item.quantity"></div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Order Totals -->
                        <div class="border-t border-gray-200 pt-6 space-y-3">
                            <div class="flex justify-between text-lg">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold" x-text="'$' + subtotal"></span>
                            </div>
                            <div class="flex justify-between text-lg">
                                <span class="text-gray-600">Tax (8%)</span>
                                <span class="font-semibold" x-text="'$' + tax"></span>
                            </div>
                            <div class="flex justify-between text-2xl font-bold text-gray-900 border-t border-gray-200 pt-3">
                                <span>Total</span>
                                <span x-text="'$' + total"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information Form -->
                    <div class="bg-white border border-gray-200 rounded-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Customer Information</h2>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-3">Full Name</label>
                                <input type="text" 
                                       x-model="customerInfo.name"
                                       class="w-full border-2 border-gray-300 rounded-lg px-4 py-4 text-lg focus:outline-none focus:ring-2 focus:ring-skin-base focus:border-skin-base"
                                       placeholder="Enter your full name">
                            </div>

                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-3">Email Address</label>
                                <input type="email" 
                                       x-model="customerInfo.email"
                                       class="w-full border-2 border-gray-300 rounded-lg px-4 py-4 text-lg focus:outline-none focus:ring-2 focus:ring-skin-base focus:border-skin-base"
                                       placeholder="Enter your email address">
                            </div>

                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-3">Phone Number</label>
                                <input type="tel" 
                                       x-model="customerInfo.phone"
                                       class="w-full border-2 border-gray-300 rounded-lg px-4 py-4 text-lg focus:outline-none focus:ring-2 focus:ring-skin-base focus:border-skin-base"
                                       placeholder="Enter your phone number">
                            </div>

                            <div x-show="orderType === 'takeaway'">
                                <label class="block text-lg font-medium text-gray-700 mb-3">Pickup Time</label>
                                <select x-model="customerInfo.pickupTime"
                                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-4 text-lg focus:outline-none focus:ring-2 focus:ring-skin-base focus:border-skin-base">
                                    <option value="15">15 minutes</option>
                                    <option value="30">30 minutes</option>
                                    <option value="45">45 minutes</option>
                                    <option value="60">1 hour</option>
                                </select>
                            </div>

                            <div x-show="orderType === 'dine-in'">
                                <label class="block text-lg font-medium text-gray-700 mb-3">Table Number</label>
                                <div class="bg-gray-100 rounded-lg px-4 py-4 text-gray-700 font-bold text-lg">
                                    <span x-text="tableNumber"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 space-y-4">
                            <button @click="saveCustomerInfo" 
                                    :disabled="!isCustomerInfoValid"
                                    :class="{'opacity-50 cursor-not-allowed': !isCustomerInfoValid}"
                                    class="w-full bg-skin-base text-white py-6 rounded-lg font-bold text-xl transition-all duration-200 hover:bg-skin-base">
                                Proceed to Checkout
                            </button>
                            <button @click="currentScreen = 'menu'" 
                                    class="w-full border-2 border-gray-300 text-gray-700 py-4 rounded-lg font-medium text-lg hover:bg-gray-50 transition-colors duration-200">
                                Back to Menu
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>