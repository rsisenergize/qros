<div>

    <!-- 🟡 7. Order Confirmation -->
    <div  
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-x-full"
        x-transition:enter-end="opacity-100 transform translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform -translate-x-full"
        class="min-h-screen flex items-center justify-center bg-white"
       >
        <div class="w-full max-w-2xl px-6 text-center">
            <!-- Success Animation -->
            <div class="mb-12">
                <div class="w-32 h-32 bg-skin-base rounded-full mx-auto mb-6 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            
            <h1 class="text-5xl font-bold text-gray-900 mb-6">{{ __('kiosk::modules.confirmation.title') }}</h1>
            <p class="text-2xl text-gray-600 mb-12">
                {{ __('kiosk::modules.confirmation.order_number_prefix') }}
                <span class="font-bold text-skin-base ml-2">{{ $order->show_formatted_order_number }}</span>
            </p>

            <!-- Order Details -->
            <div class="bg-white border border-gray-200 rounded-lg p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('kiosk::modules.confirmation.order_details') }}</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-lg text-gray-600">{{ __('kiosk::modules.confirmation.order_type') }}</span>
                        <span class="font-bold text-lg text-gray-900" >{{ __('modules.order.' . $order->order_type) }}</span>
                    </div>
                    
                    @if ($order->order_type === 'dine_in' && $order->table)
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-lg text-gray-600">{{ __('kiosk::modules.confirmation.table_number') }}</span>
                        <span class="font-bold text-lg text-gray-900" >{{ $order->table->table_code }}</span>
                    </div>                        
                    @endif
                
                    @if ($order->order_type === 'pickup' && $order->pickup_date)
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-lg text-gray-600">{{ __('kiosk::modules.confirmation.estimated_pickup') }}</span>
                        <span class="font-bold text-lg text-gray-900" >{{ \Carbon\Carbon::parse($order->pickup_date)->timezone(timezone())->translatedFormat('M d, Y h:i A') }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between items-center py-3">
                        <span class="text-lg text-gray-600">{{ __('kiosk::modules.confirmation.total_amount') }}</span>
                        <span class="font-bold text-3xl text-skin-base">{{ currency_format($order->total, $restaurant->currency_id) }}</span>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            

            <!-- Action Button -->
            <a href="{{ route('kiosk.restaurant', $restaurant->hash).'?branch=' . $shopBranch->unique_hash }}"
                class="w-full bg-skin-base text-white py-6 px-8 rounded-lg font-bold text-2xl transition-all duration-200 hover:bg-skin-base flex items-center justify-center">
                {{ __('kiosk::modules.confirmation.start_new_order') }}
            </a>
        </div>
    </div>

</div>
