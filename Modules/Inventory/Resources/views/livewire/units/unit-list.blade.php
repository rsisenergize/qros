<div>
    <div>

        <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
            <div class="w-full mb-1">
                <div class="mb-4">
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('inventory::modules.menu.units')</h1>
                </div>
                <div class="items-center justify-between block sm:flex ">
                    <div class="lg:flex items-center mb-4 sm:mb-0">
                        <form class="sm:pr-3" action="#" method="GET">
                            <label for="products-search" class="sr-only">Search</label>
                            <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                                <x-input id="menu_name" class="block mt-1 w-full" type="text" placeholder="{{ __('inventory::placeholders.searchUnit') }}" wire:model.live.debounce.500ms="search"  />
                            </div>
                        </form>
                    </div>

                    @if(user_can('Create Unit'))
                    <div class="lg:inline-flex items-center gap-4">
                        <x-button type='button' wire:click="$set('showAddUnit', true)" >@lang('inventory::modules.unit.addUnit')</x-button>            
                    </div>
                    @endif

                </div>


            </div>

        </div>

        <livewire:inventory::units.unit-table :search='$search' key='unit-table-{{ microtime() }}' />


    </div>

    
    <x-right-modal wire:model.live="showAddUnit">
        <x-slot name="title">
            @lang("inventory::modules.unit.addUnit")
        </x-slot>

        <x-slot name="content">
            <livewire:inventory::units.add-unit />
        </x-slot>
    </x-right-modal>

</div>
