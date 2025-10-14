<div>

    <div class="space-y-4">
        <form wire:submit.prevent="addItems">
            @csrf
            <div>
                <x-label for="selected_items" :value="__('kitchen::modules.menu.selectItems')" class="text-gray-700 dark:text-gray-200" />

                <!-- Search Input -->
                <div class="mt-2">
                    <x-input type="text" placeholder="{{ __('search') }}" wire:model.live="search" class="w-full" />

                </div>
                <!-- Items Table -->
                <div
                    class="overflow-x-auto w-full transition-all duration-300 ease-in-out mt-2 border border-gray-300 dark:border-gray-600 rounded-md max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    {{ __('kitchen::modules.menu.itemName') }}
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                    {{ __('app.select') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @forelse($items as $item)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="py-2.5 px-4 text-sm text-gray-900 dark:text-white">
                                        {{ $item->item_name }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right">
                                        <x-checkbox id="item{{ $item->id }}" name="selected_items[]"
                                            wire:model="selectedItems" :value="$item->id" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-2.5 px-4 text-sm text-gray-500 text-center">
                                        {{ __('kitchen::modules.menu.noDataFound') }}

                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="flex space-x-4 mt-6">
                    <x-button type="submit" class="bg-primary-600 text-white dark:bg-primary-500 dark:text-gray-100">
                        {{ __('kitchen::modules.menu.add') }}
                    </x-button>
                    <x-button-cancel wire:click="$dispatch('hideItemToKitchen')"
                        class="dark:bg-gray-700 dark:text-gray-200">
                        {{ __('kitchen::modules.menu.cancel') }}
                    </x-button-cancel>
                </div>
        </form>

        <x-input-error for="selectedItems" class="mt-2" />
    </div>

    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">
            {{ __('kitchen::modules.menu.removeItems') }}
        </h3>
        <form wire:submit.prevent="removeItems">
            @csrf
            <div
                class="overflow-x-auto w-full transition-all duration-300 ease-in-out mt-2 border border-gray-300 dark:border-gray-600 rounded-md max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                {{ __('kitchen::modules.menu.itemName') }}
                            </th>
                            <th
                                class="py-2.5 px-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                {{ __('app.select') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($fetchedItems as $item)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="py-2.5 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $item->item_name }}
                                </td>
                                <td class="py-2.5 px-4 text-right">
                                    <button type="button" wire:click="toggleItemStatus({{ $item->id }})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-2.5 px-4 text-sm text-gray-500 text-center">
                                    {{ __('kitchen::modules.menu.noDataFound') }}
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </form>
    </div>

</div>
</div>
