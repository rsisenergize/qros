<div>
    <!-- 🟡 1. Welcome Screen -->
    <div x-show="currentScreen === 'welcome'" 
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="min-h-screen flex items-center justify-center bg-white">
        <div class="w-full max-w-lg text-center px-8">
            <!-- Logo/Brand -->
            <div class="mb-12">
                <div class="w-28 h-28 rounded-lg mx-auto mb-6 flex items-center justify-center">
                    <img src="{{  $restaurant->logo_url }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ __('kiosk::modules.welcome.title', ['name' => $restaurant->name]) }}</h1>
            </div>

            <!-- Start Button -->
            <button @click="startOrder()" 
                    class="w-full bg-skin-base text-white py-6 px-8 rounded-lg font-bold text-2xl transition-all duration-200 hover:bg-skin-base/80 active:scale-95">
                {{ __('kiosk::modules.welcome.start_order') }}
            </button>
        </div>
    </div>
</div>
