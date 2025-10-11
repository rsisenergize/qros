<div class="max-h-screen flex flex-col bg-gray-50 dark:bg-gray-900">
    {{-- Optimized SVG as a component --}}
    @php
        $dragHandle = '<svg class="w-5 h-5" viewBox="0 0 48 48"><g fill="currentColor"><circle cx="18" cy="12" r="4"/><circle cx="18" cy="24" r="4"/><circle cx="18" cy="36" r="4"/><circle cx="30" cy="12" r="4"/><circle cx="30" cy="24" r="4"/><circle cx="30" cy="36" r="4"/></g></svg>';
    @endphp

    {{-- Header with responsive adjustments --}}
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 py-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mx-4 sm:mx-7 space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">@lang('modules.menu.sortManager')</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">@lang('modules.menu.sortManagerInfoMessage')</p>
            </div>
            <div class="flex items-center w-full sm:w-auto gap-4">
                <x-input wire:model.live.debounce.300ms="search" class="block w-full sm:w-64" type="search" placeholder="{{ __('placeholders.searchMenuOrCategory') }}" />
            </div>
        </div>
    </header>

    {{-- Main Content Area with Responsive Grid Layout --}}
    <div class="flex-1 grid grid-cols-1 md:grid-cols-12">
        {{-- Menus Section --}}
        <aside class="col-span-1 md:col-span-3 bg-white dark:bg-gray-800 border-b md:border-b-0 md:border-r border-gray-200 dark:border-gray-700">
            <div class="p-3 sm:p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('modules.menu.allMenus')</h2>
                    <span wire:click="$set('selectedMenu', null)"
                        class="text-sm text-gray-500 dark:text-gray-400 hover:text-skin-base dark:hover:text-skin-base hover:underline cursor-pointer">
                        @lang('modules.restaurant.resetFilter')
                    </span>
                </div>
            </div>
            <ul wire:sortable="sortMenus" class="p-3 sm:p-4 space-y-2">
                @forelse ($menus as $menu)
                    <li wire:sortable.item="{{ $menu->id }}"
                        wire:key="menu-{{ $menu->id }}"
                        wire:click="$set('selectedMenu', {{ $menu->id }})"
                        @class([
                            'flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium select-none cursor-pointer',
                            'bg-skin-base dark:bg-skin-base text-white' => $selectedMenu === $menu->id,
                            'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 hover:ring-1 hover:ring-green-500' => $selectedMenu !== $menu->id,
                        ])>
                        <div
                            @class([
                                'cursor-move text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors',
                                'text-white' => $selectedMenu === $menu->id,
                            ])>
                            {!! $dragHandle !!}
                        </div>
                        <span>{{ $menu->menu_name }}</span>
                    </li>
                @empty
                    <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 py-8">
                        <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        <p class="text-sm font-medium">@lang('messages.noRecordFound')</p>
                    </div>
                @endforelse
            </ul>
        </aside>

        {{-- Categories Section --}}
        <aside class="col-span-1 md:col-span-3 bg-white dark:bg-gray-800 border-b md:border-b-0 md:border-r border-gray-200 dark:border-gray-700">
            <div class="p-3 sm:p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('modules.menu.itemCategory')</h2>
            </div>
            <div wire:sortable="sortCategories" class="p-3 sm:p-4 space-y-2">
                @forelse ($categories as $category)
                    <div wire:sortable.item="{{ $category->id }}"
                        wire:key="category-{{ $category->id }}"
                        wire:click="$set('selectedCategory', {{ $category->id }})"
                        @class([
                            'group flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium select-none cursor-pointer opacity-100',
                            'bg-skin-base dark:bg-skin-base text-white' => $selectedCategory === $category->id,
                            'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 hover:ring-1 hover:ring-green-500' => $selectedCategory !== $category->id,
                        ])>

                        <div
                            @class([
                                'cursor-move text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors',
                                'text-white' => $selectedCategory === $category->id,
                            ])>
                            {!! $dragHandle !!}
                        </div>
                        <span>{{ $category->category_name }}</span>
                        <span @class([
                            'ltr:ml-auto rtl:mr-auto px-2 py-1 text-xs font-medium rounded-full',
                            'bg-white text-skin-base' => $selectedCategory === $category->id,
                            'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200' => $selectedCategory !== $category->id
                        ])>
                            {{ $category->items_count }}
                        </span>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 py-8">
                        <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <p class="text-sm font-medium">@lang('messages.noRecordFound')</p>
                    </div>
                @endforelse
            </div>
        </aside>

        {{-- Items Grid Section --}}
        <div class="col-span-1 md:col-span-6 bg-gray-50 dark:bg-gray-900">
            <div wire:sortable="sortItems"
                class="grid grid-cols-1 gap-4 p-4 sm:p-6">
                @forelse ($items as $item)
                    <div wire:sortable.item="{{ $item->id }}"
                        wire:key="item-{{ $item->id }}"
                        class="bg-white dark:bg-gray-800 rounded-lg border hover:ring-1 hover:ring-green-500 border-gray-200 dark:border-gray-700 shadow-sm p-3 select-none cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex-shrink-0 cursor-move text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors">
                                {!! $dragHandle !!}
                            </div>
                            <img src="{{ $item->item_photo_url }}" alt="{{ $item->item_name }}"
                                class="h-10 w-10 object-cover rounded-lg flex-shrink-0">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $item->item_name }}</span>
                            <div class="flex items-center gap-2 ltr:ml-auto rtl:mr-auto">
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">
                                    {{ $item->category->category_name }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full">
                                    {{ $item->menu->menu_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                <div class="col-span-2 flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 py-12">
                    <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-lg font-medium">@lang('messages.noRecordFound')</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

