<div>
    <!-- Search Section -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex items-center space-x-4">
            <div class="flex-1">
                <x-label for="searchItem" :value="__('kitchen::modules.menu.searchItems')" class="sr-only" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <x-input
                        id="searchItem"
                        type="text"
                        wire:model.live="searchItem"
                        class="pl-10 w-full"
                        :placeholder="__('kitchen::modules.menu.searchItemsPlaceholder')"
                    />
                </div>
            </div>
            @if($searchItem)
                <x-secondary-button wire:click="clearSearch" class="text-sm">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    {{ __('kitchen::modules.menu.clear') }}
                </x-secondary-button>
            @endif
        </div>

        @if($searchItem)
            <div class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                @if($searchResults->isNotEmpty())
                    <span class="text-green-600 dark:text-green-400">
                        {{ __('kitchen::modules.menu.foundItems', ['count' => $searchResults->count(), 'search' => $searchItem]) }}
                    </span>
                @else
                    <span class="text-red-600 dark:text-red-400">
                        {{ __('kitchen::modules.menu.noItemsFound', ['search' => $searchItem]) }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Kitchens Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach ($kitchens as $kitchen)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300
                @if($searchItem && $kitchen->menuitems->whereIn('id', $searchResults->pluck('id'))->isNotEmpty())
                    ring-2 ring-blue-500 ring-opacity-50 shadow-lg
                @endif">
                <!-- Kitchen Header -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 p-4 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-medium text-gray-900 dark:text-white">{{ $kitchen->name }}</h3>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ Str::title($kitchen->type) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <!-- Status Toggle -->
                            <div class="flex items-center">
                                <input type="checkbox"
                                    id="isActive-{{ $kitchen->id }}"
                                    wire:click="showKitchenStatusPopup({{ $kitchen->id }})"
                                    @if ($kitchen->is_active) checked @endif
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <label for="isActive-{{ $kitchen->id }}" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                    @lang('app.active')
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Kitchen Info -->
                    <div class="mt-3 flex items-center justify-between text-gray-500 dark:text-gray-400 text-xs">
                        <div class="flex items-center space-x-4">
                            <span class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $kitchen->printerSetting?->name ?? '--' }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $kitchen->menuitems->count() }} {{ __('kitchen::modules.menu.items') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Kitchen Items Section -->
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('kitchen::modules.menu.kitchenItems') }}
                        </h4>
                        <x-secondary-button wire:click="addItemToKitchen({{ $kitchen->id }})" class="text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            {{ __('kitchen::modules.menu.addItem') }}
                        </x-secondary-button>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @php
                            $kitchenItems = $kitchen->menuitems;
                            if ($searchItem) {
                                // Sort items: matching items first, then others
                                $matchingItems = $kitchenItems->filter(function($item) {
                                    return stripos($item->item_name, $this->searchItem) !== false;
                                });
                                $nonMatchingItems = $kitchenItems->filter(function($item) {
                                    return stripos($item->item_name, $this->searchItem) === false;
                                });
                                $kitchenItems = $matchingItems->concat($nonMatchingItems);
                            }
                        @endphp

                        @forelse($kitchenItems as $item)
                            <div class="flex items-center justify-between p-2 rounded-md transition-colors
                                @if($searchItem && stripos($item->item_name, $searchItem) !== false)
                                    bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700
                                @else
                                    bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600
                                @endif">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 rounded-full
                                        @if($item->type === 'veg') bg-green-400
                                        @elseif($item->type === 'non-veg') bg-red-400
                                        @else bg-yellow-400 @endif">
                                    </div>
                                    <span class="text-sm text-gray-700 dark:text-gray-300
                                        @if($searchItem && stripos($item->item_name, $searchItem) !== false)
                                            font-semibold text-blue-900 dark:text-blue-100
                                        @endif">
                                        {{ $item->item_name }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($item->variations && $item->variations->count() > 0)
                                        <span class="text-xs bg-purple-100 dark:bg-purple-800 text-purple-700 dark:text-purple-300 px-2 py-1 rounded-full">
                                            {{ __('kitchen::modules.menu.hasVariations') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ currency_format($item->price, restaurant()->currency_id) }}
                                        </span>
                                    @endif
                                    <button wire:click="removeItemFromKitchen({{ $item->id }})"
                                        class="text-gray-400 hover:text-red-500 p-1 transition-colors">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-400 dark:text-gray-500">
                                <svg class="w-6 h-6 mx-auto mb-2 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm">{{ __('kitchen::modules.menu.noItemsInKitchen') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Kitchen Actions -->
                <div class="px-4 pb-4 flex items-center pt-2 justify-between border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center space-x-2">
                        @if (user_can('Update Kitchen Place'))
                        <x-secondary-button wire:click="showEditKitchen({{ $kitchen->id }})" class="text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 px-3 py-1.5">
                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                                <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path>
                            </svg>
                            {{ __('kitchen::modules.menu.edit') }}
                        </x-secondary-button>
                        @endif
                    </div>

                    @if(!$kitchen->is_default)
                        @if (user_can('Delete Kitchen Place'))
                        <x-danger-button wire:click="confirmDeleteKitchenPlaces({{ $kitchen->id }})" class="text-xs px-2 py-1.5">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </x-danger-button>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if($kitchens->isEmpty())
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                {{ __('kitchen::modules.menu.noKitchensFound') }}
            </h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
                {{ __('kitchen::modules.menu.createFirstKitchen') }}
            </p>
            @if (user_can('Create Kitchen Place'))
            <x-button wire:click="$set('showAddkitchenPlaces', true)" class="bg-gray-600 hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                {{ __('kitchen::modules.menu.addKitchen') }}
            </x-button>
            @endif
        </div>
    @endif

    <!-- Missing Menu Items Section -->
    @if($missingItems->isNotEmpty())
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden
            @if($searchItem && $missingItems->whereIn('id', $searchResults->pluck('id'))->isNotEmpty())
                ring-2 ring-amber-500 ring-opacity-50 shadow-lg
            @endif">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 p-4 border-b border-amber-200 dark:border-amber-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-amber-100 dark:bg-amber-800 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-medium text-gray-900 dark:text-white">{{ __('kitchen::modules.menu.unassignedItems') }}</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $missingItems->count() }} {{ __('kitchen::modules.menu.itemsNotAssigned') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @php
                        $unassignedItems = $missingItems;
                        if ($searchItem) {
                            // Sort items: matching items first, then others
                            $matchingUnassignedItems = $unassignedItems->filter(function($item) {
                                return stripos($item->item_name, $this->searchItem) !== false;
                            });
                            $nonMatchingUnassignedItems = $unassignedItems->filter(function($item) {
                                return stripos($item->item_name, $this->searchItem) === false;
                            });
                            $unassignedItems = $matchingUnassignedItems->concat($nonMatchingUnassignedItems);
                        }
                    @endphp

                    @foreach($unassignedItems as $item)
                        <div class="flex items-center justify-between p-3 rounded-md transition-colors
                            @if($searchItem && stripos($item->item_name, $searchItem) !== false)
                                bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700
                            @else
                                bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600
                            @endif">
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 rounded-full
                                    @if($item->type === 'veg') bg-green-400
                                    @elseif($item->type === 'non-veg') bg-red-400
                                    @else bg-yellow-400 @endif">
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300
                                    @if($searchItem && stripos($item->item_name, $searchItem) !== false)
                                        font-semibold text-amber-900 dark:text-amber-100
                                    @endif">
                                    {{ $item->item_name }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($item->variations && $item->variations->count() > 0)
                                    <span class="text-xs bg-purple-100 dark:bg-purple-800 text-purple-700 dark:text-purple-300 px-2 py-1 rounded-full">
                                        {{ __('kitchen::modules.menu.hasVariations') }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ currency_format($item->price, restaurant()->currency_id) }}
                                    </span>
                                @endif
                                <x-secondary-button wire:click="assignItemToKitchen({{ $item->id }})" class="text-xs bg-amber-100 hover:bg-amber-200 dark:bg-amber-800 dark:hover:bg-amber-700 text-amber-700 dark:text-amber-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('kitchen::modules.menu.assign') }}
                                </x-secondary-button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Pagination -->
    @if($kitchens->hasPages())
        <div class="mt-6">
            {{ $kitchens->links() }}
        </div>
    @endif

    <!-- Modals -->
    <x-right-modal wire:model="showAddkitchenPlaces">
        <x-slot name="title">
            {{ __('kitchen::modules.menu.kitchenPlaces') }}
        </x-slot>

        <x-slot name="content">
            @livewire('kitchen::forms.add-kitchen')
        </x-slot>
    </x-right-modal>

    <x-right-modal wire:model="showEditKitchenModal">
        <x-slot name="title">
            {{ __('kitchen::modules.menu.editKitchenPlaces') }}
        </x-slot>

        <x-slot name="content">
            @if ($selectedKitchen)
                @livewire('edit-kitchen', ['kitchen' => $selectedKitchen], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
        </x-slot>
    </x-right-modal>

    <x-dialog-modal wire:model="showAddItemModal">
        <x-slot name="title">
            {{ __('kitchen::modules.menu.manageItems') }}
        </x-slot>

        <x-slot name="content">
            @if ($selectedKitchenId)
                @livewire('add-item-to-kitchen', ['kitchenId' => $selectedKitchenId], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model="showAssignItemModal">
        <x-slot name="title">
            {{ __('kitchen::modules.menu.assignItemToKitchen') }}
        </x-slot>

        <x-slot name="content">
            @if ($itemToAssign)
                <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-md">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 rounded-full
                                @if($itemToAssign->type === 'veg') bg-green-400
                                @elseif($itemToAssign->type === 'non-veg') bg-red-400
                                @else bg-yellow-400 @endif">
                            </div>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $itemToAssign->item_name }}</span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ currency_format($itemToAssign->price, restaurant()->currency_id) }}
                        </p>
                    </div>

                    <div>
                        <x-label for="selectedKitchenForAssignment" :value="__('kitchen::modules.menu.selectKitchen')" />
                        <x-select id="selectedKitchenForAssignment" class="mt-1 block w-full" wire:model="selectedKitchenForAssignment">
                            <option value="">{{ __('kitchen::modules.menu.selectKitchenPlaceholder') }}</option>
                            @foreach($allKitchens as $kitchen)
                                <option value="{{ $kitchen->id }}">{{ $kitchen->name }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error for="selectedKitchenForAssignment" class="mt-2" />
                    </div>
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('showAssignItemModal')" wire:loading.attr="disabled">
                {{ __('kitchen::modules.menu.cancel') }}
            </x-secondary-button>

            <x-button wire:click="confirmAssignItem" wire:loading.attr="disabled" class="ml-3">
                {{ __('kitchen::modules.menu.assign') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    <x-confirmation-modal wire:model="confirmDeleteKitchenPlacesModal">
        <x-slot name="title">
            @lang('kitchen::modules.menu.deleteKitchen')?
        </x-slot>

        <x-slot name="content">
            @lang('kitchen::modules.menu.deleteKitchenPlacesMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteKitchenPlacesModal')" wire:loading.attr="disabled">
                {{ __('kitchen::modules.menu.cancel') }}
            </x-secondary-button>

            @if ($deleteKitchenPlaces)
                <x-danger-button class="ml-3" wire:click='deleteKitchenPlace' wire:loading.attr="disabled">
                    {{ __('kitchen::modules.menu.delete') }}
                </x-danger-button>
            @endif
        </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal wire:model="showKitchenStatusModal">
        <x-slot name="title">
            @lang('kitchen::modules.menu.changeStatus')?
        </x-slot>

        <x-slot name="content">
            @lang('kitchen::messages.statusChangeMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('showKitchenStatusModal')" wire:loading.attr="disabled">
                {{ __('kitchen::modules.menu.cancel') }}
            </x-secondary-button>

            @if ($selectedKitchenId)
                <x-danger-button class="ml-3" wire:click='toggleKitchenStatus' wire:loading.attr="disabled">
                    {{ __('kitchen::modules.menu.changeStatus') }}
                </x-danger-button>
            @endif
        </x-slot>
    </x-confirmation-modal>
</div>
