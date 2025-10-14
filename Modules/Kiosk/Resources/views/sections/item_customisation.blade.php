<!-- 🟡 4. Item Customization Screen -->
<div x-show="currentScreen === 'item-customization'" 
x-cloak
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0 transform translate-x-full"
x-transition:enter-end="opacity-100 transform translate-x-0"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100 transform translate-x-0"
x-transition:leave-end="opacity-0 transform -translate-x-full"
class="min-h-screen flex items-center justify-center bg-white">
<div class="w-full max-w-2xl px-6">
   <!-- Header -->
   <div class="flex items-center justify-between mb-8">
       <button @click="currentScreen = 'menu'" 
               class="text-gray-500 hover:text-gray-700 font-medium text-lg transition-colors duration-200">
           ← Back to Menu
       </button>
       <h1 class="text-2xl font-bold text-gray-900">Customize Your Item</h1>
       <div></div>
   </div>

   <!-- Item Details -->
   <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
       <div class="flex items-center space-x-4">
           <img :src="selectedItem.image" :alt="selectedItem.name" class="w-20 h-20 rounded-lg object-cover">
           <div>
               <h2 class="text-xl font-bold text-gray-900" x-text="selectedItem.name"></h2>
               <p class="text-gray-600" x-text="selectedItem.description"></p>
               <p class="text-lg font-bold text-gray-900" x-text="'$' + selectedItem.price"></p>
           </div>
       </div>
   </div>

   <!-- Customization Options -->
   <div class="space-y-6">
       <!-- Size/Variants -->
       <div x-show="selectedItem.variants && selectedItem.variants.length > 0">
           <h3 class="text-lg font-semibold text-gray-900 mb-4">Choose Size</h3>
           <div class="grid grid-cols-2 gap-4">
               <template x-for="variant in selectedItem.variants" :key="variant.id">
                   <button @click="selectVariant(variant)"
                           :class="{'bg-skin-base text-white': selectedVariant === variant.id, 'bg-gray-100 text-gray-700 hover:bg-gray-200': selectedVariant !== variant.id}"
                           class="p-4 rounded-lg font-medium transition-all duration-200 text-left">
                       <div class="font-bold" x-text="variant.name"></div>
                       <div class="text-sm" x-text="variant.price > 0 ? '+$' + variant.price : 'No extra cost'"></div>
                   </button>
               </template>
           </div>
       </div>

       <!-- Add-ons -->
       <div x-show="selectedItem.addons && selectedItem.addons.length > 0">
           <h3 class="text-lg font-semibold text-gray-900 mb-4">Add-ons</h3>
           <div class="space-y-3">
               <template x-for="addon in selectedItem.addons" :key="addon.id">
                   <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                       <div class="flex items-center space-x-3">
                           <input type="checkbox" 
                                  x-model="addon.selected"
                                  class="w-5 h-5 text-skin-base border-gray-300 rounded focus:ring-skin-base">
                           <div>
                               <div class="font-medium text-gray-900" x-text="addon.name"></div>
                               <div class="text-sm text-gray-500" x-text="'+$' + addon.price"></div>
                           </div>
                       </div>
                   </label>
               </template>
           </div>
       </div>

       <!-- Remove ingredients -->
       <div x-show="selectedItem.removals && selectedItem.removals.length > 0">
           <h3 class="text-lg font-semibold text-gray-900 mb-4">Remove ingredients</h3>
           <div class="space-y-3">
               <template x-for="removal in selectedItem.removals" :key="removal.id">
                   <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                       <div class="flex items-center space-x-3">
                           <input type="checkbox" 
                                  x-model="removal.selected"
                                  class="w-5 h-5 text-skin-base border-gray-300 rounded focus:ring-skin-base">
                           <div class="font-medium text-gray-900" x-text="removal.name"></div>
                       </div>
                   </label>
               </template>
           </div>
       </div>

       <!-- Quantity -->
       <div>
           <h3 class="text-lg font-semibold text-gray-900 mb-4">Quantity</h3>
           <div class="flex items-center justify-center space-x-6">
               <button @click="decreaseQuantity()" 
                       class="w-12 h-12 bg-gray-200 text-gray-700 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors duration-200">
                   <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                   </svg>
               </button>
               <span class="text-3xl font-bold text-gray-900" x-text="itemQuantity"></span>
               <button @click="increaseQuantity()" 
                       class="w-12 h-12 bg-skin-base text-white rounded-full flex items-center justify-center hover:bg-skin-base transition-colors duration-200">
                   <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                   </svg>
               </button>
           </div>
       </div>

       <!-- Total Price -->
       <div class="bg-gray-50 rounded-lg p-4">
           <div class="flex justify-between items-center">
               <span class="text-lg font-semibold text-gray-900">Total Price:</span>
               <span class="text-2xl font-bold text-gray-900" x-text="'$' + totalItemPrice"></span>
           </div>
       </div>

       <!-- Action Buttons -->
       <div class="flex space-x-4">
           <button @click="addToCartFromCustomization()"
                class="flex-1 bg-skin-base text-white py-4 rounded-lg font-bold text-lg hover:bg-skin-base transition-all duration-200">
               Add to Cart
           </button>
           <button @click="currentScreen = 'menu'"
                   class="flex-1 border-2 border-gray-300 text-gray-700 py-4 rounded-lg font-medium text-lg hover:bg-gray-50 transition-colors duration-200">
               Cancel
           </button>
       </div>
   </div>
</div>
</div>