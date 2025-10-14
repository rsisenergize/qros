<form wire:submit="save" class="space-y-6">
    <!-- Recipe Type Selection -->
    @if(!$isEditing)
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('inventory::modules.recipe.recipe_type') }}
            </label>
            <div class="mt-2 space-y-2">
                <label class="flex items-center">
                    <input type="radio" wire:model.live="recipeType" value="menu_item" class="mr-2" {{ $recipeType === 'menu_item' ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('inventory::modules.recipe.menu_item') }}</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" wire:model.live="recipeType" value="modifier_option" class="mr-2" {{ $recipeType === 'modifier_option' ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('inventory::modules.recipe.modifier_option') }}</span>
                </label>
            </div>
            @error('recipeType') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
        </div>
    @endif

    <!-- Menu Item Selection -->
    @if($recipeType === 'menu_item' || $isEditing)
        <div>
            <label for="menuItemId" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('inventory::modules.recipe.menu_item') }}
            </label>
            @if($isEditing && $recipeType === 'menu_item')
                <div class="mt-1 block w-full py-2 text-sm text-gray-700 dark:text-gray-300">
                    {{ $availableMenuItems->first()->item_name }}
                </div>
                <input type="hidden" wire:model="menuItemId">
            @else
                <select 
                    wire:model.live="menuItemId"
                    id="menuItemId"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                >
                    <option value="">{{ __('inventory::modules.recipe.select_menu_item') }}</option>
                    @foreach($availableMenuItems as $menuItem)
                        <option value="{{ $menuItem->id }}">{{ $menuItem->item_name }}</option>
                    @endforeach
                </select>
            @endif
            @error('menuItemId') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
        </div>
    @endif

    <!-- Modifier Option Selection -->
    @if($recipeType === 'modifier_option' || $isEditing)
        <div>
            <label for="selectedModifierOptionId" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('inventory::modules.recipe.modifier_option') }}
            </label>
            @if($isEditing && $recipeType === 'modifier_option')
                <div class="mt-1 block w-full py-2 text-sm text-gray-700 dark:text-gray-300">
                    {{ $availableModifierOptions->first()->name }}
                </div>
                <input type="hidden" wire:model="selectedModifierOptionId">
            @else
                <select 
                    wire:model.live="selectedModifierOptionId"
                    id="selectedModifierOptionId"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                >
                    <option value="">{{ __('inventory::modules.recipe.select_modifier_option') }}</option>
                    @foreach($availableModifierOptions as $modifierOption)
                        <option value="{{ $modifierOption->id }}">{{ $modifierOption->name }} ({{ $modifierOption->modifierGroup->name ?? 'Modifier' }})</option>
                    @endforeach
                </select>
            @endif
            @error('selectedModifierOptionId') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
        </div>
    @endif

    <!-- Variation Selection -->
    @if($recipeType === 'menu_item' && $hasVariations)
        <div>
            <label for="selectedVariationId" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('inventory::modules.recipe.variation') }}
            </label>
            <select 
                wire:model.live="selectedVariationId"
                id="selectedVariationId"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            >
                <option value="">{{ __('inventory::modules.recipe.select_variation') }}</option>
                @foreach($menuItemVariations as $variation)
                    <option value="{{ $variation->id }}">{{ $variation->variation }}</option>
                @endforeach
            </select>
            @error('selectedVariationId') <span class="mt-1 text-sm text-red-600">{{ $message }}</span> @enderror
        </div>
    @elseif($recipeType === 'menu_item' && $menuItemId && $menuItemVariations->count() == 0)
        <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-md p-3">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        {{ __('inventory::modules.recipe.all_variations_have_recipes') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Ingredients -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ __('inventory::modules.recipe.ingredients') }}
            </h3>
            <x-secondary-button 
                type="button"
                wire:click="addIngredient"
                class="inline-flex items-center"
            >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('inventory::modules.recipe.add_ingredient') }}
            </x-secondary-button>
        </div>

        @foreach($ingredients as $index => $ingredient)
            <div class="flex items-end gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <!-- Ingredient Selection -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.recipe.ingredient') }}
                    </label>
                    <select 
                        wire:model.live="ingredients.{{ $index }}.inventory_item_id"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    >
                        <option value="">{{ __('inventory::modules.recipe.select_ingredient') }}</option>
                        @foreach($inventoryItemsWithUnits as $item)
                            <option value="{{ $item['id'] }}">{{ $item['name'] }} ({{ $item['unit_symbol'] }})</option>
                        @endforeach
                    </select>
                    @error("ingredients.{$index}.inventory_item_id") 
                        <span class="mt-1 text-sm text-red-600">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Quantity -->
                <div class="w-32">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.recipe.quantity') }}
                    </label>
                    <input 
                        type="number"
                        wire:model="ingredients.{{ $index }}.quantity"
                        step="0.01"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    >
                    @error("ingredients.{$index}.quantity") 
                        <span class="mt-1 text-sm text-red-600">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Unit (Hidden but still part of the form data) -->
                <input type="hidden" wire:model="ingredients.{{ $index }}.unit_id">

                <!-- Unit Display -->
                <div class="w-32">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.recipe.unit') }}
                    </label>
                    <div class="mt-1 block w-full py-2 text-sm text-gray-700 dark:text-gray-300">
                        @if(isset($ingredients[$index]['inventory_item_id']))
                            {{ $inventoryItemsWithUnits->firstWhere('id', $ingredients[$index]['inventory_item_id'])['unit_symbol'] ?? '' }}
                        @endif
                    </div>
                </div>

                <!-- Remove Button -->
                @if(count($ingredients) > 1)
                    <button 
                        type="button"
                        wire:click="removeIngredient({{ $index }})"
                        class="inline-flex items-center p-2 border border-transparent rounded-full text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Save Button -->
    <div class="mt-6 flex justify-end gap-3">
        <x-secondary-button wire:click="$dispatch('closeAddRecipeModal')">
            {{ __('Cancel') }}
        </x-secondary-button>
        <x-button type="submit">
            {{ $isEditing ? __('Update') : __('Save') }}
        </x-button>
    </div>
</form>