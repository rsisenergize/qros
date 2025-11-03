<div>
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
    <form wire:submit="submitForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column - Product Information -->
            <div class="space-y-4">
                <div class="bg-white space-y-4 dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">

                    @if(count($languages) > 1)
                    <div class="mb-6 sticky top-0 z-30 pt-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-3">
                                <x-label for="language" :value="__('modules.menu.selectLanguage')" class="font-medium text-gray-700 dark:text-gray-300 text-base" />
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($languages as $code => $name)
                                    <button
                                        type="button" wire:click="$set('currentLanguage', '{{ $code }}')"
                                        @if($currentLanguage === $code) aria-current="true" @endif
                                        @class([
                                            'px-3 py-1.5 text-xs rounded-md border transition-all duration-200 focus:ring-2 focus:ring-offset-2 focus:outline-none flex items-center gap-2',
                                            'bg-skin-base text-white border-skin-base shadow-sm font-medium focus:ring-skin-base' => $currentLanguage === $code,
                                            'bg-white text-gray-700 border-gray-200 hover:bg-gray-50 hover:text-skin-base hover:border-skin-base/20 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:border-skin-base/50 dark:hover:text-skin-base focus:ring-skin-base/30' => $currentLanguage !== $code
                                        ])
                                    >
                                        <img src="{{ $languageSettings->get($code)['flag_url'] ?? asset('flags/1x1/' . strtolower($code) . '.svg') }}" alt="{{ $code }}" class="w-4 h-4 rounded-sm object-cover" />
                                        <span>{{ $languageSettings->get($code)['name'] ?? $name }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Name and Description with Translation -->
                    <div class="mb-4">
                        <x-label for="name" :value="__('modules.modifier.modifierName') . ' (' . $languages[$currentLanguage] . ')'" />
                        <x-input id="name" class="block mt-1 w-full" type="text" placeholder="{{ __('placeholders.modifierGroupNamePlaceholder') }}" wire:model="name" wire:change="updateTranslation" />
                        <x-input-error for="translationNames.{{ $globalLocale }}" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="description" :value="__('modules.modifier.description') . ' (' . $languages[$currentLanguage] . ')'" />
                        <x-textarea class="block mt-1 w-full" :placeholder="__('placeholders.modifierGroupDescriptionPlaceholder')"
                            wire:model='description' rows='2' wire:change="updateTranslation" data-gramm="false"/>
                        <x-input-error for="description" class="mt-2" />
                    </div>

                    <!--  Translation Preview Section - Only show if we have translations and multiple languages -->
                    @if(count($languages) > 1 && (array_filter($translationNames) || array_filter($translationDescriptions)))
                    <div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2.5">
                            <x-label :value="__('modules.menu.translations')" class="text-sm mb-2 last:mb-0" />
                            <div class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach($languages as $lang => $langName)
                                    @if(!empty($translationNames[$lang]) || !empty($translationDescriptions[$lang]))
                                    <div class="flex flex-col gap-1.5 py-2" wire:key="translation-details-{{ $loop->index }}">
                                        <div class="flex items-center gap-3">
                                            <span class="min-w-[80px] text-xs font-medium text-gray-600 dark:text-gray-300">
                                                {{ $languageSettings->get($lang)['name'] ?? strtoupper($lang) }}
                                            </span>
                                            <div class="flex-1">
                                                @if(!empty($translationNames[$lang]))
                                                <div class="mb-1">
                                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('app.name'):</span>
                                                    <span class="text-xs text-gray-700 dark:text-gray-200 ml-1">{{ $translationNames[$lang] }}</span>
                                                </div>
                                                @endif
                                                @if(!empty($translationDescriptions[$lang]))
                                                <div>
                                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('app.description'):</span>
                                                    <span class="text-xs text-gray-700 dark:text-gray-200 ml-1">{{ $translationDescriptions[$lang] }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Menu Items and Variations Section -->
                    <div class="col-span-2">
                        <div class="flex items-center justify-between mb-1">
                            <x-label :value="__('modules.modifier.locations')" />
                            <span class="text-xs text-gray-500">
                                {{ count($selectedMenuItems) }} {{ __('selected') }}
                            </span>
                        </div>

                        <div x-data="{ isOpen: @entangle('isOpen').live }" @click.away="isOpen = false" x-cloak>
                            <div class="relative">
                                <!-- Selected Items Display -->
                                <div @click="isOpen = !isOpen"
                                    class="p-2 bg-gray-50 border rounded cursor-pointer dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                                    <div class="flex items-center justify-between">
                                        <div class="flex flex-wrap gap-1.5 flex-grow">
                                            @forelse ($allMenuItems->whereIn('id', $selectedMenuItems)->take(5) as $item)
                                                <span class="px-2 py-0.5 text-xs font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 flex items-center" wire:key="selected-item-{{ $item->id }}" @click.stop>
                                                    {{ $item->item_name }}
                                                    @if(isset($selectedVariations[$item->id]) && !empty(array_filter($selectedVariations[$item->id])))
                                                        @php
                                                            $selectedVarNames = [];
                                                            $hasVariations = false;

                                                            // Check which variations are selected
                                                            foreach ($selectedVariations[$item->id] as $varId => $isSelected) {
                                                                if ($isSelected && $varId !== 'item') {
                                                                    $variation = $item->variations->firstWhere('id', $varId);
                                                                    if ($variation) {
                                                                        $selectedVarNames[] = $variation->variation;
                                                                        $hasVariations = true;
                                                                    }
                                                                }
                                                            }

                                                            // Only add base item if no other variations are selected
                                                            if (!$hasVariations && isset($selectedVariations[$item->id]['item']) && $selectedVariations[$item->id]['item']) {
                                                                $selectedVarNames[] = 'Base Item';
                                                            }

                                                            $selectedCount = count($selectedVarNames);
                                                            $totalCount = $item->variations->count() + 1; // +1 for base item
                                                        @endphp

                                                        @if(!empty($selectedVarNames))
                                                            <span class="ml-2 px-2 py-0.5 rounded bg-skin-base text-white text-xs">
                                                                @if(isset($selectedVariations[$item->id]['item']) && $selectedVariations[$item->id]['item'])
                                                                    {{ __('All Variations') }}
                                                                @elseif($selectedCount <= 2)
                                                                    {{ implode(', ', $selectedVarNames) }}
                                                                @else
                                                                    {{ $selectedCount }} variations
                                                                @endif
                                                            </span>
                                                        @endif
                                                    @endif
                                                    <button type="button" wire:click="toggleSelectItem({ id: {{ $item->id }}, item_name: '{{ addslashes($item->item_name) }}' })" class="inline-flex items-center p-1 ms-2 text-sm text-red-500 bg-transparent rounded-xs hover:bg-red-200 hover:text-red-900 dark:hover:bg-red-800 dark:hover:text-red-300">
                                                        <svg class="w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 5 5m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                                                        <span class="sr-only">Remove badge</span>
                                                    </button>
                                                </span>
                                            @empty
                                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('modules.modifier.selectMenuItem') }}</span>
                                            @endforelse

                                            @if($allMenuItems->whereIn('id', $selectedMenuItems)->count() > 5)
                                                <span class="px-2 py-0.5 text-xs text-gray-500 bg-gray-100 border border-gray-200 rounded dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                                    +{{ $allMenuItems->whereIn('id', $selectedMenuItems)->count() - 5 }} more
                                                </span>
                                            @endif
                                        </div>

                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 ml-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>


                                <!-- Search and Selection Dropdown -->
                                <div x-show="isOpen" x-transition class="absolute z-20 w-full mt-1 overflow-hidden bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-900 dark:border-gray-700">
                                    <div class="sticky top-0 px-3 py-2 bg-white dark:bg-gray-900 z-10 border-b dark:border-gray-700">
                                        <x-input
                                            wire:model.live.debounce.300ms="search"
                                            class="block w-full"
                                            type="text"
                                            placeholder="{{ __('placeholders.searchMenuItem') }}"
                                        />
                                    </div>

                                    <div class="overflow-y-auto max-h-60">
                                        @forelse ($this->menuItems as $item)
                                            <div wire:key="menu-item-{{ $item->id }}"
                                                class="border-b dark:border-gray-700 last:border-b-0"
                                                x-data="{ expanded: false }">
                                                <!-- Menu Item Header -->
                                                <div class="flex items-center justify-between py-2 px-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800"
                                                    :class="{ 'bg-gray-50 dark:bg-gray-800': $wire.selectedMenuItems.includes({{ $item->id }}) }"
                                                    @click="$wire.toggleSelectItem({ id: {{ $item->id }}, item_name: '{{ addslashes($item->item_name) }}' })"
                                                    wire:key="menu-item-header-{{ $item->id }}">

                                                    <div class="flex items-center gap-2">
                                                        <!-- Simple checkbox -->
                                                        <div class="flex-shrink-0">
                                                            <div class="w-4 h-4 border rounded transition-colors duration-150"
                                                                :class="$wire.selectedMenuItems.includes({{ $item->id }}) ?
                                                                        'border-green-500 bg-green-500' :
                                                                        'border-gray-300 dark:border-gray-600'">
                                                                @if(in_array($item->id, $selectedMenuItems))
                                                                <svg class="w-4 h-4 text-white" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M4 8l2 2 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Item name -->
                                                        <span class="text-gray-700 dark:text-gray-300">{{ $item->item_name }}</span>

                                                        <!-- Variations badge - simplified -->
                                                        @if($item->variations->count() > 0)
                                                        <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">
                                                            ({{ $item->variations->count() }})
                                                        </span>
                                                        @endif
                                                    </div>

                                                    <!-- Simple variation toggle with selection display -->
                                                    @if($item->variations->count() > 0)
                                                    <button type="button"
                                                            @click.stop="expanded = !expanded"
                                                            wire:click.stop
                                                            class="flex items-center gap-1 text-xs px-1.5 py-0.5 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                                                        <div class="flex items-center">
                                                            @php
                                                                $selectedCount = isset($selectedVariations[$item->id])
                                                                    ? collect($selectedVariations[$item->id])->filter()->count()
                                                                    : 0;

                                                                $selectedVarNames = [];
                                                                $hasVariations = false;
                                                                $hasBaseItem = false;

                                                                if(isset($selectedVariations[$item->id])) {
                                                                    // Check for base item selection
                                                                    if(isset($selectedVariations[$item->id]['item']) && $selectedVariations[$item->id]['item']) {
                                                                        $hasBaseItem = true;
                                                                        $selectedVarNames[] = 'Base Item';
                                                                    }

                                                                    // Check for specific variations
                                                                    foreach($selectedVariations[$item->id] as $varId => $isSelected) {
                                                                        if($isSelected && $varId !== 'item') {
                                                                            $variation = $item->variations->firstWhere('id', $varId);
                                                                            if($variation) {
                                                                                $selectedVarNames[] = $variation->variation;
                                                                                $hasVariations = true;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            @endphp

                                                            @if($selectedCount > 0)
                                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                    @if($hasBaseItem)
                                                                        {{ __('All Variations') }}
                                                                    @elseif(count($selectedVarNames) <= 2)
                                                                        {{ implode(', ', $selectedVarNames) }}
                                                                    @else
                                                                        {{ count($selectedVarNames) }} {{ __('variations') }}
                                                                    @endif
                                                                </span>
                                                            @else
                                                                <span>{{ __('Select Variations') }}</span>
                                                            @endif
                                                        </div>
                                                        <svg class="w-3.5 h-3.5 transition-transform" :class="{'rotate-180': expanded}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                    @endif
                                                </div>
                                                <!-- Variation dropdown (minimal) -->
                                                @if($item->variations->count() > 0)
                                                <div
                                                    x-show="expanded"
                                                    x-collapse
                                                    class="px-3 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800"
                                                    wire:key="menu-item-variations-{{ $item->id }}"
                                                >
                                                    <x-label :value="__('modules.modifier.selectVariation')" class="text-xs mb-1" />

                                                    <div class="space-y-2">
                                                        
                                                        @foreach($item->variations as $variation)
                                                            <div class="flex items-center" wire:key="variation-{{ $variation->id }}">
                                                                <input
                                                                    id="variation-{{ $variation->id }}"
                                                                    type="checkbox"
                                                                    wire:model.live="selectedVariations.{{ $item->id }}.{{ $variation->id }}"
                                                                    class="w-4 h-4 rounded border-gray-300 focus:ring-skin-base text-skin-base"
                                                                    @click.stop
                                                                >
                                                                <label for="variation-{{ $variation->id }}" class="ml-2 text-xs text-gray-700 dark:text-gray-300">
                                                                    {{ $variation->variation }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="py-3 px-3 text-gray-500 dark:text-gray-400 text-center text-sm">
                                                {{ __('modules.modifier.noMenuItemsFound') }}
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <x-input-error for="selectedMenuItems" class="mt-2" />
                            <p class="text-xs text-gray-500 mt-1">
                                {{ __('modules.modifier.selectMenuItemsHelpText') }}
                            </p>
                        </div>

                    </div>
                    <!-- Action Buttons -->
                    <div class="flex w-full space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-button type="submit" wire:loading.attr="disabled" wire:target="submitForm" class="flex-1">
                            <span wire:loading.remove wire:target="submitForm">@lang('app.save')</span>
                            <span wire:loading wire:target="submitForm" class="flex items-center">
                                Saving...
                            </span>
                        </x-button>
                        <x-secondary-link href="{{ route('menu-items.index') }}" wire:navigate wire:loading.attr="disabled" wire:target="submitForm" class="flex-1">@lang('app.cancel')
                        </x-secondary-link>
                        {{-- <x-button-cancel type="button" wire:click="$dispatch('hideAddModifierGroupModal')">{{ __('Cancel') }}</x-button-cancel> --}}
                    </div>
                </div>
            </div>

            <!-- Right Column - Pricing Details -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Pricing Configuration -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-500" width="24" height="24" viewBox="0 0 24 24" stroke="currentColor" fill="currentColor" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="M9.5 10.5H12a1 1 0 0 0 0-2h-1V8a1 1 0 0 0-2 0v.55a2.5 2.5 0 0 0 .5 4.95h1a.5.5 0 0 1 0 1H8a1 1 0 0 0 0 2h1v.5a1 1 0 0 0 2 0v-.55a2.5 2.5 0 0 0-.5-4.95h-1a.5.5 0 0 1 0-1M21 12h-3V3a1 1 0 0 0-.5-.87 1 1 0 0 0-1 0l-3 1.72-3-1.72a1 1 0 0 0-1 0l-3 1.72-3-1.72a1 1 0 0 0-1 0A1 1 0 0 0 2 3v16a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-6a1 1 0 0 0-1-1M5 20a1 1 0 0 1-1-1V4.73l2 1.14a1.08 1.08 0 0 0 1 0l3-1.72 3 1.72a1.08 1.08 0 0 0 1 0l2-1.14V19a3 3 0 0 0 .18 1Zm15-1a1 1 0 0 1-2 0v-5h2Z"/></svg>
                        @lang('modules.modifier.modifierOptions')
                    </h3>
                    <!-- Modifier Options Section -->
                    <div class="col-span-2">
                        <div class="space-y-4 mt-4" x-data="{ openOption: null }">
                            @foreach($modifierOptions as $index => $modifierOption)
                            <div wire:key="modifierOption-{{ $modifierOption['id'] }}" 
                                class="bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4 transition-all duration-200"
                                :class="openOption === {{ $index }} ? 'ring-1 ring-skin-base shadow-md' : ''">
                                
                                <!-- Option Header - Clickable -->
                                <div class="cursor-pointer" @click="openOption = openOption === {{ $index }} ? null : {{ $index }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400 transition-transform duration-200"
                                                :class="openOption === {{ $index }} ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                            <div class="flex items-center gap-3">
                                                <div>
                                                    <h4 class="font-medium text-gray-900 dark:text-white">
                                                        {{ $modifierOptionName[$index] ?: 'Option ' . ($index + 1) }}
                                                    </h4>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        <span class="font-medium">@lang('modules.modifier.price'):</span> {{ restaurant()->currency->currency_symbol }}{{ $modifierOption['price'] ?? '0.00' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Remove Button -->
                                        <button type="button"
                                                wire:click.stop="removeModifierOption({{ $index }})"
                                                class="p-2 text-red-500 hover:text-red-700 hover:bg-red-100 dark:hover:bg-red-900/20 rounded-md transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Expanded Content -->
                                <div x-show="openOption === {{ $index }}"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 transform scale-100"
                                    x-transition:leave-end="opacity-0 transform scale-95"
                                    class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 space-y-4">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                                    <div>
                                        <!-- Modifier Option Name with Translation -->
                                        <x-label for="modifierOptions.{{ $index }}.name" :value="__('modules.modifier.optionName') . ' (' . $languages[$currentLanguage] . ')'" />
                                        <x-input id="modifierOptions.{{ $index }}.name"
                                            class="mt-1 block w-full"
                                            type="text"
                                            placeholder="{{ __('placeholders.modifierOptionNamePlaceholder') }}"
                                            wire:model="modifierOptionName.{{ $index }}"
                                            wire:change="updateModifierOptionTranslation({{ $index }})"
                                        />
                                        <x-input-error for="modifierOptions.{{ $index }}.name.{{ $globalLocale }}" class="mt-2" />

                                        <!-- Translation Preview for This Option -->
                                        @if(count($languages) > 1 && isset($modifierOptionInput[$index]) && array_filter($modifierOptionInput[$index]))
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2.5 mt-2">
                                            @foreach($modifierOptionInput[$index] as $lang => $text)
                                                @if(!empty($text))
                                                <div class="flex items-center gap-3 py-1" wire:key="translation-option-name-details-{{ $modifierOption['id'] }}-{{ $lang }}">
                                                    <span class="min-w-[80px] text-xs font-medium text-gray-600 dark:text-gray-300">
                                                        {{ $languageSettings->get($lang)['name'] ?? strtoupper($lang) }}:
                                                    </span>
                                                    <span class="flex-1 text-xs text-gray-700 dark:text-gray-200">{{ $text }}</span>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>

                    <div>
                        <x-label for="modifierOptions.{{ $index }}.price" :value="__('modules.modifier.defaultPrice')" />
                        <x-input id="modifierOptions.{{ $index }}.price" type="number" step="0.001" class="mt-1 block w-full"
                            wire:model.live.debounce.500ms="modifierOptions.{{ $index }}.price" placeholder="{{ __('placeholders.modifierOptionPricePlaceholder') }}" />
                        <x-input-error for="modifierOptions.{{ $index }}.price" class="mt-2" />
                    </div>

                    <x-label for="modifierOptions.{{ $index }}.is_available">
                        <div class="flex items-center cursor-pointer">
                            <x-checkbox id="modifierOptions.{{ $index }}.is_available" wire:model="modifierOptions.{{ $index }}.is_available" value="{{ $modifierOption['id'] }}" />
                            <div class="select-none ms-2">
                                {{ __('modules.modifier.isAvailable') }}
                            </div>
                        </div>
                    </x-label>
                </div>

                <!-- Order Types Pricing -->
                @if($orderTypes->isNotEmpty())
                <div class="col-span-2">
                    <x-label value="Order Types Pricing" class="mb-3 text-base font-semibold" />
                    <div class="space-y-2">
                        @foreach($orderTypes->reject(fn($type) => strtolower($type->slug ?? $type->name) === 'delivery') as $orderType)
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $orderType->order_type_name }}</span>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 text-sm">{{ restaurant()->currency->currency_symbol }}</span>
                                </div>
                                <x-input type="number" step="0.001"
                                        wire:model.live="optionOrderTypePrices.{{ $index }}.{{ $orderType->id }}"
                                        class="block pl-8 pr-3 w-32"
                                        placeholder="0.00" />
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Delivery Platforms -->
                @if($deliveryApps->isNotEmpty())
                <div class="col-span-2">
                    <x-label value="Delivery Platforms" class="mb-3 text-base font-semibold" />
                    <div class="space-y-2">
                        <!-- Base Delivery Price -->
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-200" fill="currentColor" height="20" viewBox="0 0 64 64" width="20" xmlns="http://www.w3.org/2000/svg"><path d="M4 16h14.001a3 3 0 0 1 3 3v11.001a3 3 0 0 1-3 3h-14a3 3 0 0 1-3.001-3v-11a3 3 0 0 1 3-3"/><circle cx="33.002" cy="7" r="5"/><path d="M12.003 35.852a5.92 5.92 0 0 0 1.7 4.15H29.96v-4.155a.996.996 0 0 0-.996-.996H12.998a1 1 0 0 0-.995 1.001"/><path d="M61.737 51.359a8.13 8.13 0 0 0-8.322-5.994 7 7 0 0 0 .24-1.791A5.93 5.93 0 0 0 51 38.75c-2.147-1.425-3.753-5.048-3.996-8.858h1.916a2.99 2.99 0 0 0 2.991-2.982v-1.986a2.99 2.99 0 0 0-2.991-2.982h-6.84c-5.782-1.665-7.522-3.583-8.561-4.732l-.063-.07a3.71 3.71 0 0 0-2.018-3.813 3.64 3.64 0 0 0-5.122 2.497l-2.869 13.71a2.983 2.983 0 0 0 2.598 3.571l4.917.544a.994.994 0 0 1 .887 1.043l-.774 13.106a5.27 5.27 0 0 1-1.477-5.796H14.313c-1.612 2.671-4.193 7.679-3.149 10.936a4.04 4.04 0 0 0 2.609 2.622 3.7 3.7 0 0 0 1.39.15 6.406 6.406 0 0 0 12.78 0h17.14a1.26 1.26 0 0 0 .875-.423 7 7 0 0 0 .587 1.703.996.996 0 0 0 1.716.14q.176-.25.376-.491a6.4 6.4 0 1 0 12.484-2.718.986.986 0 0 0 .875-1.075 8 8 0 0 0-.26-1.487m-40.184 8.318a4.407 4.407 0 0 1-4.385-3.967h8.77a4.407 4.407 0 0 1-4.385 3.967M40.94 48.754h-3.885l1.718-16.24a2.98 2.98 0 0 0-1.926-3.104l-4.9-1.829a.99.99 0 0 1-.622-1.149l.745-3.215a17.1 17.1 0 0 0 8.87 3.633zm14.586 11.218a4.413 4.413 0 0 1-4.961-4.86l.304-.38a11.08 11.08 0 0 1 7.676-1.51l.236.183a4.4 4.4 0 0 1-3.255 6.567"/></svg>
                                <span class="font-medium text-gray-900 dark:text-white text-sm">Base Delivery Price</span>
                            </div>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 text-sm">{{ restaurant()->currency->currency_symbol }}</span>
                                </div>
                                <x-input type="number" step="0.001"
                                        wire:model.live="optionBaseDeliveryPrice.{{ $index }}"
                                        class="block pl-8 pr-3 w-32"
                                        placeholder="0.00" />
                            </div>
                        </div>

                        <!-- Delivery Apps -->
                        @foreach($deliveryApps as $app)
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8">
                                    @if($app->logo)
                                    <img class="w-8 h-8 rounded-lg object-cover border border-gray-200"
                                        src="{{ $app->logo_url ?? asset('images/default-logo.png') }}"
                                        alt="{{ $app->name }}">
                                    @else
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white text-sm">{{ $app->name }}</span>
                                    <div class="text-xs text-gray-500">
                                        Commission: {{ $app->commission_value ?? 0 }}%
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                        wire:model.live="optionPlatformAvailability.{{ $index }}.{{ $app->id }}"
                                        class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                                <div class="text-right">
                                    <div class="font-semibold text-sm text-gray-900 dark:text-white">
                                        {{ restaurant()->currency->currency_symbol }}{{ $optionDeliveryPrices[$index][$app->id] ?? '0.00' }}
                                    </div>
                                    <div class="text-xs text-gray-500">Final Price</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                            </div>
                        </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <x-secondary-button type="button" wire:click="addModifierOption">{{ __('modules.modifier.addModifierOption') }}</x-secondary-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>