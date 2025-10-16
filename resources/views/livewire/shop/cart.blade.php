<div>
    <section class="px-4 bg-white dark:bg-gray-900">
        @if($headerType === 'text')
            <div class="py-4 px-4 mx-auto max-w-screen-xl text-center lg:py-8 lg:px-12 bg-skin-base/[.1] dark:bg-gray-800 rounded-lg">
                <h1 class="text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-3xl dark:text-white">
                    {{ $headerText }}
                </h1>
            </div>
        @elseif($headerType === 'image' && count($headerImages) > 0)
            <!-- Image Carousel -->
            <div id="default-carousel" class="relative w-full touch-pan-y" data-carousel="slide">
                <!-- Carousel wrapper -->
                <div class="relative h-24 overflow-hidden border border-gray-200 rounded-lg shadow-lg sm:h-32 md:h-40 lg:h-48 dark:border-gray-700">
                    @foreach($headerImages as $index => $image)
                        <!-- Item {{ $index + 1 }} -->
                        <div class="hidden duration-700 ease-in-out" data-carousel-item>
                            <img src="{{ $image->image_url }}"
                                 class="absolute block object-cover w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2"
                                 alt="{{ $image->alt_text ?? 'Header Image' }}">
                        </div>
                    @endforeach
                </div>

                @if(count($headerImages) > 1)
                    <!-- Slider indicators -->
                    <div class="absolute z-30 flex space-x-2 -translate-x-1/2 bottom-3 sm:bottom-5 left-1/2 sm:space-x-3 rtl:space-x-reverse">
                        @foreach($headerImages as $index => $image)
                            <button type="button"
                                    class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full transition-all duration-200 {{ $index === 0 ? 'bg-white dark:bg-gray-200' : 'bg-white/50 dark:bg-gray-200/50 hover:bg-white/75 dark:hover:bg-gray-200/75' }}"
                                    aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                    aria-label="Slide {{ $index + 1 }}"
                                    data-carousel-slide-to="{{ $index }}"></button>
                        @endforeach
                    </div>

                    <!-- Slider controls - Hidden on mobile for better touch experience -->
                    <button type="button" class="absolute top-0 z-30 items-center justify-center hidden h-full px-2 cursor-pointer start-0 sm:flex sm:px-4 group focus:outline-none" data-carousel-prev>
                        <span class="inline-flex items-center justify-center w-8 h-8 transition-all duration-200 rounded-full sm:w-10 sm:h-10 bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                            <svg class="w-3 h-3 text-white sm:w-4 sm:h-4 dark:text-gray-200 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
                            </svg>
                            <span class="sr-only">Previous</span>
                        </span>
                    </button>
                    <button type="button" class="absolute top-0 z-30 items-center justify-center hidden h-full px-2 cursor-pointer end-0 sm:flex sm:px-4 group focus:outline-none" data-carousel-next>
                        <span class="inline-flex items-center justify-center w-8 h-8 transition-all duration-200 rounded-full sm:w-10 sm:h-10 bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                            <svg class="w-3 h-3 text-white sm:w-4 sm:h-4 dark:text-gray-200 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="sr-only">Next</span>
                        </span>
                    </button>
                @endif
            </div>
        @else
            <!-- Default header if no custom settings -->
            <div
            class="py-4 px-4 mx-auto max-w-screen-xl text-center lg:py-8 lg:px-12 bg-skin-base/[.1] dark:bg-gray-800 rounded-lg">
            <h1
                class="text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-3xl dark:text-white">
                    @lang('messages.frontHeroHeading')</h1>
            </div>
        @endif
    </section>
    @if (!$showCart)

        <div class="flex flex-col px-4 my-4" x-data="{ showAll: false }">
            <!-- Card Section -->
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 sm:gap-4">

                <!-- All Menu Card -->
                <a @class([
                    'group flex items-center border shadow-sm rounded-lg hover:shadow-md transition dark:bg-gray-700 dark:border-gray-600',
                    'bg-skin-base dark:bg-skin-base' => is_null($menuId),
                    'bg-white' => !is_null($menuId),
                ]) wire:key='menu-{{ 'all-' . microtime() }}'
                    wire:click='filterMenuItems(null)' href="javascript:;">
                    <div class="p-2 sm:p-3">
                        <div class="flex items-center gap-3">
                            <div class="hidden p-2 bg-gray-100 rounded-md sm:block">
                                <svg class="flex-shrink-0 text-gray-800 size-5 dark:text-neutral-200"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 409.221 409.221">
                                    <path
                                        d="M387.059 389.218H372.73v-18.114h14.327c5.523 0 10-4.477 10-10 0-55.795-42.81-101.781-97.305-106.843v-17.29c0-5.523-4.477-10-10-10s-10 4.477-10 10v17.29c-54.496 5.062-97.305 51.048-97.305 106.843 0 5.523 4.477 10 10 10h14.327v18.114h-14.327c-5.523 0-10 4.477-10 10s4.477 10 10 10h24.13q.197.004.393 0h145.564l.196.002.196-.002h24.133c5.523 0 10-4.477 10-10s-4.478-10-10-10m-34.33 0H226.772v-18.114h125.957zm-149.714-38.113c4.978-43.447 41.978-77.305 86.736-77.305s81.758 33.858 86.736 77.305zM131.63 97.306c-29.383 0-52.4 16.809-52.4 38.267 0 21.457 23.017 38.265 52.4 38.265s52.399-16.808 52.399-38.265c0-21.459-23.016-38.267-52.399-38.267m0 56.531c-19.094 0-32.4-9.625-32.4-18.265s13.306-18.267 32.4-18.267c19.093 0 32.399 9.627 32.399 18.267s-13.306 18.265-32.399 18.265m23.553 235.383H32.162V68.652h198.936v166.52c0 5.523 4.477 10 10 10s10-4.477 10-10V58.652c0-5.523-4.477-10-10-10h-4.701V10A10.002 10.002 0 0 0 225.215.07L20.979 24.397a10 10 0 0 0-8.817 9.93V399.22c0 5.523 4.477 10 10 10h133.021c5.523 0 10-4.477 10-10s-4.477-10-10-10M32.162 43.206l184.235-21.944v27.391H32.162zm82.627 317.362c-5.523 0-10-4.477-10-10s4.477-10 10-10h33.681c5.523 0 10 4.477 10 10s-4.477 10-10 10z" />
                                </svg>
                            </div>
                            <div class="grow">
                                <h3 wire:loading.class.delay='opacity-50' @class([
                                    'font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-xs lg:text-base',
                                    'text-gray-800 group-hover:text-skin-base' => !is_null($menuId),
                                    'text-white group-hover:text-white' => is_null($menuId),
                                ])>
                                    @lang('app.showAll')
                                </h3>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Dynamic Menu Cards -->
                @forelse ($menuList as $index => $item)
                    <div x-show="showAll || {{ $index }} < 7" x-transition>
                        <a @class([
                            'group flex flex-col border shadow-sm rounded-lg hover:shadow-md transition dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600',
                            'bg-skin-base dark:bg-skin-base' => $menuId == $item->id,
                            'bg-white' => $menuId != $item->id,
                        ]) wire:key='menu-{{ $item->id . microtime() }}'
                            wire:click='filterMenuItems({{ $item->id }})' href="javascript:;">
                            <div class="p-2 sm:p-3">
                                <div class="flex items-center gap-3">
                                    <div class="hidden p-2 bg-gray-100 rounded-md sm:block">
                                        <svg class="flex-shrink-0 text-gray-800 size-5 dark:text-neutral-200"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 409.221 409.221">
                                            <path
                                                d="M387.059 389.218H372.73v-18.114h14.327c5.523 0 10-4.477 10-10 0-55.795-42.81-101.781-97.305-106.843v-17.29c0-5.523-4.477-10-10-10s-10 4.477-10 10v17.29c-54.496 5.062-97.305 51.048-97.305 106.843 0 5.523 4.477 10 10 10h14.327v18.114h-14.327c-5.523 0-10 4.477-10 10s4.477 10 10 10h24.13q.197.004.393 0h145.564l.196.002.196-.002h24.133c5.523 0 10-4.477 10-10s-4.478-10-10-10m-34.33 0H226.772v-18.114h125.957zm-149.714-38.113c4.978-43.447 41.978-77.305 86.736-77.305s81.758 33.858 86.736 77.305zM131.63 97.306c-29.383 0-52.4 16.809-52.4 38.267 0 21.457 23.017 38.265 52.4 38.265s52.399-16.808 52.399-38.265c0-21.459-23.016-38.267-52.399-38.267m0 56.531c-19.094 0-32.4-9.625-32.4-18.265s13.306-18.267 32.4-18.267c19.093 0 32.399 9.627 32.399 18.267s-13.306 18.265-32.399 18.265m23.553 235.383H32.162V68.652h198.936v166.52c0 5.523 4.477 10 10 10s10-4.477 10-10V58.652c0-5.523-4.477-10-10-10h-4.701V10A10.002 10.002 0 0 0 225.215.07L20.979 24.397a10 10 0 0 0-8.817 9.93V399.22c0 5.523 4.477 10 10 10h133.021c5.523 0 10-4.477 10-10s-4.477-10-10-10M32.162 43.206l184.235-21.944v27.391H32.162zm82.627 317.362c-5.523 0-10-4.477-10-10s4.477-10 10-10h33.681c5.523 0 10 4.477 10 10s-4.477 10-10 10z" />
                                        </svg>
                                    </div>

                                    <div class="grow">
                                        <h3 wire:loading.class.delay='opacity-50' @class([
                                            'font-semibold group-hover:text-skin-base dark:group-hover:text-gray-100 dark:text-neutral-200 text-xs lg:text-base',
                                            'text-gray-800 dark:text-gray-200' => $menuId != $item->id,
                                            'text-white group-hover:text-white' => $menuId == $item->id,
                                        ])>
                                            {{ $item->getTranslation('menu_name', session('locale', app()->getLocale())) }}
                                        </h3>
                                        <p @class([
                                            'text-sm dark:text-neutral-500 hidden sm:block',
                                            'text-gray-500 dark:text-white' => $menuId != $item->id,
                                            'text-gray-100 dark:text-white' => $menuId == $item->id,
                                        ])>
                                            {{ $item->items_count }} @lang('modules.menu.item')
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="inline-flex items-center dark:text-gray-400">
                        @lang('messages.noMenuAdded')
                    </div>
                @endforelse
            </div>
            <!-- End Card Section -->

            <!-- Toggle Button -->
            @if (count($menuList) > 8)
                <div class="flex justify-center mt-4" x-cloak wire:key="show-more-button">
                    <button @click="showAll = !showAll"
                        class="flex items-center gap-1 text-sm text-skin-base hover:underline">
                        <span
                            x-text="showAll ? '{{ __('modules.menu.showLess') }}' : '{{ __('modules.menu.showMore') }}'"></span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': showAll }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            @endif
        </div>

        <div class="mx-4 mt-6">
            <!-- Mobile Dropdown -->
            <div class="relative lg:hidden" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false"
                    class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg px-4 py-2.5 flex items-center justify-between shadow-sm hover:bg-gray-50 transition-colors duration-200">
                    <span class="text-sm font-medium truncate">
                        {{ is_null($filterCategories) ? __('app.showAll') : $categoryList->firstWhere('id', $filterCategories)?->getTranslation('category_name', session('locale', app()->getLocale())) }}
                    </span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown menu -->
                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    class="absolute left-0 right-0 z-50 mt-2 overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-700">
                    <div class="overflow-y-auto max-h-80">
                        <button wire:click="filterMenu(null); $nextTick(() => { open = false })"
                            class="w-full px-4 py-2.5 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors {{ is_null($filterCategories) ? 'bg-gray-50 dark:bg-gray-600 text-skin-base' : 'text-gray-700 dark:text-gray-200' }}">
                            @lang('app.showAll')
                        </button>

                        @foreach ($categoryList as $item)
                            @if ($item->items->count() > 0)
                                <button wire:click="filterMenu({{ $item->id }}); $nextTick(() => { open = false })"
                                    class="w-full px-4 py-2.5 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors flex items-center justify-between {{ $filterCategories == $item->id ? 'bg-gray-50 dark:bg-gray-600 text-skin-base' : 'text-gray-700 dark:text-gray-200' }}">
                                    <span>{{ $item->getTranslation('category_name', session('locale', app()->getLocale())) }}</span>
                                    <span
                                        class="px-2 py-1 text-xs text-gray-600 bg-gray-100 rounded-full dark:bg-gray-600 dark:text-gray-300">
                                        {{ $item->items->count() }}
                                    </span>
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Desktop Tabs -->
            <div class="hidden p-2 rounded-md lg:block group bg-gray-50 dark:bg-gray-800">
                <nav class="flex gap-2 overflow-x-auto group-hover:[&::-webkit-scrollbar-thumb]:bg-gray-300 dark:group-hover:[&::-webkit-scrollbar-thumb]:bg-gray-600 [&::-webkit-scrollbar]:h-1.5 [&::-webkit-scrollbar-track]:hidden [&::-webkit-scrollbar-thumb]:rounded-full py-2"
                    aria-label="Categories">
                    <button wire:click="filterMenu(null)" @class([
                        'px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap',
                        'bg-skin-base text-white shadow-sm' => is_null($filterCategories),
                        'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' => !is_null(
                            $filterCategories),
                    ])>
                        @lang('app.showAll')
                    </button>

                    @foreach ($categoryList as $item)
                        @if ($item->items->count() > 0)
                            <button wire:click="filterMenu({{ $item->id }})" @class([
                                'px-4 py-2 text-sm font-medium rounded-lg transition-colors inline-flex items-center gap-2 whitespace-nowrap',
                                'bg-skin-base text-white shadow-sm' => $filterCategories == $item->id,
                                'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' =>
                                    $filterCategories != $item->id,
                            ])>
                                <span>{{ $item->getTranslation('category_name', session('locale', app()->getLocale())) }}</span>
                                <span
                                    class="px-2 py-0.5 text-xs rounded-full {{ $filterCategories == $item->id ? 'bg-white/20 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                    {{ $item->items->count() }}
                                </span>
                            </button>
                        @endif
                    @endforeach
                </nav>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mx-4 my-6 sm:flex-row sm:items-center">
            <div class="col-span-full md:col-span-2">
                <x-input id="menu_name" class="block w-full " type="text"
                    placeholder="{{ __('placeholders.searchMenuItems') }}" wire:model.live.debounce.500ms="search" />
            </div>
            <div class="flex flex-row flex-wrap items-center justify-end w-full col-span-2 gap-4 mt-2 md:col-span-1 sm:w-auto">
                @if ($restaurant?->show_veg)
                <label class="inline-flex items-center cursor-pointer" id="veg_toggle">
                    <input type="checkbox" value="1" wire:model.live='showVeg' class="sr-only peer">
                    <div
                        class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                    </div>
                    <span class="text-sm font-medium text-gray-900 ms-3 dark:text-gray-300">
                        @lang('modules.menu.typeVeg')
                    </span>
                </label>
                @endif

                @if ($restaurant?->show_halal)
                <label class="inline-flex items-center cursor-pointer" id="halal_toggle">
                    <input type="checkbox" value="1" wire:model.live='showHalal' class="sr-only peer">
                    <div
                        class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                    </div>
                    <span class="text-sm font-medium text-gray-900 ms-3 dark:text-gray-300">
                        @lang('modules.menu.typeHalal')
                    </span>
                </label>
                @endif
            </div>


        </div>

    @endif

    @if ($showMenu)
        <div class="px-4 mb-32 space-y-4 lg:gap-8">

            @forelse ($menuItems as $key => $itemCat)
                <h3 class="my-4 text-base font-semibold text-gray-900 lg:text-xl dark:text-white">{{ $key }}
                </h3>
                <div class="space-y-4 lg:space-y-0 lg:grid lg:grid-cols-3 lg:gap-8">
                    @foreach ($itemCat as $item)
                        <div @class([
                            'flex items-center justify-between gap-6 border shadow-sm rounded-lg hover:shadow-md transition dark:border-gray-600 dark:lg:bg-gray-900 dark:shadow-sm lg:rounded-md',
                            'bg-gray-100 dark:bg-gray-800' => !$item->in_stock,
                            'bg-white dark:bg-gray-900' => $item->in_stock,
                        ]) wire:key='menu-item-{{ $item->id . microtime() }}'>
                            <div class="flex w-full p-3 space-x-4">
                                @if ($restaurant && !$restaurant->hide_menu_item_image_on_customer_site)
                                    <img class="object-cover w-16 h-16 rounded-md cursor-pointer lg:w-24 lg:h-24"
                                        wire:click="showItemDetail({{ $item->id }})"
                                        src="{{ $item->item_photo_url }}" alt="{{ $item->item_name }}">
                                @endif
                                <div
                                    class="flex flex-col w-full gap-1 text-sm font-normal text-gray-500 lg:text-base dark:text-gray-400">
                                    <div
                                        class="inline-flex items-center text-sm font-semibold text-gray-900 lg:text-base dark:text-white">
                                        <img src="{{ asset('img/' . $item->type . '.svg') }}" class="h-4 mr-1"
                                            title="@lang('modules.menu.' . $item->type)" alt="" />
                                        {{ $item->getTranslatedValue('item_name', session('locale')) }}
                                    </div>
                                    @if ($item->description)
                                        <div class="w-full text-xs font-normal text-gray-500 cursor-pointer lg:text-sm dark:text-gray-400"
                                            wire:click="showItemDetail({{ $item->id }})">
                                            {{ str($item->getTranslatedValue('description', session('locale')))->limit(50) }}
                                        </div>
                                    @endif

                                    @if ($item->preparation_time)
                                        <div
                                            class="inline-flex items-center my-1 text-xs font-normal text-gray-700 dark:text-gray-400 max-w-56">
                                            @lang('modules.menu.preparationTime') :
                                            {{ $item->preparation_time }} @lang('modules.menu.minutes')</div>
                                    @endif
                                    <div class="flex items-center justify-between w-full">
                                        <div>
                                            @if ($item->variations_count == 0)
                                                <span
                                                    class="font-semibold text-gray-900 dark:text-white">{{ currency_format($item->price, $restaurant->currency_id) }}</span>
                                            @endif
                                        </div>

                                        @if ($canCreateOrder)
                                            @if (!$item->in_stock)
                                                <div class="text-red-500">Out of stock</div>
                                            @elseif ($restaurant->allow_customer_orders)
                                                @if (isset($cartItemQty[$item->id]) && $cartItemQty[$item->id] > 0)
                                                    <div class="relative flex items-center justify-start max-w-24 me-2"
                                                        wire:key='orderItemQty-{{ $item->id }}-counter'>
                                                        <button type="button"
                                                            @if ($item->variations_count > 0) wire:click="subCartItems({{ $item->id }})"
                                                    @elseif($item->modifier_groups_count > 0)
                                                        wire:click="subModifiers({{ $item->id }})"
                                                    @else
                                                        wire:click="subQty('{{ $item->id }}')" @endif
                                                            class="h-8 p-3 border border-gray-300 bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 rounded-s-md">
                                                            <svg class="w-2 h-2 text-gray-900 dark:text-white"
                                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                                fill="none" viewBox="0 0 18 2">
                                                                <path stroke="currentColor" stroke-linecap="round"
                                                                    stroke-linejoin="round" stroke-width="2"
                                                                    d="M1 1h16" />
                                                            </svg>
                                                        </button>

                                                        <input type="text"
                                                            wire:model='cartItemQty.{{ $item->id }}'
                                                            class="min-w-10 bg-white border-x-0 border-gray-300 h-8 text-center text-gray-900 text-sm  block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white "
                                                            value="1" readonly />
                                                        <button type="button"
                                                            wire:click="
                                                        @if ($item->variations_count > 0 || $item->modifier_groups_count > 0) addCartItems({{ $item->id }}, {{ $item->variations_count }}, {{ $item->modifier_groups_count }})
                                                        @else
                                                            addQty('{{ $item->id }}') @endif
                                                    "
                                                            class="h-8 p-3 border border-gray-300 bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 rounded-e-md">
                                                            <svg class="w-2 h-2 text-gray-900 dark:text-white"
                                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                                fill="none" viewBox="0 0 18 18">
                                                                <path stroke="currentColor" stroke-linecap="round"
                                                                    stroke-linejoin="round" stroke-width="2"
                                                                    d="M9 1v16M1 9h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @else
                                                    <x-cart-button
                                                        wire:click='addCartItems({{ $item->id }}, {{ $item->variations_count }} , {{ $item->modifier_groups_count }})'
                                                        wire:key='item-input-{{ $item->id . microtime() }}'>@lang('app.add')</x-cart-button>
                                                @endif
                                            @elseif ($item->variations_count > 0 && $restaurant->allow_customer_orders)
                                                <x-secondary-button-table
                                                    wire:click='showItemVariations({{ $item->id }})'>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                        height="16" fill="currentColor" class="w-4 h-4 mr-1"
                                                        viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd"
                                                            d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                                                    </svg>
                                                    @lang('modules.menu.showVariations') ({{ $item->variations_count }})
                                                </x-secondary-button-table>
                                            @endif
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <div
                    class="flex flex-col items-center justify-center p-6 text-center text-gray-500 dark:text-gray-400">
                    <svg width="100" height="100" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                        fill="none">
                        <path d="M4 14a8 8 0 0 1 16 0z" fill="#e5e7eb" />
                        <rect x="3" y="14" width="18" height="2.5" rx=".5" fill="#d1d5db" />
                        <circle cx="12" cy="4.5" r=".8" fill="#9ca3af" />
                        <circle cx="9.5" cy="10" r=".5" fill="#4b5563" />
                        <circle cx="14.5" cy="10" r=".5" fill="#4b5563" />
                    </svg>
                    <span class="text-lg">
                        @lang('messages.noItemAdded')
                    </span>
                </div>
            @endforelse

            <div class="fixed flex justify-center w-full max-w-lg gap-6 -ml-4 bottom-24 lg:hidden">
                @if ($this->shouldShowWaiterButtonMobile)
                    @livewire('forms.callWaiterButton', ['tableNumber' => $table->id ?? null, 'shopBranch' => $shopBranch])
                @endif
                @if (is_null(customer()) && $restaurant->customer_login_required)
                    <x-button type="button" wire:click="$dispatch('showSignup')">@lang('app.login')</x-button>
                @endif
            </div>

            @if ($cartQty > 0)
                <div
                    class="fixed z-10 flex items-center justify-between w-full max-w-lg p-4 mx-auto -ml-4 antialiased font-bold text-white rounded-md bg-skin-base lg:max-w-screen-xl dark:bg-gray-800 bottom-1">
                    <div>@lang('modules.order.totalItem'): {{ $cartQty }} &nbsp;|&nbsp;
                        {{ currency_format($subTotal, $restaurant->currency_id) }} + @lang('modules.order.taxes')</div>

                    <x-secondary-button wire:click="showCartItems">@lang('modules.order.viewCart')</x-secondary-button>

                </div>
            @endif
        </div>
    @endif

    @if ($showCart)

        @if ($restaurant->allow_customer_orders)
            <div class="flex my-4">

                <ul
                    class="flex items-center w-full mx-4 text-sm font-medium text-gray-900 bg-white border border-gray-200 divide-x rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @if ($restaurant->allow_dine_in_orders)
                        <li class="w-full border-b border-gray-200 cursor-pointer sm:border-b-0 dark:border-gray-600">
                            <div class="flex items-center ps-3">
                                <input id="horizontal-list-radio-dine_in" wire:model.live='orderType' type="radio"
                                    value="dine_in" name="list-radio"
                                    class="w-4 h-4 bg-gray-100 border-gray-300 text-skin-base focus:ring-skin-base dark:focus:ring-skin-base dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                <label for="horizontal-list-radio-dine_in"
                                    class="w-full py-3 text-sm font-medium text-gray-900 ms-2 dark:text-gray-300">@lang('modules.order.dine_in')</label>
                            </div>
                        </li>
                    @endif
                    @if ($restaurant->allow_customer_delivery_orders)
                        <li class="w-full border-b border-gray-200 cursor-pointer sm:border-b-0 dark:border-gray-600">
                            <div class="flex items-center ps-3 ">
                                <input id="horizontal-list-radio-delivery" wire:model.live='orderType' type="radio"
                                    value="delivery" name="list-radio"
                                    class="w-4 h-4 bg-gray-100 border-gray-300 text-skin-base focus:ring-skin-base dark:focus:ring-skin-base dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                <label for="horizontal-list-radio-delivery"
                                    class="w-full py-3 text-sm font-medium text-gray-900 ms-2 dark:text-gray-300">@lang('modules.order.delivery')</label>
                            </div>
                        </li>
                    @endif

                    @if ($restaurant->allow_customer_pickup_orders)
                        <li class="w-full border-b border-gray-200 sm:border-b-0 dark:border-gray-600">
                            <div class="flex items-center ps-3 ">
                                <input id="horizontal-list-radio-pickup" wire:model.live='orderType' type="radio"
                                    value="pickup" name="list-radio"
                                    class="w-4 h-4 bg-gray-100 border-gray-300 text-skin-base focus:ring-skin-base dark:focus:ring-skin-base dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                <label for="horizontal-list-radio-pickup"
                                    class="w-full py-3 text-sm font-medium text-gray-900 ms-2 dark:text-gray-300">@lang('modules.order.pickup')</label>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        @endif
        <div class="px-4 mt-4 space-y-4">
            @foreach ($orderItemList as $key => $item)
                <div class="flex items-center justify-between gap-6 transition bg-white border rounded-lg shadow-sm hover:shadow-md dark:border-gray-600 dark:lg:bg-gray-900 dark:shadow-sm"
                    wire:key='menu-item-{{ $item->id . microtime() }}'>
                    <div class="flex w-full p-4 space-x-4 dark:bg-gray-800 dark:text-gray-200">
                        <!-- Item Image -->
                        @if ($restaurant && !$restaurant->hide_menu_item_image_on_customer_site)

                            <img class="object-cover w-10 h-10 rounded-lg cursor-pointer lg:w-16 lg:h-16"
                                wire:click="showItemDetail({{ $item->id }})" src="{{ $item->item_photo_url }}"
                                alt="{{ $item->item_name }}">
                        @endif

                        <!-- Item Details -->
                        <div class="flex-1 min-w-0">
                            <div
                                class="flex flex-col items-start justify-between w-full gap-2 sm:flex-row sm:items-baseline">
                                <!-- Item Name and Details -->
                                <div class="flex flex-wrap items-center gap-2">
                                    <div
                                        class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                        <img src="{{ asset('img/' . $item->type . '.svg') }}" class="h-4 mr-2"
                                            title="@lang('modules.menu.' . $item->type)" alt="" />
                                        {{ $item->item_name }}
                                    </div>

                                    @if (isset($orderItemVariation[$key]))
                                        <span
                                            class="px-2.5 py-0.5 bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-sm text-xs font-sm">
                                            {{ $orderItemVariation[$key]->variation }}
                                        </span>
                                    @endif

                                    {{-- @if ($item->preparation_time)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            @lang('modules.menu.preparationTime'): {{ $item->preparation_time }} @lang('modules.menu.minutes')
                                        </span>
                                    @endif --}}
                                </div>

                                <!-- Quantity Controls and Price -->
                                <div class="flex flex-wrap items-center justify-between gap-3 sm:w-auto md:w-1/3">
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center">
                                        <button type="button" wire:click="subQty('{{ $key }}')"
                                            class="h-8 p-2 border border-gray-300 bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 rounded-s-md">
                                            <svg class="w-2 h-2 text-gray-900 dark:text-white" aria-hidden="true"
                                                viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>

                                        <input type="text" wire:model='orderItemQty.{{ $key }}'
                                            class="w-12 h-8 text-sm text-center text-gray-900 bg-white border-gray-300 border-x-0 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            readonly />

                                        <button type="button" wire:click="addQty('{{ $key }}')"
                                            class="h-8 p-2 border border-gray-300 bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 rounded-e-md">
                                            <svg class="w-2 h-2 text-gray-900 dark:text-white" aria-hidden="true"
                                                viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Price and Amount -->
                                    @php
                                        // Use display price (base price without tax for inclusive items)
                                        $displayPrice = $this->getItemDisplayPrice($key);
                                        // Total amount per line (what customer pays)
                                        $totalAmount = $orderItemAmount[$key];
                                    @endphp
                                    <div class="flex flex-col items-end gap-1">
                                        @if ($taxMode === 'item' && $restaurant?->tax_inclusive)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ currency_format($displayPrice, $restaurant->currency_id) }} ×
                                                {{ $orderItemQty[$key] }}
                                            </div>
                                        @endif
                                        <span
                                            class="text-base font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                            {{ currency_format($totalAmount, $restaurant->currency_id) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Modifiers (Shown below if present) -->
                            @if (!empty($itemModifiersSelected[$key]))
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach ($itemModifiersSelected[$key] as $modifierOptionId)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-skin-base/10 text-skin-base">
                                            {{ $this->modifierOptions[$modifierOptionId]->name }}
                                            <span class="ml-1 text-skin-base">
                                                {{ currency_format($this->modifierOptions[$modifierOptionId]->price, $this->modifierOptions[$modifierOptionId]->modifierGroup->branch->restaurant->currency_id) }}
                                            </span>
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Item Notes Section -->
                            <div class="mt-2">
                                @if (isset($this->itemNotes[$key]) && !empty($this->itemNotes[$key]))
                                    <div class="flex items-center mt-2">
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">
                                            <svg class="w-3.5 h-3.5 mr-1.5" viewBox="0 0 24 24" stroke="currentColor"
                                                fill="none">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 8h10M7 12h4m1 8-4-4H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-3z" />
                                            </svg>
                                            <span class="mr-1.5">{{ $this->itemNotes[$key] }}</span>
                                            <button wire:click="$set('itemNotes.{{ $key }}', '')"
                                                class="text-gray-400 transition-colors duration-200 hover:text-red-500">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </span>
                                    </div>
                                @else
                                    <div x-data="{ showNoteInput: false, noteText: '' }" class="mt-2">
                                        <button x-show="!showNoteInput"
                                            @click="showNoteInput = true; $nextTick(() => $refs.noteInput.focus())"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-full text-gray-700 hover:bg-skin-base/10  hover:text-skin-base dark:text-gray-300 dark:hover:text-gray-200 dark:hover:bg-gray-600 transition-all duration-200 group">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"
                                                class="w-3.5 h-3.5 group-hover:scale-110 transition-transform duration-200"
                                                xml:space="preserve">
                                                <path
                                                    d="M11.3 26.5 4 28l1.5-7.3L21.6 4.5c.8-.8 2.1-.8 2.9 0l2.9 2.9c.8.8.8 2.1 0 2.9zm7.4-19 5.8 5.8m-5.8 0-8.8 8.8"
                                                    style="fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10" />
                                            </svg>
                                            <span class="whitespace-nowrap">@lang('modules.order.addNote')</span>
                                        </button>
                                        <div x-show="showNoteInput" x-cloak @click.away="showNoteInput = false"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 transform scale-95"
                                            x-transition:enter-end="opacity-100 transform scale-100"
                                            class="flex items-center mt-2">

                                            <div class="flex w-full">
                                                <div class="relative flex-1">
                                                    <x-input x-ref="noteInput" x-model="noteText" type="text"
                                                        class="w-full pr-20 text-sm border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-skin-base focus:border-skin-base"
                                                        :placeholder="__('placeholders.addItemNotesPlaceholder')"
                                                        @keydown.enter="$wire.set('itemNotes.{{ $key }}', noteText); showNoteInput = false" />
                                                    <div
                                                        class="absolute inset-y-0 right-0 flex items-center gap-1 pr-2">
                                                        <button
                                                            @click="$wire.set('itemNotes.{{ $key }}', noteText); showNoteInput = false"
                                                            class="p-1.5 text-white rounded-md bg-skin-base hover:bg-skin-base/90 transition-colors duration-200"
                                                            title="@lang('app.save')">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="w-3.5 h-3.5" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="m5 13 4 4L19 7" />
                                                            </svg>
                                                        </button>
                                                        <button @click="showNoteInput = false"
                                                            class="p-1.5 text-gray-500 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
                                                            title="@lang('app.cancel')">
                                                            <svg class="w-3.5 h-3.5"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if ($cartQty > 0)
                <div>
                    <div
                        class="w-full h-auto p-4 mt-3 space-y-4 text-center rounded select-none bg-gray-50 dark:bg-gray-700">
                        <div class="mb-3">
                            <div x-data="{ showNotes: false }" x-cloak>
                                <!-- Add Note Button -->
                                <div x-show="!showNotes && !$wire.orderNote" class="flex">
                                    <x-secondary-button @click="showNotes = true"
                                        class="inline-flex items-center gap-2">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        @lang('modules.order.addNote')
                                    </x-secondary-button>
                                </div>

                                <!-- Notes Form -->
                                <div x-show="showNotes" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100" class="mt-3">

                                    <div
                                        class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                                        <!-- Header -->
                                        <div
                                            class="flex items-center justify-between p-3 border-b border-gray-200 dark:border-gray-700">
                                            <h3 class="font-medium text-gray-900 dark:text-white">
                                                @lang('modules.order.addNote')
                                            </h3>
                                            <x-secondary-button @click="showNotes = false" class="!p-1.5">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                                                </svg>
                                            </x-secondary-button>
                                        </div>

                                        <!-- Form Content -->
                                        <div class="p-3">
                                            <x-textarea id="orderNote" class="block w-full mt-1" rows="3"
                                                wire:model.live.debounce.750ms="orderNote"
                                                placeholder="{{ __('placeholders.addOrderNotesPlaceholder') }}" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview Note -->
                                <div x-show="!showNotes && $wire.orderNote"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0" class="mt-3">
                                    <div
                                        class="flex items-center justify-between p-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700/50 dark:border-gray-600">
                                        <!-- Note Icon & Text -->
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-400" width="24" height="24"
                                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M6 11h8V9H6zm0 4h8v-2H6zm0-8h4V5H6zm6-5H5.5A1.5 1.5 0 0 0 4 3.5v13A1.5 1.5 0 0 0 5.5 18h9a1.5 1.5 0 0 0 1.5-1.5V6z"
                                                    fill="currentColor" />
                                            </svg>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $orderNote }}
                                            </p>
                                        </div>

                                        <!-- Edit Button -->
                                        <button @click="showNotes = true"
                                            class="flex items-center gap-1.5 text-skin-base hover:text-skin-base/80 hover:scale-110 p-1">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="m15.232 5.232 3.536 3.536m-2.036-5.036a2.5 2.5 0 1 1 3.536 3.536L6.5 21.036H3v-3.572z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                            <div>
                                @lang('modules.order.totalItem')
                            </div>
                            <div>
                                {{ count($orderItemList) }}
                            </div>
                        </div>

                        <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                            <div>
                                @lang('modules.order.subTotal')
                            </div>
                            <div>
                                {{ currency_format($subTotal, $restaurant->currency_id) }}
                            </div>
                        </div>

                        @if (count($orderItemList) > 0 && $extraCharges)
                            @foreach ($extraCharges as $charge)
                                <div wire:key="extraCharge-{{ $loop->index }}"
                                    class="flex justify-between text-sm text-gray-500 dark:text-neutral-400">
                                    <div class="inline-flex items-center gap-x-1">{{ $charge->charge_name }}
                                        @if ($charge->charge_type == 'percent')
                                            ({{ $charge->charge_value }}%)
                                        @endif
                                    </div>
                                    <div>
                                        {{ currency_format($charge->getAmount($subTotal), $restaurant->currency_id) }}
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if ($taxMode == 'order')
                            @foreach ($taxes as $item)
                                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                                    <div>
                                        {{ $item->tax_name }} ({{ $item->tax_percent }}%)
                                    </div>
                                    <div>
                                        {{ currency_format(($item->tax_percent / 100) * $subTotal, $restaurant->currency_id) }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            @if (!empty($orderItemTaxDetails) && count($orderItemTaxDetails))
                                @php
                                    $taxTotals = [];
                                    $totalTax = 0;
                                    foreach ($orderItemTaxDetails as $item) {
                                        $qty = $item['qty'] ?? 1;
                                        foreach ($item['tax_breakup'] as $taxName => $taxInfo) {
                                            if (!isset($taxTotals[$taxName])) {
                                                $taxTotals[$taxName] = [
                                                    'percent' => $taxInfo['percent'],
                                                    'amount' => $taxInfo['amount'] * $qty,
                                                ];
                                            } else {
                                                $taxTotals[$taxName]['amount'] += $taxInfo['amount'] * $qty;
                                            }
                                        }
                                        $totalTax += collect($item['tax_amount'])->sum();
                                    }
                                @endphp
                                @foreach ($taxTotals as $taxName => $taxInfo)
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-neutral-400">
                                        <div>
                                            {{ $taxName }} ({{ $taxInfo['percent'] }}%)
                                        </div>
                                        <div>
                                            {{ currency_format($taxInfo['amount'], $restaurant->currency_id) }}
                                        </div>
                                    </div>
                                @endforeach
                                <div class="flex justify-between mt-2 text-sm text-gray-500 dark:text-neutral-400">
                                    <div>
                                        @lang('modules.order.totalTax')
                                        <span
                                            class="ml-2 px-2 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300">
                                            @lang($restaurant?->tax_inclusive ? 'modules.settings.taxInclusive' : 'modules.settings.taxExclusive')
                                        </span>
                                    </div>
                                    <div>
                                        {{ currency_format($totalTax, $restaurant->currency_id) }}
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if ($orderType === 'delivery' && !is_null($deliveryFee))
                            <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                                <div>
                                    @lang('modules.delivery.deliveryFee')
                                </div>
                                <div>
                                    @if ($deliveryFee > 0)
                                        {{ currency_format($deliveryFee, $restaurant->currency_id) }}
                                    @else
                                        <span class="font-medium text-green-500">@lang('modules.delivery.freeDelivery')</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between font-medium text-gray-900 dark:text-white">
                            <div>
                                @lang('modules.order.total')
                            </div>
                            <div>
                                {{ currency_format($total, $restaurant->currency_id) }}
                            </div>
                        </div>
                    </div>

                    @if ($orderType === 'delivery' && !empty($deliveryAddress))
                        <div class="w-full h-auto p-4 mt-3 rounded select-none bg-gray-50 dark:bg-gray-700">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-base font-medium text-gray-900 dark:text-white">@lang('modules.delivery.deliveryAddress')</h3>

                                @if (!empty($deliveryAddress))
                                    <x-secondary-button class="text-xs"
                                        wire:click="$toggle('showDeliveryAddressModal')">
                                        @lang('modules.delivery.changeDeliveryAddress')
                                    </x-secondary-button>
                                @endif
                            </div>

                            @if (!empty($deliveryAddress))
                                <div
                                    class="p-3 bg-white border border-gray-200 rounded-md dark:bg-gray-800 dark:border-gray-700">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 mt-0.5 text-gray-500 dark:text-gray-400 flex-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <div class="text-sm text-gray-700 dark:text-gray-300">
                                            <p class="font-medium">{{ $deliveryAddress }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="w-full h-auto pt-3 pb-4 text-center select-none"
                        wire:key='order-{{ microtime() }}'>
                        <div class="flex gap-2">
                            @if (is_null($customer) && ($restaurant->customer_login_required || $orderType == 'delivery'))
                                <x-button class="justify-center w-full" wire:click="$dispatch('showSignup')">
                                    @lang('app.next')
                                </x-button>
                            @elseif (is_null($customer) && $orderType == 'pickup')
                                <x-button class="justify-center w-full" wire:click="showPickupDateTime">
                                    @lang('app.next')
                                </x-button>
                            @else
                                <div class="grid w-full grid-cols-2 gap-2">
                                    @php
                                        $isPaymentEnabled =
                                            in_array($orderType, ['dine_in', 'delivery', 'pickup']) &&
                                            (($orderType == 'dine_in' && $paymentGateway->is_dine_in_payment_enabled) ||
                                                ($orderType == 'delivery' &&
                                                    $paymentGateway->is_delivery_payment_enabled) ||
                                                ($orderType == 'pickup' && $paymentGateway->is_pickup_payment_enabled));

                                        $showPayNow =
                                            $paymentGateway->is_qr_payment_enabled ||
                                            $paymentGateway->stripe_status ||
                                            $paymentGateway->razorpay_status ||
                                            $paymentGateway->flutterwave_status ||
                                            $paymentGateway->paypal_status ||
                                            $paymentGateway->payfast_status ||
                                            $paymentGateway->xendit_status ||
                                            $paymentGateway->is_offline_payment_enabled;

                                        $loadingSpinner = '
                                            <div role="status" class="flex items-center">
                                                <svg aria-hidden="true" class="w-5 h-5 text-gray-200 animate-spin dark:text-gray-600 fill-gray-700"
                                                    viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                                </svg>
                                            </div>';
                                    @endphp

                                    @if (!$order)
                                        @if ($showPayNow)
                                            <x-button class="flex items-center justify-center w-full gap-2"
                                                wire:click="placeOrder(true)" wire:loading.delay.attr="disabled">
                                                <span wire:loading.delay
                                                    wire:target="placeOrder(true)">{!! $loadingSpinner !!}</span>
                                                @lang('modules.order.payNow')
                                            </x-button>

                                            @if (!$isPaymentEnabled)
                                                <x-secondary-button
                                                    class="flex items-center justify-center w-full gap-2"
                                                    wire:click="placeOrder" wire:loading.delay.attr="disabled">
                                                    <span wire:loading.delay
                                                        wire:target="placeOrder">{!! $loadingSpinner !!}</span>
                                                    @lang('modules.order.payLater')
                                                </x-secondary-button>
                                            @endif
                                        @else
                                            <x-button class="flex items-center justify-center w-full gap-2"
                                                wire:click="placeOrder" wire:loading.delay.attr="disabled">
                                                <span wire:loading.delay
                                                    wire:target="placeOrder">{!! $loadingSpinner !!}</span>
                                                @lang('modules.order.placeOrder')
                                            </x-button>
                                        @endif
                                    @else
                                        <x-button class="flex items-center justify-center w-full gap-2"
                                            wire:click="placeOrder" wire:loading.delay.attr="disabled">
                                            <span wire:loading.delay
                                                wire:target="placeOrder">{!! $loadingSpinner !!}</span>
                                            @lang('modules.order.placeOrder')
                                        </x-button>
                                    @endif

                                </div>

                            @endif
                        </div>

                        <div class="flex mt-4">
                            <a href="javascript:;" wire:click="showMenuItems"
                                class="relative text-gray-500 transition-colors duration-300 group hover:text-skin-base">
                                <span class="inline-block">@lang('app.back')</span>
                                <span
                                    class="absolute bottom-0 left-0 w-0 h-0.5 bg-skin-base group-hover:w-full transition-all duration-300 ease-in-out"></span>
                            </a>
                        </div>
                    </div>

                </div>
            @else
                <div class="p-4 text-center md:py-7 md:px-5">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                        @lang('messages.cartEmpty')
                    </h3>

                    <a class="inline-flex items-center justify-center px-3 py-2 mt-3 text-sm font-medium text-white border border-transparent rounded-lg gap-x-2 bg-skin-base hover:bg-skin-base focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                        href="{{ module_enabled('Subdomain') ? url('/') : route('shop_restaurant', [$restaurant->hash]) }}"
                        wire:navigate>
                        @lang('modules.order.placeOrder')
                    </a>
                </div>
            @endif
        </div>
    @endif

    <x-dialog-modal wire:model.live="showCustomerNameModal" maxWidth="sm">
        <x-slot name="title">

        </x-slot>

        <x-slot name="content">
            @if (!is_null($customer))
                <form wire:submit="submitCustomerName">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-label for="customerName" value="{{ __('modules.customer.enterName') }}" />
                            <x-input id="customerName" class="block w-full mt-1" type="text"
                                wire:model='customerName' />
                            <x-input-error for="customerName" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="customerPhone " value="{{ __('modules.customer.phone') }}" />
                            <x-input id="customerPhone" class="block w-full mt-1" type="text"
                                wire:model='customerPhone' />
                            <x-input-error for="customerPhone" class="mt-2" />
                        </div>

                        @if ($orderType == 'delivery')
                            <div>
                                <x-label for="customerAddress" value="{{ __('modules.customer.address') }}" />
                                <x-textarea id="customerAddress" class="block w-full mt-1"
                                    wire:model='customerAddress' rows="4" />
                                <x-input-error for="customerAddress" class="mt-2" />
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-between w-full pb-4 mt-6 space-x-4">
                        <x-button>@lang('app.continue')</x-button>
                        <x-button-cancel wire:click="$toggle('showCustomerNameModal')"
                            wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
                    </div>
                </form>
            @endif
        </x-slot>

    </x-dialog-modal>

    <!-- Pickup DateTime Dialog Modal -->
    <x-dialog-modal wire:model.live="showPickupDateTimeModal" maxWidth="sm">
        <x-slot name="title">
            @lang('modules.order.pickUpDateTime')
        </x-slot>

        <x-slot name="content">
            <form wire:submit="savePickupDateTime">
                @csrf
                <div class="space-y-4">
                    <div>
                        {{-- <x-label for="pickupDateTime" value="{{ __('modules.order.selectPickupDateTime') }}"  class="m-2"/> --}}
                         <input type="datetime-local" id="delivery_datetime"
                            class="px-3 py-2 text-sm text-gray-900 bg-gray-100 border border-gray-300 rounded-md dark:border-gray-700 dark:bg-gray-600 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            wire:model.defer="deliveryDateTime" min="{{ now($restaurant->timezone ?? config('app.timezone'))->format('Y-m-d\TH:i') }}" max="{{ $maxDate }}"
                            value="{{ $defaultDate }}" style="color-scheme: light dark;" />
                        <x-input-error for="pickupDateTime" class="mt-2" />
                    </div>
                </div>
                <div class="flex justify-between w-full pb-4 mt-6 space-x-4">
                    <x-button>@lang('app.continue')</x-button>
                    <x-button-cancel wire:click="$toggle('showPickupDateTimeModal')"
                        wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
                </div>
            </form>

        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showTableModal" maxWidth="2xl">
        <x-slot name="title">
            @lang('modules.table.selectTable')
        </x-slot>

        <x-slot name="content">
            @if ($showTableModal && $getTable)
                <!-- Card Section -->
                <div class="col-span-2 space-y-8">
                    @forelse ($tables as $area)
                        <div class="flex flex-col gap-3 space-y-3 sm:gap-4"
                            wire:key='area-table-{{ $loop->index }}'>
                            <h3 class="inline-flex items-center gap-2 font-medium f-15 dark:text-neutral-200">
                                {{ $area->area_name }}
                                <span
                                    class="px-2 py-1 text-sm text-gray-800 border border-gray-300 rounded bg-slate-100 ">{{ $area->tables->count() }}
                                    @lang('modules.table.table')</span>
                            </h3>
                            <!-- Card -->

                            <div class="grid grid-cols-2 gap-3 md:grid-cols-4 sm:gap-4">
                                @foreach ($area->tables as $item)
                                    <a @class([
                                        'group cursor-pointer flex items-center justify-center border shadow-sm rounded-lg hover:shadow-md transition dark:bg-gray-700 dark:border-gray-600',
                                        'bg-red-50' => $item->status == 'inactive',
                                        'bg-white' => $item->status == 'active',
                                    ]) wire:key='table-{{ $loop->index }}'
                                        wire:click="selectTableOrder('{{ $item->hash }}')">
                                        <div class="p-3">
                                            <div class="flex flex-col items-center justify-center space-y-2">
                                                @if ($item->status == 'inactive')
                                                    <div class="inline-flex gap-1 text-xs font-semibold text-red-600">
                                                        @lang('app.inactive')
                                                    </div>
                                                @endif
                                                <div @class([
                                                    'p-2 rounded-lg tracking-wide ',
                                                    ' bg-green-100 text-green-600' => $item->available_status == 'available',
                                                    'bg-red-100 text-red-600' => $item->available_status == 'reserved',
                                                    'bg-blue-100 text-blue-600' => $item->available_status == 'running',
                                                ])>
                                                    <h3 wire:loading.class.delay='opacity-50'
                                                        @class(['font-semibold'])>
                                                        {{ $item->table_code }}
                                                    </h3>
                                                </div>
                                                <p @class(['text-xs font-medium dark:text-neutral-200 text-gray-500'])>
                                                    {{ $item->seating_capacity }} @lang('modules.table.seats')
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- End Card -->
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div>
                            @lang('messages.noTablesFound')
                        </div>
                    @endforelse

                </div>
                <!-- End Card Section -->
            @endif
        </x-slot>

    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showVariationModal" maxWidth="xl">
        <x-slot name="title">
            @lang('modules.menu.itemVariations')
        </x-slot>

        <x-slot name="content">
            @if ($menuItem)
                @livewire('pos.itemVariations', ['menuItem' => $menuItem], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-button-cancel wire:click="$toggle('showVariationModal')" wire:loading.attr="disabled" />
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showCartVariationModal" maxWidth="sm">
        <x-slot name="title">
            @lang('modules.menu.itemVariations')
        </x-slot>

        <x-slot name="content">
            @if ($menuItem)
                @livewire('shop.cartItemVariations', ['menuItem' => $menuItem, 'orderItemQty' => $orderItemQty], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-button-cancel wire:click="$toggle('showCartVariationModal')" wire:loading.attr="disabled" />
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showItemDetailModal" maxWidth="sm">
        <x-slot name="title">
            @lang('modules.menu.itemDescription')
        </x-slot>

        <x-slot name="content">
            @if ($selectedItem)
                <div class="flex flex-col gap-2">
                    <div class="flex flex-col gap-2">
                        @if ($restaurant && !$restaurant->hide_menu_item_image_on_customer_site)

                            <img src="{{ $selectedItem->item_photo_url }}" alt="{{ $selectedItem->item_name }}"
                                class="object-cover w-full rounded-md">
                        @endif
                        <div class="flex flex-col gap-1">
                            <h3 class="text-lg font-semibold dark:text-white">{{ $selectedItem->item_name }}</h3>
                            @if (strlen($selectedItem->description) > 100)
                                <div x-data="{ expanded: false }">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        <span
                                            x-show="!expanded">{{ Str::limit($selectedItem->description, 100) }}</span>
                                        <span x-show="expanded">{{ $selectedItem->description }}</span>
                                    </p>
                                    <button @click="expanded = !expanded"
                                        class="mt-1 text-sm font-medium text-skin-base">
                                        <span x-text="expanded ? '@lang('modules.menu.showLess')' : '@lang('modules.menu.showMore')'"></span>
                                    </button>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $selectedItem->description }}
                                </p>
                            @endif

                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    @lang('modules.menu.preparationTime')
                                    {{ $selectedItem->preparation_time }} @lang('modules.menu.minutes')
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-button-cancel wire:click="$toggle('showItemDetailModal')" wire:loading.attr="disabled" />
        </x-slot>
    </x-dialog-modal>

    @if ($paymentOrder)
        <x-dialog-modal wire:model.live="showPaymentModal" maxWidth="md">
            <x-slot name="title">
                @lang('modules.order.chooseGateway')
            </x-slot>

            <x-slot name="content">
                <div
                    class="flex items-center justify-between p-2 mb-6 rounded-md cursor-pointer bg-gray-50 dark:bg-gray-800">
                    <div class="flex items-center min-w-0">
                        <div>
                            <div class="font-medium text-gray-700 truncate dark:text-white">
                                    {{ $paymentOrder->show_formatted_order_number }}
                            </div>
                        </div>
                    </div>
                    <div class="inline-flex flex-col text-base font-semibold text-right text-gray-900 dark:text-white">
                        <div>{{ currency_format($paymentOrder->total, $restaurant->currency_id) }}</div>
                    </div>
                </div>

                @if ($showQrCode || $showPaymentDetail)
                    <x-secondary-button wire:click="{{ $showQrCode ? 'toggleQrCode' : 'togglePaymenntDetail' }}">
                        <span class="ml-2">@lang('modules.billing.showOtherPaymentOption')</span>
                    </x-secondary-button>

                    <div class="flex items-center mt-2">
                        @if ($showQrCode)
                            <img src="{{ $paymentGateway->qr_code_image_url }}" alt="QR Code Preview"
                                class="object-cover rounded-md h-30 w-30">
                        @else
                            <span class="p-3 font-bold text-gray-700 rounded">@lang('modules.billing.accountDetails')</span>
                            <span>{{ $paymentGateway->offline_payment_detail }}</span>
                        @endif
                    </div>
                 @else
                    <div class="grid items-center w-full grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                        @if ($paymentGateway->stripe_status)
                            <x-secondary-button wire:click='initiateStripePayment({{ $paymentOrder->id }})'>
                                <span class="inline-flex items-center">
                                    <svg height="21" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 468 222.5"
                                        xml:space="preserve">
                                        <path
                                            d="M414 113.4c0-25.6-12.4-45.8-36.1-45.8-23.8 0-38.2 20.2-38.2 45.6 0 30.1 17 45.3 41.4 45.3 11.9 0 20.9-2.7 27.7-6.5v-20c-6.8 3.4-14.6 5.5-24.5 5.5-9.7 0-18.3-3.4-19.4-15.2h48.9c0-1.3.2-6.5.2-8.9m-49.4-9.5c0-11.3 6.9-16 13.2-16 6.1 0 12.6 4.7 12.6 16zm-63.5-36.3c-9.8 0-16.1 4.6-19.6 7.8l-1.3-6.2h-22v116.6l25-5.3.1-28.3c3.6 2.6 8.9 6.3 17.7 6.3 17.9 0 34.2-14.4 34.2-46.1-.1-29-16.6-44.8-34.1-44.8m-6 68.9c-5.9 0-9.4-2.1-11.8-4.7l-.1-37.1c2.6-2.9 6.2-4.9 11.9-4.9 9.1 0 15.4 10.2 15.4 23.3 0 13.4-6.2 23.4-15.4 23.4m-71.3-74.8 25.1-5.4V36l-25.1 5.3zm0 7.6h25.1v87.5h-25.1zm-26.9 7.4-1.6-7.4h-21.6v87.5h25V97.5c5.9-7.7 15.9-6.3 19-5.2v-23c-3.2-1.2-14.9-3.4-20.8 7.4m-50-29.1-24.4 5.2-.1 80.1c0 14.8 11.1 25.7 25.9 25.7 8.2 0 14.2-1.5 17.5-3.3V135c-3.2 1.3-19 5.9-19-8.9V90.6h19V69.3h-19zM79.3 94.7c0-3.9 3.2-5.4 8.5-5.4 7.6 0 17.2 2.3 24.8 6.4V72.2c-8.3-3.3-16.5-4.6-24.8-4.6C67.5 67.6 54 78.2 54 95.9c0 27.6 38 23.2 38 35.1 0 4.6-4 6.1-9.6 6.1-8.3 0-18.9-3.4-27.3-8v23.8c9.3 4 18.7 5.7 27.3 5.7 20.8 0 35.1-10.3 35.1-28.2-.1-29.8-38.2-24.5-38.2-35.7"
                                            style="fill-rule:evenodd;clip-rule:evenodd;fill:#635bff" />
                                    </svg>
                                </span>
                            </x-secondary-button>
                        @endif

                        @if ($paymentGateway->razorpay_status)
                            <x-secondary-button wire:click='initiatePayment({{ $paymentOrder->id }})'>
                                <span class="inline-flex items-center">
                                    <svg height="21" version="1.1" id="Layer_1"
                                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        x="0px" y="0px" viewBox="0 0 122.88 26.53"
                                        style="enable-background:new 0 0 122.88 26.53" xml:space="preserve">
                                        <style type="text/css">
                                            <![CDATA[
                                            .strp0 {
                                                fill: #3395FF;
                                            }

                                            .st1 {
                                                fill: #072654;
                                            }
                                            ]]>
                                        </style>
                                        <g>
                                            <polygon class="st1"
                                                points="11.19,9.03 7.94,21.47 0,21.47 1.61,15.35 11.19,9.03" />
                                            <path class="st1"
                                                d="M28.09,5.08C29.95,5.09,31.26,5.5,32,6.33s0.92,2.01,0.51,3.56c-0.27,1.06-0.82,2.03-1.59,2.8 c-0.8,0.8-1.78,1.38-2.87,1.68c0.83,0.19,1.34,0.78,1.5,1.79l0.03,0.22l0.6,5.09h-3.7l-0.62-5.48c-0.01-0.18-0.06-0.36-0.15-0.52 c-0.09-0.16-0.22-0.29-0.37-0.39c-0.31-0.16-0.65-0.24-1-0.25h-0.21h-2.28l-1.74,6.63h-3.46l4.3-16.38H28.09L28.09,5.08z M122.88,9.37l-4.4,6.34l-5.19,7.52l-0.04,0.04l-1.16,1.68l-0.04,0.06L112,25.09l-1,1.44h-3.44l4.02-5.67l-1.82-11.09h3.57 l0.9,7.23l4.36-6.19l0.06-0.09l0.07-0.1l0.07-0.09l0.54-1.15H122.88L122.88,9.37z M92.4,10.25c0.66,0.56,1.09,1.33,1.24,2.19 c0.18,1.07,0.1,2.18-0.21,3.22c-0.29,1.15-0.78,2.23-1.46,3.19c-0.62,0.88-1.42,1.61-2.35,2.13c-0.88,0.48-1.85,0.73-2.85,0.73 c-0.71,0.03-1.41-0.15-2.02-0.51c-0.47-0.28-0.83-0.71-1.03-1.22l-0.06-0.2l-1.77,6.75h-3.43l3.51-13.4l0.02-0.06l0.01-0.06 l0.86-3.25h3.35l-0.57,1.88l-0.01,0.08c0.49-0.7,1.15-1.27,1.91-1.64c0.76-0.4,1.6-0.6,2.45-0.6C90.84,9.43,91.7,9.71,92.4,10.25 L92.4,10.25z M88.26,12.11c-0.4-0.01-0.8,0.07-1.18,0.22c-0.37,0.15-0.71,0.38-1,0.66c-0.68,0.7-1.15,1.59-1.36,2.54 c-0.3,1.11-0.28,1.95,0.02,2.53c0.3,0.58,0.87,0.88,1.72,0.88c0.81,0.02,1.59-0.29,2.18-0.86c0.66-0.69,1.12-1.55,1.33-2.49 c0.29-1.09,0.27-1.96-0.03-2.57S89.08,12.11,88.26,12.11L88.26,12.11z M103.66,9.99c0.46,0.29,0.82,0.72,1.02,1.23l0.07,0.19 l0.44-1.66h3.36l-3.08,11.7h-3.37l0.45-1.73c-0.51,0.61-1.15,1.09-1.87,1.42c-0.7,0.32-1.45,0.49-2.21,0.49 c-0.88,0.04-1.76-0.21-2.48-0.74c-0.66-0.52-1.1-1.28-1.24-2.11c-0.18-1.06-0.12-2.14,0.19-3.17c0.3-1.15,0.8-2.24,1.49-3.21 c0.63-0.89,1.44-1.64,2.38-2.18c0.86-0.5,1.84-0.77,2.83-0.77C102.36,9.43,103.06,9.61,103.66,9.99L103.66,9.99z M101.92,12.14 c-0.41,0-0.82,0.08-1.19,0.24c-0.38,0.16-0.72,0.39-1.01,0.68c-0.67,0.71-1.15,1.59-1.36,2.55c-0.28,1.08-0.28,1.9,0.04,2.49 c0.31,0.59,0.89,0.87,1.75,0.87c0.4,0.01,0.8-0.07,1.18-0.22s0.71-0.38,1-0.66c0.59-0.63,1.02-1.38,1.26-2.22l0.08-0.31 c0.3-1.11,0.29-1.96-0.03-2.53C103.33,12.44,102.76,12.14,101.92,12.14L101.92,12.14z M81.13,9.63l0.22,0.09l-0.86,3.19 c-0.49-0.26-1.03-0.39-1.57-0.39c-0.82-0.03-1.62,0.24-2.27,0.75c-0.56,0.48-0.97,1.12-1.18,1.82l-0.07,0.27l-1.6,6.11h-3.42 l3.1-11.7h3.37l-0.44,1.72c0.42-0.58,0.96-1.05,1.57-1.4c0.68-0.39,1.44-0.59,2.22-0.59C80.51,9.48,80.83,9.52,81.13,9.63 L81.13,9.63z M68.5,10.19c0.76,0.48,1.31,1.24,1.52,2.12c0.25,1.06,0.21,2.18-0.11,3.22c-0.3,1.18-0.83,2.28-1.58,3.22 c-0.71,0.91-1.61,1.63-2.64,2.12c-1.05,0.49-2.19,0.74-3.35,0.73c-1.22,0-2.22-0.24-3-0.73c-0.77-0.48-1.32-1.24-1.54-2.12 c-0.24-1.06-0.2-2.18,0.11-3.22c0.3-1.17,0.83-2.27,1.58-3.22c0.71-0.9,1.62-1.63,2.66-2.12c1.06-0.49,2.22-0.75,3.39-0.73 C66.57,9.41,67.6,9.67,68.5,10.19L68.5,10.19z M64.84,12.1c-0.81-0.01-1.59,0.3-2.18,0.86c-0.61,0.58-1.07,1.43-1.36,2.57 c-0.6,2.29-0.02,3.43,1.74,3.43c0.8,0.02,1.57-0.29,2.15-0.85c0.6-0.57,1.04-1.43,1.34-2.58c0.3-1.13,0.31-1.98,0.01-2.57 C66.25,12.37,65.68,12.1,64.84,12.1L64.84,12.1z M57.89,9.76l-0.6,2.32l-7.55,6.67h6.06l-0.72,2.73H45.05l0.63-2.41l7.43-6.57 h-5.65l0.72-2.73H57.89L57.89,9.76z M40.96,9.99c0.46,0.29,0.82,0.72,1.02,1.23l0.07,0.19l0.44-1.66h3.37l-3.07,11.7h-3.37 l0.45-1.73c-0.51,0.6-1.14,1.08-1.85,1.41s-1.48,0.5-2.27,0.5c-0.88,0.04-1.74-0.22-2.45-0.74c-0.66-0.52-1.1-1.28-1.24-2.11 c-0.18-1.06-0.12-2.14,0.19-3.17c0.29-1.15,0.8-2.24,1.49-3.21c0.63-0.89,1.44-1.64,2.37-2.18c0.86-0.5,1.84-0.76,2.83-0.76 C39.66,9.44,40.36,9.62,40.96,9.99L40.96,9.99z M39.23,12.14c-0.41,0-0.81,0.08-1.19,0.24c-0.38,0.16-0.72,0.39-1.01,0.68 c-0.68,0.71-1.15,1.59-1.36,2.55c-0.28,1.08-0.27,1.9,0.04,2.49c0.31,0.59,0.89,0.87,1.75,0.87c0.4,0.01,0.8-0.07,1.18-0.22 c0.37-0.15,0.72-0.38,1-0.66c0.59-0.62,1.03-1.38,1.26-2.22l0.08-0.31c0.29-1.11,0.26-1.94-0.03-2.53 C40.64,12.44,40.06,12.14,39.23,12.14L39.23,12.14z M26.85,7.81h-3.21l-1.13,4.28h3.21c1.01,0,1.81-0.17,2.35-0.52 c0.57-0.37,0.98-0.95,1.13-1.63c0.2-0.72,0.11-1.27-0.27-1.62C28.55,7.99,27.86,7.81,26.85,7.81L26.85,7.81z" />
                                            <polygon class="strp0"
                                                points="18.4,0 12.76,21.47 8.89,21.47 12.7,6.93 6.86,10.78 7.9,6.95 18.4,0" />
                                        </g>
                                    </svg>
                                </span>
                            </x-secondary-button>
                        @endif

                        @if ($paymentGateway->flutterwave_status)
                            <x-secondary-button wire:click="initiateFlutterwavePayment({{ $paymentOrder->id }})">
                                <span class="inline-flex items-center">
                                    <svg class="h-5 dark:mix-blend-plus-lighter" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 916.7 144.7">
                                        <path
                                            d="M280.5 33.8h16.1v82.9h-16.1zM359 87.3c0 11.4-7.4 16.6-17.2 16.6s-16.4-5.1-16.4-16V58.3h-16.1v33.3c0 16.6 10.4 26.3 27.7 26.3 10.9 0 16.9-4 21-8.5h.9l1.4 7.4h14.8V58.3H359zm158 17.9c-11.8 0-18.4-5.4-19.5-13.2h51.1c.2-1.6.4-3.3.3-4.9-.1-21-16-29.9-33-29.9-19.7 0-34.6 11.8-34.6 30.8 0 18.1 14.2 29.9 35.6 29.9 17.9 0 29.8-7.9 32.2-20.1h-15.9c-1.8 4.8-7.5 7.4-16.2 7.4m-1-35.3c10.3 0 16.2 4.6 17.2 11h-35.3c1.5-6.2 7.5-11 18.1-11m60.4-3.2h-1l-1.5-8.4h-14.6v58.4h16.1V91.6c0-11.3 6.5-17.6 18.7-17.6q3.3 0 6.6.6V58.3h-2.2c-10.9 0-17.5 2.3-22.1 8.4m103.3 31.8h-.9L665 62h-16.6l-13.5 36.4h-1.1L621 58.3h-16l19.7 58.4h17.5l14-37.2h1l13.8 37.2h17.6l19.7-58.4h-16zm92.7 1.2V80.2c0-15.9-13.4-23-30.1-23-17.7 0-28.8 8.4-30.3 21h16.1c1.2-5.5 5.8-8.5 14.2-8.5s14 3.2 14 9.6v1.5l-26.3 2c-12.1.9-21 6.3-21 17.8 0 11.8 10.2 17.4 25.1 17.4 12.1 0 19.4-3.4 23.9-8.4h.8c2.5 5.7 7.7 7.3 13.2 7.3h6.8V105h-1.5c-3.3-.2-4.9-1.8-4.9-5.3m-16.1-6.2c0 9.2-11 12.3-20.4 12.3-6.4 0-10.6-1.6-10.6-6.1 0-4 3.6-5.9 9-6.4l22.1-1.6zM832 58.3l-18.8 42.3h-1l-19.1-42.3h-17.4l27.2 58.4h19.3l27.1-58.4zm68.8 39.5c-2 4.8-7.7 7.4-16.3 7.4-11.8 0-18.4-5.4-19.5-13.2h51.1c.2-1.6.4-3.3.3-4.9-.1-21-16-29.9-33-29.9-19.7 0-34.5 11.8-34.5 30.8 0 18.1 14.2 29.9 35.6 29.9 17.9 0 29.8-7.9 32.2-20.1zm-17.4-27.9c10.3 0 16.2 4.6 17.2 11h-35.3c1.5-6.2 7.4-11 18.1-11M254.4 54c0-5.1 3.6-7.3 8.3-7.3 2.2 0 4.3.3 6.4.9l2.7-11.7c-3.9-1.4-8-2.1-12.1-2.1-11.9 0-21.5 6.3-21.5 19.4v5.1h-13.9v12.8h13.9v45.6h16.2V71.1h18.2V58.3h-18.2zm156.4-12.1h-15l-.8 16.5h-12.7v12.8h12.4V100c0 9.8 5 18 20 18 3.9 0 7.8-.4 11.6-1.3v-12.3c-2.2.5-4.4.8-6.7.8-8 0-8.8-4.6-8.8-8.1v-26h16V58.3h-16zm50.6 0h-14.9l-.8 16.5H433v12.8h12.4V100c0 9.8 5 18 20 18 3.9 0 7.7-.5 11.5-1.3v-12.3c-2.2.5-4.4.8-6.7.8-8 0-8.8-4.6-8.8-8.1v-26h16V58.3h-16.1V41.9z"
                                            style="fill:#2a3362" />
                                        <path
                                            d="M0 31.6c0-9.4 2.7-17.4 8.5-23.1l10 10C7.4 29.6 17.1 64.1 48.8 95.8s66.2 41.4 77.3 30.3l10 10c-18.8 18.8-61.5 5.4-97.3-30.3C14 80.9 0 52.8 0 31.6"
                                            style="fill:#009a46" />
                                        <path
                                            d="M63.1 144.7c-9.4 0-17.4-2.7-23.1-8.5l10-10c11.1 11.1 45.6 1.4 77.3-30.3s41.4-66.2 30.3-77.3l10-10c18.8 18.8 5.4 61.5-30.3 97.3-24.9 24.8-53.1 38.8-74.2 38.8"
                                            style="fill:#ff5805" />
                                        <path
                                            d="M140.5 91.6C134.4 74.1 122 55.4 105.6 39 69.8 3.2 27.1-10.1 8.3 8.6 7 10 8.2 13.3 10.9 16s6.1 3.9 7.4 2.6c11.1-11.1 45.6-1.4 77.3 30.3 15 15 26.2 31.8 31.6 47.3 4.7 13.6 4.3 24.6-1.2 30.1-1.3 1.3-.2 4.6 2.6 7.4s6.1 3.9 7.4 2.6c9.6-9.7 11.2-25.6 4.5-44.7"
                                            style="fill:#f5afcb" />
                                        <path
                                            d="M167.5 8.6C157.9-1 142-2.6 122.9 4c-17.5 6.1-36.2 18.5-52.6 34.9-35.8 35.8-49.1 78.5-30.3 97.3 1.3 1.3 4.7.2 7.4-2.6s3.9-6.1 2.6-7.4c-11.1-11.1-1.4-45.6 30.3-77.3 15-15 31.8-26.2 47.2-31.6 13.6-4.7 24.6-4.3 30.1 1.2 1.3 1.3 4.6.2 7.4-2.6s3.9-5.9 2.5-7.3"
                                            style="fill:#ff9b00" />
                                    </svg>
                                </span>
                            </x-secondary-button>
                        @endif

                        @if ($paymentGateway->paypal_status)
                            <x-secondary-button wire:click='initiatePaypalPayment({{ $paymentOrder->id }})'>
                                <span class="inline-flex items-center">
                                    <svg height="21" viewBox="0 0 916.7 144.7" class="h-6 w-22"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <defs>
                                            <style>
                                                .text {
                                                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                                                    font-size: 80px;
                                                    font-weight: bold;
                                                }

                                                .dark-blue {
                                                    fill: #002E6D;
                                                }

                                                .blue {
                                                    fill: #009CDE;
                                                }
                                            </style>
                                        </defs>
                                        <!-- P Shape -->
                                        <path class="dark-blue" d="M60,30 h50 a30,30 0 0 1 0,60 h-35 l-10,60 h-30z" />
                                        <!-- Overlay light P -->
                                        <path class="blue" d="M75,40 h25 a20,20 0 0 1 0,40 h-20 l-8,40 h-20z" />
                                        <!-- PayPal Text -->
                                        <text x="140" y="95" class="text">
                                            <tspan class="dark-blue">Pay</tspan>
                                            <tspan class="blue">Pal</tspan>
                                        </text>
                                    </svg>

                                </span>
                            </x-secondary-button>
                        @endif

                        @if ($paymentGateway->payfast_status)
                            <x-secondary-button wire:click='initiatePayfastPayment({{ $paymentOrder->id }})'>
                                <span class="inline-flex items-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 6 L14 12 L8 18" fill="none" stroke="#E63950"
                                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    @lang('modules.billing.payfast')

                                </span>
                            </x-secondary-button>
                        @endif

                        @if ($paymentGateway->paystack_status)
                            <x-secondary-button wire:click='initiatePaystackPayment({{ $paymentOrder->id }})'>
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                        width="24" height="24" fill="#0AA5FF">
                                        <path
                                            d="M2 3.6c0-.331.269-.6.6-.6H21.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6V3.6Zm0 4.8c0-.331.269-.6.6-.6H15.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6V8.4Zm0 4.8c0-.331.269-.6.6-.6H21.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6v-1.8Zm0 4.8c0-.331.269-.6.6-.6H15.4c.331 0 .6.269.6.6v1.8a.6.6 0 0 1-.6.6H2.6a.6.6 0 0 1-.6-.6v-1.8Z"
                                            fill-rule="evenodd" />
                                    </svg>
                                    @lang('modules.billing.paystack')

                                </span>
                            </x-secondary-button>
                        @endif

                        @if ($paymentGateway->xendit_status)
                            <x-secondary-button wire:click='initiateXenditPayment({{ $paymentOrder->id }})'>
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" id="Xendit--Streamline-Simple-Icons" height="24" width="24">
                                            <desc>
                                            Xendit Streamline Icon: https://streamlinehq.com
                                            </desc>
                                            <title>Xendit</title>
                                            <path d="M11.781 2.743H7.965l-5.341 9.264 5.341 9.263 -1.312 2.266L0 12.007 6.653 0.464h6.454l-1.326 2.279Zm-5.128 2.28 1.312 -2.28L9.873 6.03 8.561 8.296 6.653 5.023Zm9.382 -2.28 1.312 2.28L7.965 21.27l-1.312 -2.279 9.382 -16.248Zm-5.128 20.793 1.298 -2.279h3.83L14.1 17.931l1.312 -2.267 1.926 3.337 4.038 -6.994 -5.341 -9.264L17.347 0.464 24 12.007l-6.653 11.529h-6.44Z" fill="#000000" stroke-width="1"></path>
                                        </svg>
                                        @lang('modules.billing.xendit')
                                </span>
                            </x-secondary-button>
                        @endif

                        @if ($paymentGateway->is_qr_payment_enabled && $paymentGateway->qr_code_image_url)
                            <!-- Button -->
                            <x-secondary-button wire:click="toggleQrCode">
                                <span class="inline-flex items-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g stroke-width="0" />
                                        <g stroke-linecap="round" stroke-linejoin="round" />
                                        <path fill="none" d="M0 0h24v24H0z" />
                                        <path
                                            d="M16 17v-1h-3v-3h3v2h2v2h-1v2h-2v2h-2v-3h2v-1zm5 4h-4v-2h2v-2h2zM3 3h8v8H3zm2 2v4h4V5zm8-2h8v8h-8zm2 2v4h4V5zM3 13h8v8H3zm2 2v4h4v-4zm13-2h3v2h-3zM6 6h2v2H6zm0 10h2v2H6zM16 6h2v2h-2z" />
                                    </svg>
                                    <span class="ml-2">@lang('modules.billing.paybyQr')</span>
                                </span>
                            </x-secondary-button>
                        @endif

                        @if ($paymentGateway->is_offline_payment_enabled && $paymentGateway->offline_payment_detail)
                            <!-- Button -->
                            <x-secondary-button wire:click="togglePaymenntDetail">
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12 15V17M6 7H18C18.5523 7 19 7.44772 19 8V16C19 16.5523 18.5523 17 18 17H6C5.44772 17 5 16.5523 5 16V8C5 7.44772 5.44772 7 6 7ZM6 7L18 7C18.5523 7 19 6.55228 19 6V4C19 3.44772 18.5523 3 18 3H6C5.44772 3 5 3.44772 5 4V6C5 6.55228 5.44772 7 6 7Z"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M12 11C13.1046 11 14 10.1046 14 9C14 7.89543 13.1046 7 12 7C10.8954 7 10 7.89543 10 9C10 10.1046 10.8954 11 12 11Z"
                                            stroke="currentColor" stroke-width="2" />
                                    </svg>

                                    <span class="ml-2">@lang('modules.billing.bankTransfer')</span>
                                </span>
                            </x-secondary-button>
                        @endif

                        @if ($paymentGateway->is_cash_payment_enabled)
                            <x-secondary-button wire:click="placeOrder(false, {{ $paymentOrder->id }}, 'cash')">
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                            d="M8 7V6a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1M3 18v-7a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                                    </svg>
                                    <span class="ml-2">@lang('modules.order.payViaCash')</span>
                                </span>
                            </x-secondary-button>
                        @endif
                    </div>
                @endif

            </x-slot>

            <x-slot name="footer">
                <x-button-cancel wire:click="hidePaymentModal" wire:loading.attr="disabled" />
                @if ($showQrCode)
                    <x-button class="ml-3"
                        wire:click="placeOrder(false, {{ $paymentOrder->id }}, '{{ $showQrCode ? 'upi' : 'others' }}')"
                        wire:loading.attr="disabled">@lang('modules.billing.paymentDone')</x-button>

                @elseif ($showPaymentDetail)
                    <x-button class="ml-3"
                        wire:click="placeOrder(false, {{ $paymentOrder->id }}, 'bank_transfer')"
                        wire:loading.attr="disabled">@lang('modules.billing.paymentDone')</x-button>
                @endif
            </x-slot>
        </x-dialog-modal>
    @endif

    <x-dialog-modal wire:model.live="showModifiersModal" maxWidth="xl">
        <x-slot name="title">
            @lang('modules.modifier.itemModifiers')
        </x-slot>

        <x-slot name="content">
            @if ($selectedModifierItem)
                @livewire('pos.itemModifiers', ['menuItemId' => $selectedModifierItem], key(str()->random(50)))
            @endif
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showItemVariationsModal" maxWidth="xl">
        <x-slot name="title">
            @lang('modules.menu.itemVariations')
        </x-slot>

        <x-slot name="content">
            @if ($menuItem)
                <div>
                    <div class="flex flex-col">
                        <div class="flex gap-4 mb-4">
                            @if ($restaurant && !$restaurant->hide_menu_item_image_on_customer_site)

                                <img class="w-16 h-16 rounded-md" src="{{ $menuItem->item_photo_url }}"
                                    alt="{{ $menuItem->item_name }}">
                            @endif
                            <div class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                <div
                                    class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                    <img src="{{ asset('img/' . $menuItem->type . '.svg') }}" class="h-4 mr-2"
                                        title="@lang('modules.menu.' . $menuItem->type)" alt="" />
                                    {{ $menuItem->item_name }}
                                </div>
                                <div class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                    {{ $menuItem->description }}</div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden shadow">
                                    <table
                                        class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                                        <thead class="bg-gray-100 dark:bg-gray-700">
                                            <tr>
                                                <th scope="col"
                                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                                    @lang('modules.menu.itemName')
                                                </th>
                                                <th scope="col"
                                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                                    @lang('modules.menu.setPrice')
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                                            wire:key='menu-item-list-{{ microtime() }}'>

                                            @foreach ($menuItem->variations as $item)
                                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700"
                                                    wire:key='menu-item-{{ $item->id . microtime() }}'>
                                                    <td
                                                        class="flex items-center p-4 mr-12 space-x-6 whitespace-nowrap">
                                                        <div
                                                            class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                                            <div
                                                                class="inline-flex items-center text-base text-gray-900 dark:text-white">
                                                                {{ $item->variation }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td
                                                        class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                                        {{ $item->price ? currency_format($item->price, $restaurant->currency_id) : '--' }}
                                                    </td>

                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-button-cancel wire:click="$toggle('showItemVariationsModal')" wire:loading.attr="disabled" />
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showDeliveryAddressModal" maxWidth="4xl">
        <x-slot name="title"></x-slot>

        <x-slot name="content">
            @if ($shopBranch?->deliverySetting)
                @livewire('customer.location-selector', ['shopBranch' => $shopBranch, 'customer' => $customer, 'orderGrandTotal' => $total, 'maxPreparationTime' => $maxPreparationTime, 'currencyId' => $restaurant->currency_id], key(str()->random(50)))
            @endif
        </x-slot>
    </x-dialog-modal>

    @script
        <script>
            $wire.on('paymentInitiated', (payment) => {
                payViaRazorpay(payment);
            });

            $wire.on('stripePaymentInitiated', (payment) => {
                document.getElementById('order_payment').value = payment.payment.id;
                document.getElementById('order-payment-form').submit();
            });

            function payViaRazorpay(payment) {

                var options = {
                    "key": "{{ $restaurant->paymentGateways->razorpay_key }}", // Enter the Key ID generated from the Dashboard
                    "amount": (parseFloat(payment.payment.amount) * 100),
                    "currency": "{{ $restaurant->currency->currency_code }}",
                    "description": "Order Payment",
                    "image": "{{ $restaurant->logoUrl }}",
                    "order_id": payment.payment.razorpay_order_id,
                    "handler": function(response) {
                        Livewire.dispatch('razorpayPaymentCompleted', [response.razorpay_payment_id, response
                            .razorpay_order_id,
                            response.razorpay_signature
                        ]);
                    },
                    "modal": {
                        "ondismiss": function() {
                            if (confirm("Are you sure, you want to close the form?")) {
                                txt = "You pressed OK!";
                                console.log("Checkout form closed by the user");
                            } else {
                                txt = "You pressed Cancel!";
                                console.log("Complete the Payment")
                            }
                        }
                    }
                };
                var rzp1 = new Razorpay(options);
                rzp1.on('payment.failed', function(response) {
                    console.log(response);
                });
                rzp1.open();
            }
        </script>
    @endscript

</div>
