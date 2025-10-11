<div>
    <div class="flex flex-col">
        <div class="flex gap-4 mb-4">
            <img class="w-16 h-16 rounded-md  object-cover" src="{{ $menuItem->item_photo_url }}" alt="{{ $menuItem->item_name }}">
            <div class="text-sm font-normal text-gray-500 dark:text-gray-400">
                <div class="text-base font-semibold text-gray-900 dark:text-white inline-flex items-center">
                    <img src="{{ asset('img/'.$menuItem->type.'.svg')}}" class="h-4 mr-2"
                        title="@lang('modules.menu.' . $menuItem->type)" alt="" />
                    {{ $menuItem->item_name }}
                </div>
                <div class="text-sm font-normal text-gray-500 dark:text-gray-400">{{
                    $menuItem->description }}</div>
            </div>
        </div>

        <div class="w-full space-y-4">
            <!-- Table Headers for desktop -->
            <div class="hidden md:grid grid-cols-3 gap-4 px-4 py-2 bg-gray-100 dark:bg-gray-800 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase rounded-md">
                <div>@lang('modules.menu.itemName')</div>
                <div class="text-center">@lang('modules.menu.setPrice')</div>
                <div class="text-right">@lang('app.action')</div>
            </div>

            @foreach ($menuItem->variations as $item)
                <div
                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:shadow-sm p-3 transition"
                    wire:key="menu-item-{{ $item->id . microtime() }}">

                    <div class="md:hidden mb-2 space-y-1 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        <div>@lang('modules.menu.itemName')</div>
                    </div>

                    <!-- Content Grid -->
                    <div class="grid md:grid-cols-3 gap-4 items-center">
                        <div class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ $item->variation }}
                        </div>

                        <!-- Price + Button Row for Mobile -->
                        <div class="md:hidden flex items-center justify-between gap-4">
                            <div class="text-sm text-gray-800 dark:text-white">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">@lang('modules.menu.setPrice')</div>
                                {{ $item->price ? currency_format($item->price, $item->menuItem->branch->restaurant->currency_id) : '--' }}
                            </div>
                            <x-button
                                wire:click="setItemVariation({{ $item->id }})"
                                wire:key="del-var-btn-{{ $item->id }}"
                                class="text-sm">
                                @lang('modules.order.select')
                            </x-button>
                        </div>

                        <!-- Desktop Only Price -->
                        <div class="hidden md:block text-center text-sm text-gray-900 dark:text-white">
                            {{ $item->price ? currency_format($item->price, $item->menuItem->branch->restaurant->currency_id) : '--' }}
                        </div>

                        <!-- Desktop Only Button -->
                        <div class="hidden md:flex justify-end">
                            <x-button
                                wire:click="setItemVariation({{ $item->id }})"
                                wire:key="del-var-btn-{{ $item->id }}"
                                class="text-sm w-full md:w-auto">
                                @lang('modules.order.select')
                            </x-button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

</div>
