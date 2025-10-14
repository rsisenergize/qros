 <!-- Table Entry Screen (for Dine-In) -->
 <div x-show="currentScreen === 'table-entry'" 
 x-cloak
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform translate-x-full"
 x-transition:enter-end="opacity-100 transform translate-x-0"
 x-transition:leave="transition ease-in duration-200"
 x-transition:leave-start="opacity-100 transform translate-x-0"
 x-transition:leave-end="opacity-0 transform -translate-x-full"
 class="min-h-screen flex items-center justify-center bg-white">
<div class="w-full max-w-md px-8">
    <div class="text-center mb-12">
        <div class="w-24 h-24 bg-red-600 rounded-lg mx-auto mb-6 flex items-center justify-center">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Enter Table Number</h2>
        <p class="text-xl text-gray-600">Please enter your table number to continue</p>
    </div>
    
    <!-- QR Scanner Button -->
    <button @click="scanQR" 
            class="w-full bg-gray-100 border-2 border-gray-300 text-gray-700 py-6 rounded-lg font-medium hover:bg-gray-200 transition-all duration-200 mb-6 flex items-center justify-center">
        <svg class="h-6 w-6 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
        </svg>
        Scan Table QR
    </button>

    <!-- Manual Entry -->
    <div class="mb-8">
        <label class="block text-lg font-medium text-gray-700 mb-4 text-center">
            Or enter table number manually
        </label>
        <input type="number" 
               x-model="tableNumber"
               class="w-full border-2 border-gray-300 rounded-lg px-6 py-6 text-center text-3xl font-bold focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
               placeholder="Enter table number">
    </div>

    <div class="space-y-4">
        <button @click="confirmTable" 
                :disabled="!tableNumber"
                :class="{'opacity-50 cursor-not-allowed': !tableNumber}"
                class="w-full bg-red-600 text-white py-6 rounded-lg font-bold text-xl transition-all duration-200 hover:bg-red-700">
            Continue
        </button>
        
        <button @click="currentScreen = 'order-type'" 
                class="w-full border-2 border-gray-300 text-gray-700 py-4 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200">
            Back
        </button>
    </div>
</div>
</div>