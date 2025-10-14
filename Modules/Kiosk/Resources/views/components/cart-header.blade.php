{{-- Cart Header Component for Kiosk --}}
<div class="bg-white shadow-sm border-b border-gray-200 px-4 py-3">
    <div class="flex items-center justify-between max-w-7xl mx-auto">
        {{-- Logo/Brand --}}
        <div class="flex items-center space-x-4">
            @if($restaurant->logo)
                <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}" class="h-8 w-auto">
            @endif
            <h1 class="text-xl font-semibold text-gray-900">{{ $restaurant->name }}</h1>
        </div>

        {{-- Cart Information --}}
        <div class="flex items-center space-x-4">
            {{-- Order Type Badge --}}
            @if($orderType)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $orderType === 'dine_in' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                    {{ $orderType === 'dine_in' ? 'Dine In' : ucfirst(str_replace('_', ' ', $orderType)) }}
                </span>
            @endif

            {{-- Cart Button --}}
            <button 
                wire:click="viewCart" 
                class="relative inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                
                {{-- Cart Icon --}}
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.8 9M7 13l-1.8 9m0 0h9.4m-9.4 0L17 22"></path>
                </svg>
                
                Cart
                
                {{-- Cart Count Badge --}}
                @if($cartCount > 0)
                    <span class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full min-w-[1.5rem] h-6">
                        {{ $cartCount }}
                    </span>
                @endif
            </button>

            {{-- Cart Total --}}
            @if($cartTotal > 0)
                <div class="text-right">
                    <div class="text-sm text-gray-500">Total</div>
                    <div class="text-lg font-bold text-gray-900">${{ number_format($cartTotal, 2) }}</div>
                </div>
            @endif
        </div>
    </div>
</div>


