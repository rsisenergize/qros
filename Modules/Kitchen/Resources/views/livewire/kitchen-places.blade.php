<div>

    {{-- The Master doesn't talk, he acts. --}}
    <div>

        <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
            <div class="w-full mb-1">
                <div class="mb-4">
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('kitchen::modules.menu.kitchenPlaces')</h1>
                </div>
                <div
                    class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                    <div class="flex items-center mb-4 sm:mb-0">
                        <form class="sm:pr-3" action="#" method="GET">
                            <label for="products-search" class="sr-only">Search</label>
                            <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                                <x-input id="kitchens" class="block mt-1 w-full" type="text"
                                     placeholder="{{ __('kitchen::placeholders.kitchens') }}"
                                    wire:model.live="search" />
                            </div>
                        </form>


                    </div>

                    @if (user_can('Create Kitchen Place'))
                    <x-button type='button' wire:click="$set('showAddkitchenPlaces', true)">
                        @lang ('kitchen::modules.menu.addKitchenPlaces')
                    </x-button>
                    @endif

                </div>
            </div>
        </div>

        <div class="p-4 ">
            @livewire('all-kitchens', ['search' => $search], key('kitchen-' . microtime()))
        </div>



        <!-- Product Drawer -->
        <x-right-modal wire:model.live="showAddkitchenPlaces">
            <x-slot name="title">
                {{ __('kitchen::modules.menu.kitchenPlaces') }}
            </x-slot>

            <x-slot name="content">
                @livewire('add-kitchen')
            </x-slot>
        </x-right-modal>

    </div>


</div>
