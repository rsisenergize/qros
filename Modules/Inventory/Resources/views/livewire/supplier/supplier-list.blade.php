<div>
    <div>
        <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
            <div class="w-full mb-1">
                <div class="mb-4">
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('inventory::modules.supplier.suppliers')</h1>
                </div>
                <div class="items-center justify-between block sm:flex">
                    <div class="lg:flex items-center mb-4 sm:mb-0">
                        <form class="sm:pr-3" action="#" method="GET">
                            <label for="suppliers-search" class="sr-only">Search</label>
                            <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                                <x-input id="menu_name" class="block mt-1 w-full" type="text" :placeholder="__('inventory::modules.supplier.searchPlaceholder')" wire:model.live.debounce.500ms="search" />
                            </div>
                        </form>
                    </div>

                    <div class="lg:inline-flex items-center gap-4">
                        <x-button type='button' wire:click="createSupplier">@lang('inventory::modules.supplier.addSupplier')</x-button>
                    </div>
                </div>
            </div>
        </div>

        <livewire:inventory::supplier.supplier-table :search='$search' key='supplier-table-{{ microtime() }}' />
    </div>

    <x-right-modal wire:model.live="showAddSupplierModal">
        <x-slot name="title">
            @lang('inventory::modules.supplier.addSupplier')
        </x-slot>

        <x-slot name="content">
            <livewire:inventory::supplier.add-supplier />
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showAddSupplierModal', false)" wire:loading.attr="disabled">
                @lang('app.close')
            </x-secondary-button>
        </x-slot>
    </x-right-modal>
</div>
