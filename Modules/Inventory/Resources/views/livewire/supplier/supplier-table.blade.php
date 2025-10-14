<div>
    <!-- Table -->
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">@lang('inventory::modules.supplier.name')</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">@lang('inventory::modules.supplier.email')</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">@lang('inventory::modules.supplier.phone')</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">@lang('inventory::modules.supplier.address')</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">@lang('app.actions')</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($suppliers as $item)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='supplier-{{ $item->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        <a href="{{ route('suppliers.show', $item->id) }}" class="underline underline-offset-2" wire:navigate>
                                            {{ $item->name }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $item->email }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $item->phone }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $item->address }}
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 space-x-2">
                                        <x-secondary-button wire:click="editSupplier({{ $item->id }})" wire:key='edit-supplier-{{ $item->id . rand(1111, 9999) . microtime() }}' class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                <path d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 012 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                                            </svg>
                                            @lang('app.update')
                                        </x-secondary-button>

                                        @if($item->orders_count == 0)
                                        <x-danger-button-table wire:click="deleteSupplier({{ $item->id }})" wire:key='delete-supplier-{{ $item->id . rand(1111, 9999) . microtime() }}'>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </x-danger-button-table>
                                        @else
                                        <p class="text-gray-500 text-sm">
                                            @lang('inventory::modules.supplier.supplierHasOrders', ['count' => $item->orders_count])
                                        </p>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        @lang('inventory::modules.supplier.noSuppliersFound')
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $suppliers->links() }}
    </div>

    <x-right-modal wire:model.live="showEditSupplierModal">
        <x-slot name="title">
            @lang('inventory::modules.supplier.editSupplier')
        </x-slot>

        <x-slot name="content">
            @if ($supplier)
            <livewire:inventory::supplier.edit-supplier :supplier="$supplier" wire:key='edit-supplier-{{ $supplier->id . rand(1111, 9999) . microtime() }}' />
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditSupplierModal', false)" wire:loading.attr="disabled">
                @lang('app.close')
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model="confirmDeleteSupplierModal">
        <x-slot name="title">
            @lang('inventory::modules.supplier.deleteSupplier')
        </x-slot>

        <x-slot name="content">
            @lang('inventory::modules.supplier.deleteSupplierMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteSupplierModal')" wire:loading.attr="disabled">
                @lang('app.cancel')
            </x-secondary-button>

            @if ($supplier)
            <x-danger-button class="ml-3" wire:click='deleteSupplier({{ $supplier->id }})' wire:loading.attr="disabled" wire:key='delete-supplier-{{ $supplier->id . rand(1111, 9999) . microtime() }}'>
                @lang('app.delete')
            </x-danger-button>
            @endif
        </x-slot>
    </x-confirmation-modal>
</div>
