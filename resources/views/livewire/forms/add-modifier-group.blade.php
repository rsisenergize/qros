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
        <div class="space-y-4">
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

            <!-- Modifier Options Section -->
            <div class="col-span-2">
                <x-label :value="__('modules.modifier.modifierOptions')" />
                <div class="space-y-4 mt-1">
                    @foreach($modifierOptions as $index => $modifierOption)
                    <div wire:key="modifierOption-{{ $modifierOption['id'] }}" class="border p-3 flex items-baseline gap-x-4 justify-baseline rounded-lg dark:border-gray-600">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                            <div>
                                <!-- Modifier Option Name with Translation -->
                                <x-label for="modifierOptions.{{ $index }}.name" :value="__('modules.modifier.name') . ' (' . $languages[$currentLanguage] . ')'" />
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
                                <x-label for="modifierOptions.{{ $index }}.price" :value="__('modules.modifier.price')" />
                                <x-input id="modifierOptions.{{ $index }}.price" type="number" step="0.001" class="mt-1 block w-full"
                                    wire:model="modifierOptions.{{ $index }}.price" placeholder="{{ __('placeholders.modifierOptionPricePlaceholder') }}" />
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

                        <div class="text-right">
                            <button type="button" class="bg-red-200 text-red-500 hover:bg-red-300 p-2 rounded" wire:click="removeModifierOption({{ $index }})">
                                <svg class="w-4 h-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L17.94 6M18 18L6.06 6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <x-secondary-button type="button" wire:click="addModifierOption">{{ __('modules.modifier.addModifierOption') }}</x-secondary-button>
                </div>
            </div>
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

                <!-- We've moved the variation selection inside the dropdown menu above -->
            </div>

            <div class="col-span-2 flex justify-baseline space-x-4 mt-6">
                <x-button type="submit" class="bg-green-500 hover:bg-green-700">{{ __('Save') }}</x-button>
                <x-button-cancel type="button" wire:click="$dispatch('hideAddModifierGroupModal')">{{ __('Cancel') }}</x-button-cancel>
            </div>
        </div>
    </form>
</div>
