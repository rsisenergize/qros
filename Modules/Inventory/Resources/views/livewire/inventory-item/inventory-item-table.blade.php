<div class="flex flex-col">
    <div class="overflow-x-auto">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('inventory::modules.inventoryItem.name')
                            </th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('inventory::modules.inventoryItem.category')
                            </th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('inventory::modules.inventoryItem.unit')
                            </th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('inventory::modules.inventoryItem.thresholdQuantity')
                            </th>
                            <th scope="col" class="p-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                @lang('app.action')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($inventoryItems as $item)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item->name }}
                                </td>
                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item->category?->name ?? '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item->unit->name }} ({{ $item->unit->symbol }})
                                </td>
                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item->threshold_quantity }}
                                </td>
                                <td class="p-4 space-x-2 whitespace-nowrap text-right rtl:space-x-reverse">
                                    @if(user_can('Update Inventory Item'))
                                    <x-secondary-button-table wire:click='showEditInventoryItem({{ $item->id }})' wire:key='inventory-item-edit-{{ $item->id . microtime() }}'>
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                            </path>
                                            <path fill-rule="evenodd"
                                                d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        @lang('app.update')
                                    </x-secondary-button-table>
                                    @endif

                                    @if(user_can('Delete Inventory Item'))
                                    <x-danger-button-table wire:click="showDeleteInventoryItem({{ $item->id }})"  wire:key='inventory-item-del-{{ $item->id . microtime() }}'>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </x-danger-button-table>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                    @lang('inventory::modules.inventoryItem.noInventoryItemFound')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div wire:key='inventory-item-table-paginate-{{ microtime() }}'
    class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center mb-4 sm:mb-0 w-full">
            {{ $inventoryItems->links() }}
        </div>
    </div>


    <x-confirmation-modal wire:model="showDeleteInventoryItemModal">
        <x-slot name="title">
            @lang('inventory::modules.inventoryItem.deleteInventoryItem')?
        </x-slot>

        <x-slot name="content">
            @lang('inventory::modules.inventoryItem.deleteInventoryItemMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('showDeleteInventoryItemModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            @if ($inventoryItem)
            <x-danger-button class="ml-3" wire:click='deleteInventoryItem({{ $inventoryItem->id }})' wire:loading.attr="disabled">
                {{ __('app.delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>



    <x-right-modal wire:model.live="showEditInventoryItemModal">
        <x-slot name="title">
            {{ __("inventory::modules.inventoryItem.editInventoryItem") }}
        </x-slot>

        <x-slot name="content">
            @if ($inventoryItem)
            @livewire('inventory::inventory-item.edit-inventory-item', ['inventoryItem' => $inventoryItem], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditInventoryItemModal', false)" wire:loading.attr="disabled">
                {{ __('app.close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>
    
</div>