<div>

    <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('modules.menu.allMenuItems')</h1>
            </div>
            <div class="items-center justify-between block sm:flex ">
                <div class="flex items-center mb-4 sm:mb-0">
                    <form class="ltr:pr-3 rtl:pl-3 " action="#" method="GET">
                        <label for="products-search" class="sr-only">Search</label>
                        <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                            <x-input id="menu_name" class="block mt-1 w-full" type="text" placeholder="{{ __('placeholders.searchMenuItems') }}" wire:model.live.debounce.500ms="search"  />
                        </div>
                    </form>

                    <x-secondary-button wire:click="$dispatch('showMenuItemFilters')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filter mr-1" viewBox="0 0 16 16">
                            <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
                        </svg> @lang('app.showFilter')
                    </x-secondary-button>
                </div>

                <div class="inline-flex gap-x-4 mb-4 sm:mb-0">
                    <x-secondary-link href="{{ route('menu-items.entities.sort') }}">
                        @lang('modules.menu.sortMenuItems')
                    </x-secondary-link>

                    @if(user_can('Create Menu Item'))
                    <x-secondary-link href="{{ route('menu-items.bulk-import') }}" wire:navigate>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload mr-1" viewBox="0 0 16 16">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                        </svg>
                        {{ __('modules.menu.bulkUpload') }}
                    </x-secondary-link>

                    <x-button type='button' wire:click="$set('showAddMenuItem', true)" >@lang('modules.menu.addMenuItem')</x-button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <livewire:menu.menu-items :search='$search' key='menu-item-{{ microtime() }}' />


    <!-- Product Drawer -->
    <x-right-modal wire:model.live="showAddMenuItem">
        <x-slot name="title">
            {{ __("modules.menu.addMenuItem") }}
        </x-slot>

        <x-slot name="content">
            @livewire('forms.addMenuItem')
        </x-slot>
    </x-right-modal>

</div>
