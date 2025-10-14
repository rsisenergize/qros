<div class="min-h-screen bg-white dark:bg-gray-800 py-8">
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-3xl font-bold leading-7 text-gray-900 dark:text-white sm:text-4xl sm:truncate">
                    {{ __('inventory::modules.recipe.title') }}
                </h2>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        {{ $recipes->total() }} {{ __('inventory::modules.recipe.stats.total_recipes') }}
                    </div>
                </div>
            </div>
            <div class="mt-5 flex lg:mt-0 lg:ml-4 space-x-3">

                @if(user_can('Create Recipe'))
                <!-- Add Recipe Button -->
                <x-button  wire:click="$set('showAddRecipe', true)"  type="button" class="inline-flex gap-1 items-center" >
                    <svg class=" h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('inventory::modules.recipe.add_recipe') }}
                </x-button>
                @endif

       
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            @foreach([
                ['label' => __('inventory::modules.recipe.stats.total_recipes'), 'value' => $totalRecipes, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'blue'],
                ['label' => __('inventory::modules.recipe.stats.main_courses'), 'value' => $mainCoursesCount, 'icon' => 'M12 4v16m8-8H4', 'color' => 'green'],
                ['label' => __('inventory::modules.recipe.stats.avg_prep_time'), 'value' => round($avgPrepTime) . ' ' . __('inventory::modules.recipe.preparation_time'), 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'yellow']
            ] as $stat)
                <div class="relative bg-gray-50 dark:bg-gray-700 px-4 py-3 shadow rounded-lg overflow-hidden">
                    <div class="flex items-center">
                        <div class="p-2 rounded-md bg-{{ $stat['color'] }}-500 dark:bg-{{ $stat['color'] }}-600">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}" />
                            </svg>
                        </div>
                        <div class="ml-3 w-full">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ $stat['label'] }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Search and Filters -->
        <div class="my-6 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('inventory::modules.recipe.search_placeholder') }}" 
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                    <div class="absolute left-3 top-2.5">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <select 
                    wire:model.live="category" 
                    class="rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white py-2 px-4 focus:ring-2 focus:ring-indigo-600 focus:border-transparent"
                >
                    <option value="">{{ __('inventory::modules.recipe.filters.all_categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_name }}">{{ $category->getTranslation('category_name', user()->locale) }}</option>
                    @endforeach
                </select>

                <select 
                    wire:model.live="sortBy" 
                    class="rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white py-2 px-4 focus:ring-2 focus:ring-indigo-600 focus:border-transparent"
                >
                    <option value="name">{{ __('inventory::modules.recipe.filters.sort.name') }}</option>
                    <option value="category">{{ __('inventory::modules.recipe.filters.sort.category') }}</option>
                    <option value="prep_time">{{ __('inventory::modules.recipe.filters.sort.prep_time') }}</option>
                </select>
                
                @if($search || $category || $sortBy !== 'menu_items.item_name')
                    <button 
                        wire:click="clearFilters"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        {{ __('inventory::modules.recipe.filters.clear') }}
                    </button>
                @endif
            </div>
        </div>

        <!-- Recipe List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recipes as $recipe)
                    <li class="group hover:bg-gray-100 dark:hover:bg-gray-600/50 transition-colors duration-150">
                        <div class="px-4 py-3 sm:px-6">
                            <div class="flex items-start">
                                <!-- Image -->
                                <div class="flex-shrink-0 h-14 w-14 rounded-lg overflow-hidden">
                                    @if($recipe['menu_item']['image'])
                                        <img class="h-full w-full object-cover" 
                                            src="{{ $recipe['menu_item']['image'] }}" 
                                            alt="{{ $recipe['menu_item']['name'] }}">
                                    @else
                                        <div class="h-full w-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Main Content -->
                                <div class="ml-4 flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-base font-medium text-gray-900 dark:text-white">
                                                {{ $recipe['menu_item']['name'] }}
                                                @if($recipe['menu_item']['variation'])
                                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                                        - {{ $recipe['menu_item']['variation'] }}
                                                    </span>
                                                @endif
                                                @if(isset($recipe['menu_item']['is_modifier_option']) && $recipe['menu_item']['is_modifier_option'])
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                        {{ __('inventory::modules.recipe.modifier_option') }}
                                                    </span>
                                                @endif
                                            </h3>
                                            <div class="flex items-center mt-0.5">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $recipe['menu_item']['category'] }}
                                                </span>
                                                <span class="mx-2 text-gray-300 dark:text-gray-600">·</span>
                                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                    <svg class="flex-shrink-0 mr-1 h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $recipe['menu_item']['preparation_time'] ?? 0 }} {{ __('inventory::modules.recipe.preparation_time') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="flex items-center space-x-2">
                                            @if(user_can('Update Recipe'))
                                            <button 
                                                wire:click="editRecipe({{ $recipe['menu_item']['id'] }}, {{ $recipe['menu_item']['variation_id'] ?? 'null' }}, {{ $recipe['menu_item']['modifier_option_id'] ?? 'null' }})"
                                                class="p-1 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-150"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            @endif

                                            @if(user_can('Delete Recipe'))
                                            <button 
                                                wire:click="showDeleteRecipe({{ $recipe['menu_item']['id'] }}, {{ $recipe['menu_item']['variation_id'] ?? 'null' }}, {{ $recipe['menu_item']['modifier_option_id'] ?? 'null' }})"
                                                class="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-150"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Ingredients Section -->
                                    <div class="mt-2 bg-gray-50 dark:bg-gray-800 rounded-md p-2 border border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center">
                                                <svg class="h-4 w-4 text-gray-500 dark:text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ __('inventory::modules.recipe.ingredients_required') }}
                                                </span>
                                            </div>

                                            <!-- ingredient cost -->
                                            <div class="flex items-center bg-indigo-50 dark:bg-indigo-900/30 px-3 py-1 rounded-full">
                                                <svg class="h-4 w-4 text-indigo-500 dark:text-indigo-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">
                                                    {{ __('inventory::modules.recipe.ingredients_cost') }}:
                                                    <span class="ml-1 font-semibold">
                                                        {{ currency_format($recipe['ingredients_cost'], restaurant()->currency_id) }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($recipe['ingredients'] as $ingredient)
                                                <div class="flex items-center bg-white dark:bg-gray-700 px-2 py-1 rounded-md border border-gray-200 dark:border-gray-600">
                                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                        {{ $ingredient['name'] }}
                                                    </span>
                                                    <span class="ml-1.5 text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                                                        {{ $ingredient['quantity'] }}{{ $ingredient['unit'] }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-8">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('inventory::modules.recipe.no_recipes_found') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('inventory::modules.recipe.get_started') }}
                            </p>
                            <div class="mt-6">
                                <button 
                                    type="button" 
                                    wire:click="$set('showAddRecipe', true)"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900"
                                >
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    {{ __('inventory::modules.recipe.add_recipe') }}
                                </button>
                            </div>
                        </div>
                    </li>
                @endforelse
            </ul>
        </div>

        <!-- Pagination -->
        @if($recipes->hasPages())
            <div class="mt-8">
                {{ $recipes->links() }}
            </div>
        @endif
    </div>

    <x-right-modal wire:model="showAddRecipe">
        <x-slot name="title">
            {{ $isEditing ? __('inventory::modules.recipe.edit_recipe') : __('inventory::modules.recipe.add_recipe') }}
        </x-slot>

        <x-slot name="content">
            <livewire:inventory::recipes.recipe-form />
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model="confirmDeleteRecipe">
        <x-slot name="title">
            @lang('inventory::modules.recipe.delete_recipe')
        </x-slot>

        <x-slot name="content">
            @lang('inventory::modules.recipe.confirm_delete')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmDeleteRecipe', false)" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="deleteRecipe" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
