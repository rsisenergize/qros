      

        <!-- 🟡 6. Payment Method Selection -->
        <div x-show="currentScreen === 'payment'" 
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
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">Payment Method</h1>
                    <p class="text-xl text-gray-600">Choose your preferred payment method</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Order Summary -->
                    <div class="bg-white border border-gray-200 rounded-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Order Summary</h2>
                        <div class="space-y-3 mb-6">
                            <template x-for="item in cart" :key="item.id">
                                <div class="flex justify-between text-lg">
                                    <span x-text="item.quantity + 'x ' + item.name"></span>
                                    <span x-text="'$' + (item.price * item.quantity).toFixed(2)"></span>
                                </div>
                            </template>
                        </div>
                        <div class="border-t border-gray-200 pt-6 space-y-3">
                            <div class="flex justify-between text-lg">
                                <span class="text-gray-600">Subtotal</span>
                                <span x-text="'$' + subtotal"></span>
                            </div>
                            <div class="flex justify-between text-lg">
                                <span class="text-gray-600">Tax (8%)</span>
                                <span x-text="'$' + tax"></span>
                            </div>
                            <div class="flex justify-between text-2xl font-bold text-gray-900 border-t border-gray-200 pt-3">
                                <span>Total</span>
                                <span x-text="'$' + total"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="bg-white border border-gray-200 rounded-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Method</h2>
                        
                        <div class="space-y-4">
                            <!-- Credit/Debit Card -->
                            <button @click="selectPaymentMethod('card')" 
                                    :class="{'border-skin-base bg-skin-base': paymentMethod === 'card'}"
                                    class="w-full border-2 border-gray-200 rounded-lg p-6 flex items-center justify-between hover:bg-gray-50 transition-all duration-200">
                                <div class="flex items-center">
                                    <div class="w-16 h-16 bg-skin-base rounded-lg flex items-center justify-center mr-6">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="font-bold text-gray-900 text-lg">Credit/Debit Card</div>
                                        <div class="text-gray-600">Visa, Mastercard, Amex</div>
                                    </div>
                                </div>
                                <svg x-show="paymentMethod === 'card'" class="w-8 h-8 text-skin-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>

                            <!-- UPI -->
                            <button @click="selectPaymentMethod('upi')" 
                                    :class="{'border-skin-base bg-skin-base': paymentMethod === 'upi'}"
                                    class="w-full border-2 border-gray-200 rounded-lg p-6 flex items-center justify-between hover:bg-gray-50 transition-all duration-200">
                                <div class="flex items-center">
                                    <div class="w-16 h-16 bg-skin-base rounded-lg flex items-center justify-center mr-6">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="font-bold text-gray-900 text-lg">UPI (QR Code)</div>
                                        <div class="text-gray-600">Google Pay, PhonePe, Paytm</div>
                                    </div>
                                </div>
                                <svg x-show="paymentMethod === 'upi'" class="w-8 h-8 text-skin-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>

                            <!-- McDonald's App Wallet -->
                            <button @click="selectPaymentMethod('wallet')" 
                                    :class="{'border-skin-base bg-skin-base': paymentMethod === 'wallet'}"
                                    class="w-full border-2 border-gray-200 rounded-lg p-6 flex items-center justify-between hover:bg-gray-50 transition-all duration-200">
                                <div class="flex items-center">
                                    <div class="w-16 h-16 bg-skin-base rounded-lg flex items-center justify-center mr-6">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="font-bold text-gray-900 text-lg">McDonald's App Wallet</div>
                                        <div class="text-gray-600">App balance & rewards</div>
                                    </div>
                                </div>
                                <svg x-show="paymentMethod === 'wallet'" class="w-8 h-8 text-skin-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>

                            <!-- Cash -->
                            <button @click="selectPaymentMethod('cash')" 
                                    :class="{'border-skin-base bg-skin-base': paymentMethod === 'cash'}"
                                    class="w-full border-2 border-gray-200 rounded-lg p-6 flex items-center justify-between hover:bg-gray-50 transition-all duration-200">
                                <div class="flex items-center">
                                    <div class="w-16 h-16 bg-skin-base rounded-lg flex items-center justify-center mr-6">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="font-bold text-gray-900 text-lg">Cash</div>
                                        <div class="text-gray-600">Pay at counter</div>
                                    </div>
                                </div>
                                <svg x-show="paymentMethod === 'cash'" class="w-8 h-8 text-skin-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 space-y-4">
                            <button @click="processPayment" 
                                    :disabled="!paymentMethod"
                                    :class="{'opacity-50 cursor-not-allowed': !paymentMethod}"
                                    class="w-full bg-skin-base text-white py-6 rounded-lg font-bold text-xl transition-all duration-200 hover:bg-skin-base">
                                Pay Now
                            </button>
                            <button @click="currentScreen = 'customer-info'" 
                                    class="w-full border-2 border-gray-300 text-gray-700 py-4 rounded-lg font-medium text-lg hover:bg-gray-50 transition-colors duration-200">
                                Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>