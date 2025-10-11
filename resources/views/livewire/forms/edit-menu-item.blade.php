<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <!-- Language Selection -->
            @if(count($languages) > 1)
            <div class="mb-4">
                <x-label for="language" :value="__('modules.menu.selectLanguage')" />
                <div class="relative mt-1">
                    @php
                        $languageSettings = collect(App\Models\LanguageSetting::LANGUAGES)
                            ->keyBy('language_code')
                            ->map(function ($lang) {
                                return [
                                    'flag_url' => asset('flags/1x1/' . strtolower($lang['flag_code']) . '.svg'),
                                    'name' => App\Models\LanguageSetting::LANGUAGES_TRANS[$lang['language_code']] ?? $lang['language_name']
                                ];
                            });
                    @endphp
                    <x-select class="block pl-10 w-full" wire:model.live="currentLanguage">
                        @foreach($languages as $code => $name)
                            <option value="{{ $code }}"
                                    data-flag="{{ $languageSettings->get($code)['flag_url'] ?? asset('flags/1x1/' . strtolower($code) . '.svg') }}"
                                    class="flex items-center py-2">
                                {{ $languageSettings->get($code)['name'] ?? $name }}
                            </option>
                        @endforeach
                    </x-select>

                    {{-- Current Selected Flag --}}
                    @php
                        $currentFlagCode = collect(App\Models\LanguageSetting::LANGUAGES)
                            ->where('language_code', $currentLanguage)
                            ->first()['flag_code'] ?? $currentLanguage;
                    @endphp
                    <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                        <img src="{{ asset('flags/1x1/' . strtolower($currentFlagCode) . '.svg') }}"
                            alt="{{ $currentLanguage }}"
                            class="object-cover w-5 h-5 rounded-sm"
                        />
                    </div>
                </div>
            </div>
            @endif


            <!-- Item Name and Description with Translation -->
            <div class="mb-4">
                <x-label for="itemName" :value="__('modules.menu.itemName') . ' (' . $languages[$currentLanguage] . ')'" />
                <x-input id="itemName" class="block mt-1 w-full" type="text" placeholder="{{ __('placeholders.menuItemNamePlaceholder') }}" wire:model="itemName" wire:change="updateTranslation" />
                <x-input-error for="translationNames.{{ $globalLocale }}" class="mt-2" />
            </div>

            <div>
                <x-label for="itemDescription" :value="__('modules.menu.itemDescription') . ' (' . $languages[$currentLanguage] . ')'" />
                <x-textarea class="block mt-1 w-full" :placeholder="__('placeholders.itemDescriptionPlaceholder')"
                    wire:model='itemDescription' rows='2' wire:change="updateTranslation" data-gramm="false"/>
                <x-input-error for="itemDescription" class="mt-2" />
            </div>

            <!-- Translation Preview -->
            <div>
                @if(count($languages) > 1 && (array_filter($translationNames) || array_filter($translationDescriptions)))
                <div class="p-2.5 bg-gray-50 rounded-lg dark:bg-gray-700">
                    <x-label :value="__('modules.menu.translations')" class="mb-2 text-sm last:mb-0" />
                    <div class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($languages as $lang => $langName)
                            @if(!empty($translationNames[$lang]) || !empty($translationDescriptions[$lang]))
                            <div class="flex flex-col gap-1.5 py-2" wire:key="translation-details-{{ $loop->index }}">
                                <div class="flex gap-3 items-center">
                                    <span class="min-w-[80px] text-xs font-medium text-gray-600 dark:text-gray-300">
                                        {{ $languageSettings->get($lang)['name'] ?? strtoupper($lang) }}
                                    </span>
                                    <div class="flex-1">
                                        @if(!empty($translationNames[$lang]))
                                        <div class="mb-1">
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('app.name'):</span>
                                            <span class="ml-1 text-xs text-gray-700 dark:text-gray-200">{{ $translationNames[$lang] }}</span>
                                        </div>
                                        @endif
                                        @if(!empty($translationDescriptions[$lang]))
                                        <div>
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('app.description'):</span>
                                            <span class="ml-1 text-xs text-gray-700 dark:text-gray-200">{{ $translationDescriptions[$lang] }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label for="menu" :value="__('modules.menu.chooseMenu')"/>
                    <x-select id="menu" class="block mt-1 w-full" wire:model="menu">
                        <option value="">--</option>
                        @foreach ($menus as $item)
                            <option value="{{ $item->id }}">{{ $item->menu_name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="menu" class="mt-2"/>
                </div>

                <div>
                    <x-label for="itemCategory" :value="__('modules.menu.itemCategory')"/>
                    <x-select id="itemCategory" name="item_category_id" class="block mt-1 w-full"
                              wire:model="itemCategory">
                        <option value="">--</option>
                        @foreach ($categoryList as $item)
                            <option value="{{ $item->id }}">{{ $item->category_name }}</option>
                        @endforeach

                        <x-slot name="append">
                            <button class="text-sm font-semibold border-l-0"
                                    wire:click="showMenuCategoryModal" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-gear-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                                </svg>
                            </button>
                        </x-slot>
                    </x-select>
                    <x-input-error for="itemCategory" class="mt-2"/>
                </div>
            </div>

            <div>
                <ul class="grid grid-cols-3 gap-2 w-full">
                    <li>
                        <input type="radio" id="typeVeg" name="itemType" value="veg" class="hidden peer"
                               wire:model='itemType'>
                        <label for="typeVeg"
                               class="inline-flex justify-between items-center p-2 w-full text-sm font-medium text-gray-600 bg-white rounded-lg border-2 border-gray-200 cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-skin-base peer-checked:border-skin-base peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                            <img src="{{ asset('img/veg.svg')}}" class="mr-1 h-5"/>
                            @lang('modules.menu.typeVeg')
                        </label>
                    </li>
                    <li>
                        <input type="radio" id="typeNonVeg" name="itemType" value="non-veg" class="hidden peer"
                               wire:model='itemType'/>
                        <label for="typeNonVeg"
                               class="inline-flex justify-between items-center p-2 w-full text-sm font-medium text-gray-600 bg-white rounded-lg border-2 border-gray-200 cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-skin-base peer-checked:border-skin-base peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                            <img src="{{ asset('img/non-veg.svg')}}" class="mr-1 h-5"/>
                            @lang('modules.menu.typeNonVeg')
                        </label>
                    </li>
                    <li>
                        <input type="radio" id="typeEgg" name="itemType" value="egg" class="hidden peer"
                               wire:model='itemType'>
                        <label for="typeEgg"
                               class="inline-flex justify-between items-center p-2 w-full text-sm font-medium text-gray-600 bg-white rounded-lg border-2 border-gray-200 cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-skin-base peer-checked:border-skin-base peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                            <img src="{{ asset('img/egg.svg')}}" class="mr-1 h-5"/>
                            @lang('modules.menu.typeEgg')
                        </label>
                    </li>
                     <li>
                        <input type="radio" id="typeDrink" name="itemType" value="drink" class="hidden peer"
                            wire:model='itemType'>
                        <label for="typeDrink"
                            class="inline-flex justify-between items-center p-2 w-full text-sm font-medium text-gray-600 bg-white rounded-lg border-2 border-gray-200 cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-skin-base peer-checked:border-skin-base peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                            <img src="{{ asset('img/drink.svg')}}" class="mr-1 h-5" />
                            @lang('modules.menu.typeDrink')
                        </label>
                    </li>
                    <li>
                        <input type="radio" id="typeHalal" name="itemType" value="halal" class="hidden peer"
                            wire:model='itemType'>
                        <label for="typeHalal"
                            class="inline-flex items-center justify-between w-full p-2  bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-skin-base peer-checked:border-skin-base peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 text-sm font-medium">
                            <img src="{{ asset('img/halal.svg') }}" class="h-5 mr-1" />
                             @lang('modules.menu.typeHalal')
                        </label>
                    </li>
                      <li>
                        <input type="radio" id="typeOther" name="itemType" value="other" class="hidden peer"
                            wire:model='itemType'>
                        <label for="typeOther"
                            class="inline-flex justify-between items-center p-2 w-full text-sm font-medium text-gray-600 bg-white rounded-lg border-2 border-gray-200 cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-skin-base peer-checked:border-skin-base peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                            {{-- <img src="{{ asset('img/egg.svg')}}" class="mr-1 h-5" /> --}}
                            @lang('modules.menu.typeOther')
                        </label>
                    </li>
                </ul>


            </div>

            <div>
                <x-label for="preparationTime" :value="__('modules.menu.preparationTime')" />
                <div class="relative mt-1 rounded-md">
                    <x-input id="preparationTime" type="number" step="1" wire:model="preparationTime"
                        class="block w-full text-gray-900 rounded placeholder:text-gray-400" placeholder="0" />

                    <div class="flex absolute inset-y-0 right-0 items-center pr-8 pointer-events-none">
                        <span class="text-gray-500">@lang('modules.menu.minutes')</span>
                    </div>

                </div>
                <x-input-error for="preparationTime" class="mt-2" />
            </div>

            <div>
                <x-label for="isAvailable" :value="__('modules.menu.isAvailable')" />
                <x-select id="isAvailable" class="block mt-1 w-full" wire:model="isAvailable">
                    <option value="1">@lang('app.yes')</option>
                    <option value="0">@lang('app.no')</option>
                </x-select>
                <x-input-error for="isAvailable" class="mt-2" />
            </div>

            @if ((module_enabled('Inventory') && in_array('Inventory', restaurant_modules())) )
            <div>
                <x-label for="inStock" :value="__('modules.menu.inStock')" />
                <x-select id="inStock" class="block mt-1 w-full" wire:model="inStock">
                    <option value="1">@lang('app.yes')</option>
                    <option value="0">@lang('app.no')</option>
                </x-select>
                <x-input-error for="inStock" class="mt-2" />
            </div>
            @endif


            @if (in_array('Kitchen', restaurant_modules()))
            <div>
                <x-label for="kitchenType" :value="__('modules.menu.kitchenType')" />
                <x-select id="kitchenType" class="block mt-1 w-full" wire:model="kitchenType">
                    <option value="">@lang('modules.menu.SelectKitchenType')</option>
                    @foreach($kitchenTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="kitchenType" class="mt-2" />
            </div>
            @endif

            <div>
                <x-label for="showOnCustomerSite" :value="__('modules.menu.showOnCustomerSite')" />
                <x-select id="showOnCustomerSite" class="block mt-1 w-full" wire:model="showOnCustomerSite">
                    <option value="1">@lang('app.yes')</option>
                    <option value="0">@lang('app.no')</option>
                </x-select>
                <x-input-error for="showOnCustomerSite" class="mt-2" />
            </div>

            <div>
                <x-label for="itemImage" value="{{ __('modules.menu.itemImage') }}"/>

                <input
                    class="block my-1 w-full text-sm bg-gray-50 rounded-lg border border-gray-300 cursor-pointer focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 text-slate-500"
                    type="file" wire:model="itemImageTemp" accept="image/*">

                <x-input-error for="itemImageTemp" class="mt-2"/>

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Supported formats: JPEG, PNG, JPG, GIF, SVG. Maximum size: 2MB
                </p>

                <!-- Current Image Display -->
                @if ($menuItem->image && !$itemImageTemp)
                    <div class="mt-2">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Current image:</p>
                        <img class="object-cover w-20 h-20 rounded-md" src="{{ $menuItem->item_photo_url }}"
                             alt="{{ $menuItem->item_name }}">
                    </div>
                @endif

                <!-- New Image Preview -->
                @if($itemImageTemp)
                    <div class="mt-2">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">New image preview:</p>
                        <div class="relative inline-block">
                            <img src="{{ $itemImageTemp->temporaryUrl() }}" alt="Preview" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                            <button type="button" wire:click="removeSelectedImage" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                            <p class="font-medium">{{ $itemImageTemp->getClientOriginalName() }}</p>
                            <p class="text-gray-500">{{ $this->formatFileSize($itemImageTemp->getSize()) }}</p>
                            @php
                                $imageInfo = getimagesize($itemImageTemp->getRealPath());
                                if ($imageInfo) {
                                    echo '<p class="text-gray-500">' . $imageInfo[0] . ' × ' . $imageInfo[1] . ' pixels</p>';
                                }
                            @endphp
                        </div>
                    </div>
                @endif
            </div>

            @if (restaurant()->tax_mode === 'item')
            <div class="mb-4" wire:key="tax-selection-section">
                <x-label for="selectedTaxes" class="mb-1" :value="__('modules.menu.selectTaxes')" />
                <div x-data="{
                    isOpen: false,
                    selectedTaxes: @entangle('selectedTaxes').live,
                }" @click.away="isOpen = false">
                    <div class="relative">
                        <div @click="isOpen = !isOpen"
                            class="w-full flex items-center justify-between bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg p-2.5 cursor-pointer">
                            <div class="flex flex-wrap gap-1">
                                @if(empty($selectedTaxes))
                                    <span class="text-gray-500 dark:text-gray-400">@lang('modules.menu.selectTaxes')</span>
                                @else
                                    @foreach(collect($taxes)->whereIn('id', $selectedTaxes) as $tax)
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-600 rounded-md text-sm mr-1 flex items-center" wire:key="tax-badge-{{ $tax->id }}">
                                            {{ $tax->tax_name }} ({{ $tax->tax_percent }}%)
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        <!-- Dropdown menu -->
                        <div x-show="isOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-700 shadow-lg rounded-md max-h-60 overflow-auto">
                            <div class="p-2 border-b border-gray-200 dark:border-gray-600">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">@lang('modules.menu.selectTaxes')</span>
                            </div>
                            <ul class="py-1">
                                @foreach($taxes as $tax)
                                <li class="px-2" wire:key="tax-option-{{ $tax->id }}">
                                    <label class="flex items-center p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-md cursor-pointer">
                                        <input type="checkbox" value="{{ $tax->id }}"
                                            x-model="selectedTaxes"
                                            @click="$wire.set('selectedTaxes', selectedTaxes);"
                                            class="rounded border-gray-300 text-skin-base shadow-sm focus:border-skin-base focus:ring focus:ring-skin-base focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $tax->tax_name }} ({{ $tax->tax_percent }}%)
                                        </span>
                                    </label>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <x-input-error for="selectedTaxes" class="mt-2" />
            </div>
            @endif

            <div>
                <x-label for="hasVariations">
                    <div class="flex items-center cursor-pointer">
                        <x-checkbox name="hasVariations" id="hasVariations" wire:model.live='hasVariations' />

                        <div class="ms-2">
                            @lang('modules.menu.hasVariations')
                        </div>
                    </div>
                </x-label>
            </div>

            @if ($showItemPrice)
                <div wire:transition  wire:key="item-price">
                    <x-label for="itemPrice" :value="__('modules.menu.setPrice')"/>
                    <div class="relative mt-1 rounded-md">
                        <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                            <span class="text-gray-500">{{ restaurant()->currency->currency_symbol }}</span>
                        </div>
                        <x-input id="itemPrice" type="number" step="0.001" wire:model.live="itemPrice"
                                class="block pl-10 w-full text-gray-900 rounded placeholder:text-gray-400"
                                placeholder="0.00"/>
                    </div>
                    <x-input-error for="itemPrice" class="mt-2"/>
                </div>
            @else
                <div wire:key="variation-item-section">
                    @foreach($inputs as $key => $value)
                    <div wire:key="variation-full-details-{{ $key }}">
                        <div class="grid grid-cols-2 gap-4 mb-4" wire:key='variation-item-number-{{ $value }}'>
                            <div>
                                <x-label for="variationName.{{ $key }}" :value="__('modules.menu.variationName')"/>

                                <x-input id="variationName.{{ $key }}" class="block mt-1 w-full" type="text"
                                        placeholder="{{ __('placeholders.itemVariationPlaceholder') }}" autofocus
                                        wire:model.change='variationName.{{ $key }}'/>

                                <x-input-error for="variationName.{{ $key }}" class="mt-2"/>
                            </div>
                            <div>
                                <x-label for="variationPrice.{{ $key }}" :value="__('modules.menu.setPrice')"/>
                                <div class="inline-flex relative items-center mt-1 rounded-md">
                                    <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                                        <span class="text-gray-500">{{ restaurant()->currency->currency_symbol }}</span>
                                    </div>
                                    <x-input id="variationPrice.{{ $key }}" type="number" step="0.001"
                                            wire:model.live="variationPrice.{{ $key }}"
                                            class="block pl-10 w-full text-gray-900 rounded placeholder:text-gray-400"
                                            placeholder="0.00"/>

                                    <x-danger-button class="ml-2" wire:click="removeField({{ $key }})"
                                                    wire:key='remove-variation-{{ $key }}'>&cross;
                                    </x-danger-button>
                                </div>
                                <x-input-error for="variationPrice.{{ $key }}" class="mt-2"/>
                            </div>
                        </div>
                        {{-- Variation tax breakdown for this variation --}}
                        @if(!empty($variationBreakdowns[$key]['breakdown']))
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2 mb-4" wire:key="variation-breakdown-{{ $key }}">
                            <div class="text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300">
                                @lang('modules.menu.taxBreakdown') — <span class="font-normal">{{ $variationName[$key] ?? __('modules.menu.variation') }}</span>
                            </div>
                            <div class="flex flex-col gap-1 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">@lang('modules.menu.basePrice'):</span>
                                    <span class="font-medium">{{ currency_format($variationBreakdowns[$key]['breakdown']['base_raw'] ?? 0, restaurant()->currency_id) }}</span>
                                </div>
                                @if(!empty($variationBreakdowns[$key]['breakdown']['tax_breakdown']))
                                    <div class="ml-2 my-1">
                                        @foreach($variationBreakdowns[$key]['breakdown']['tax_breakdown'] as $taxName => $amount)
                                            <div class="flex justify-between text-gray-500 dark:text-gray-400">
                                                <span>{{ $taxName }}</span>
                                                <span>{{ currency_format($amount, restaurant()->currency_id) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">@lang('modules.menu.tax') ({{ $variationBreakdowns[$key]['breakdown']['tax_percent'] ?? 0 }}%):</span>
                                    <span class="font-medium">{{ currency_format($variationBreakdowns[$key]['breakdown']['tax_raw'] ?? 0, restaurant()->currency_id) }}</span>
                                </div>
                                <div class="flex justify-between pt-1 border-t border-gray-200 dark:border-gray-600 mt-1">
                                    <span class="text-gray-700 dark:text-gray-300 font-semibold">@lang('modules.menu.total'):</span>
                                    <span class="font-semibold">{{ currency_format($variationBreakdowns[$key]['breakdown']['total_raw'] ?? 0, restaurant()->currency_id) }}</span>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                @if($variationBreakdowns[$key]['breakdown']['inclusive'] ?? false)
                                    <span>
                                        @lang('modules.menu.taxInclusiveInfo', [
                                            'percent' => $variationBreakdowns[$key]['breakdown']['tax_percent'],
                                            'tax' => currency_format($variationBreakdowns[$key]['breakdown']['tax_raw'] ?? 0, restaurant()->currency_id),
                                            'base' => currency_format($variationBreakdowns[$key]['breakdown']['base_raw'] ?? 0, restaurant()->currency_id)
                                        ])
                                    </span>
                                @else
                                    <span>
                                        @lang('modules.menu.taxExclusiveInfo', [
                                            'percent' => $variationBreakdowns[$key]['breakdown']['tax_percent'],
                                            'tax' => currency_format($variationBreakdowns[$key]['breakdown']['tax_raw'] ?? 0, restaurant()->currency_id),
                                            'base' => currency_format($variationBreakdowns[$key]['breakdown']['base_raw'] ?? 0, restaurant()->currency_id)
                                        ])
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                    <x-secondary-button wire:click="addMoreField({{ $i }})" wire:key='add-variation-{{ $i }}'>
                        @lang('modules.menu.addVariations')
                    </x-secondary-button>
                </div>
            @endif

            {{-- <!-- Tax Inclusive/Exclusive Radio Buttons -->
            <div class="flex gap-4 mb-4 mt-2">
                @foreach([
                    ['id' => 'tax-exclusive', 'value' => 0, 'label' => __('modules.menu.taxAddedToPrice')],
                    ['id' => 'tax-inclusive', 'value' => 1, 'label' => __('modules.menu.priceIncludesTax')]
                ] as $option)
                    <div class="flex items-center ps-4 border border-gray-200 rounded-sm dark:border-gray-700 w-full">
                        <input
                            id="{{ $option['id'] }}"
                            type="radio"
                            name="taxInclusive"
                            value="{{ $option['value'] }}"
                            wire:model.live="taxInclusive"
                            class="w-3 h-3 text-skin-base bg-gray-100 border-gray-300 focus:ring-skin-base dark:focus:ring-skin-base dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                        >
                        <label for="{{ $option['id'] }}" class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $option['label'] }}
                        </label>
                    </div>
                @endforeach
            </div> --}}

            <!-- For Normal Price breakdowns -->
            @if (!$hasVariations && $taxInclusivePrice)
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-2" wire:key="tax-details-section">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('modules.menu.taxBreakdown')</h4>

                @php
                    $currencySymbol = restaurant()->currency->currency_symbol;
                    $formatPrice = function($amount) use ($currencySymbol) {
                        return $currencySymbol . ' ' . number_format($amount ?? 0, 2);
                    };
                @endphp

                <div class="flex flex-col gap-1 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">@lang('modules.menu.basePrice'):</span>
                        <span class="font-medium">{{ $formatPrice($taxInclusivePrice['base_raw']) }}</span>
                    </div>

                    @if(!empty($taxInclusivePrice['tax_breakdown']))
                        <div class="ml-2 my-1">
                            @foreach($taxInclusivePrice['tax_breakdown'] as $taxName => $amount)
                            <div class="flex justify-between text-gray-500 dark:text-gray-400" wire:key="tax-breakdown-{{ $taxName }}">
                                <span>{{ $taxName }}</span>
                                <span>{{ $formatPrice($amount) }}</span>
                            </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">@lang('modules.menu.tax') ({{ $taxInclusivePrice['tax_percent'] }}%):</span>
                        <span class="font-medium">{{ $formatPrice($taxInclusivePrice['tax_raw']) }}</span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-gray-200 dark:border-gray-600 mt-1">
                        <span class="text-gray-700 dark:text-gray-300 font-semibold">@lang('modules.menu.total'):</span>
                        <span class="font-semibold">{{ $formatPrice($taxInclusivePrice['total_raw']) }}</span>
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    @if($taxInclusivePrice['inclusive'])
                        <span>@lang('modules.menu.taxInclusiveInfo', [
                            'percent' => $taxInclusivePrice['tax_percent'],
                            'tax' => $formatPrice($taxInclusivePrice['tax_raw']),
                            'base' => $formatPrice($taxInclusivePrice['base_raw'])
                        ])</span>
                    @else
                        <span>@lang('modules.menu.taxExclusiveInfo', [
                            'percent' => $taxInclusivePrice['tax_percent'],
                            'tax' => $formatPrice($taxInclusivePrice['tax_raw']),
                            'base' => $formatPrice($taxInclusivePrice['base_raw'])
                        ])</span>
                    @endif
                </div>
            </div>
            @endif

        </div>

        <div class="flex pb-4 mt-6 space-x-4 w-full rtl:space-x-reverse">
            <x-button wire:loading.attr="disabled" wire:target="submitForm">
                <span wire:loading.remove wire:target="submitForm">@lang('app.save')</span>
                <span wire:loading wire:target="submitForm" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                </span>
            </x-button>
            <x-button-cancel wire:click="$dispatch('hideEditMenuItem')" wire:loading.attr="disabled" wire:target="submitForm">@lang('app.cancel')</x-button-cancel>
        </div>

    </form>
</div>
