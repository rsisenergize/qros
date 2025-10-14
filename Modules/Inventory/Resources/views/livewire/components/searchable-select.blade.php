<div class="relative">
    <div class="relative">
        <input
            type="text"
            id="{{ $name }}"
            wire:model.live="search"
            wire:keydown.escape="$set('showDropdown', false)"
            wire:keydown.tab="$set('showDropdown', false)"
            class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-2.5 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
        >
        @if($selectedItem)
            <button 
                wire:click="clearSelection"
                type="button"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-500"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif
    </div>

    @if($showDropdown)
        <div class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 rounded-md shadow-lg">
            <ul class="py-1 overflow-auto text-base leading-6 rounded-md shadow-xs max-h-60 focus:outline-none sm:text-sm sm:leading-5">
                @forelse($searchResults as $item)
                    <li wire:click="selectItem({{ $item->id }})" 
                        class="relative px-3 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                        <div class="flex justify-between">
                            <span class="text-gray-900 dark:text-white">
                                {{ data_get($item, $displayField) }}
                            </span>
                            @if($subField && data_get($item, $subField))
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ data_get($item, $subField) }}
                                </span>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="relative px-3 py-2 text-gray-900 dark:text-white">
                        @lang('app.noResults')
                    </li>
                @endforelse
            </ul>
        </div>
    @endif
</div> 