<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="menuItem" class="block text-sm font-medium mb-2 dark:text-white">{{ __('modules.modifier.menuItemName') }}</label>
                <div x-data="{ open: false, search: '', items: [], selectedItem: '', init() {
                    this.items = [...document.querySelectorAll('#addMenuItemDropdown option')].map(el => ({ value: el.value, text: el.textContent })).filter(i => i.value);
                    this.selectedItem = this.items.find(i => i.value === $wire.menuItemId)?.text || '';
                } }"
                    class="relative">
                    <button @click="open = !open" type="button"
                            class="relative py-3 px-4 w-full flex items-center justify-between text-gray-800 dark:text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm text-sm">
                        <span x-text="selectedItem || '{{ __('modules.modifier.selectMenuItem') }}'"></span>
                        <span class="text-gray-600 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </span>
                    </button>
                    <div x-show="open" x-transition class="absolute z-50 mt-2 w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg">
                        <div class="p-2">
                            <input type="text" x-model="search" @click.stop placeholder="{{ __('placeholders.searchMenuItems') }}" class="w-full p-2 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded text-sm focus:ring-2 focus:ring-primary-500">
                        </div>
                        <ul class="max-h-60 overflow-y-auto p-2">
                            <template x-for="item in items.filter(i => i.text.toLowerCase().includes(search.toLowerCase()))" :key="item.value">
                                <li class="cursor-pointer py-2 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 dark:text-gray-200 rounded-md"
                                    @click="selectedItem = item.text; $wire.menuItemId = item.value; $wire.set('menuItemId', item.value); open = false">
                                    <span x-text="item.text"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <select id="addMenuItemDropdown" class="hidden" wire:model.live="menuItemId">
                        <option value="">{{ __('modules.modifier.selectMenuItem') }}</option>
                        @foreach ($menuItems as $item)
                        <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                        @endforeach
                    </select>
                    <div class="text-xs text-primary-500 mt-1" wire:loading wire:target="menuItemId">
                        {{ __('Loading variations...') }}
                    </div>
                </div>
                <x-input-error for="menuItemId" class="mt-2" />
            </div>

            @if(count($variations) > 0)
            <div class="mt-4">
                <x-label for="variationId" :value="__('Variation (Optional)')" />
                <x-select id="variationId" class="mt-1 block w-full" wire:model="variationId">
                    <option value="">None (Apply to base item)</option>
                    @foreach ($variations as $variation)
                    <option value="{{ $variation->id }}">{{ $variation->variation }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="variationId" class="mt-2" />
                <p class="text-xs text-gray-500 mt-1 italic">{{ __('modules.modifier.variationDescription') }}</p>
            </div>
            @endif

            <div class="mt-5">
                <label for="modifierGroupId" class="block text-sm font-medium mb-2 dark:text-white">{{ __('modules.modifier.modifierGroup') }}</label>
                <div x-data="{ open: false, search: '', items: [], selectedItem: '', init() {
                    this.items = [...document.querySelectorAll('#addModifierGroupDropdown option')].map(el => ({ value: el.value, text: el.textContent })).filter(i => i.value);
                    this.selectedItem = this.items.find(i => i.value === $wire.modifierGroupId)?.text || '';
                } }"
                    class="relative">
                    <div class="flex">
                        <button @click="open = !open" type="button"
                                class="relative py-3 px-4 w-full flex items-center justify-between text-gray-800 dark:text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm text-sm">
                            <span x-text="selectedItem || '{{ __('modules.modifier.selectModifierGroup') }}'"></span>
                            <span class="text-gray-600 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                </svg>
                            </span>
                        </button>

                        <!-- Uncomment if you need the gear button in the future -->
                        {{-- <button class="p-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 border-l-0 rounded-r-lg shadow-sm"
                            wire:click="$toggle('showAddModifierGroupModal')" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-gear-fill" viewBox="0 0 16 16">
                                <path
                                    d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z" />
                            </svg>
                        </button> --}}
                    </div>
                    <div x-show="open" x-transition class="absolute z-50 mt-2 w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg">
                        <div class="p-2">
                            <input type="text" x-model="search" @click.stop placeholder="{{ __('placeholders.searchModifierGroups') }}"
                                class="w-full p-2 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <ul class="max-h-60 overflow-y-auto p-2">
                            <template x-for="item in items.filter(i => i.text.toLowerCase().includes(search.toLowerCase()))" :key="item.value">
                                <li class="cursor-pointer py-2 px-3 hover:bg-gray-100 dark:hover:bg-gray-800 dark:text-gray-200 rounded-md"
                                    @click="selectedItem = item.text; $wire.modifierGroupId = item.value; $wire.set('modifierGroupId', item.value); open = false">
                                    <span x-text="item.text"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <select id="addModifierGroupDropdown" class="hidden" wire:model.live="modifierGroupId">
                        <option value="">{{ __('modules.modifier.selectModifierGroup') }}</option>
                        @foreach ($modifierGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-input-error for="modifierGroupId" class="mt-2" />
            </div>


            <div class="mt-4 grid gap-4">
                <label for="allowMultipleSelection" class="flex items-center gap-3 cursor-pointer bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 transition-colors">
                    <x-checkbox id="allowMultipleSelection" wire:model="allowMultipleSelection" class="accent-primary-500 h-5 w-5" />
                    <div>
                        <span class="font-semibold text-gray-900 dark:text-white">@lang('modules.modifier.allowMultipleSelection')</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{ __('modules.modifier.allowMultipleSelectionDescription') }}</span>
                    </div>
                </label>

                <label for="isRequired" class="flex items-center gap-3 cursor-pointer bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 transition-colors">
                    <x-checkbox name="isRequired" id="isRequired" wire:model="isRequired" class="accent-primary-500 h-5 w-5" />
                    <span class="font-semibold text-gray-900 dark:text-white select-none">@lang('modules.modifier.isRequired')</span>
                </label>
            </div>

            <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
                <x-button>@lang('app.save')</x-button>
                <x-button-cancel wire:click="$dispatch('hideAddItemModifierModal')">@lang('app.cancel')</x-button-cancel>
            </div>
        </div>
    </form>

    {{-- <x-right-modal wire:model.live="showAddModifierGroupModal">
        <x-slot name="title">
            {{ __('modules.modifier.addModifierGroup') }}
        </x-slot>

        <x-slot name="content">
            @if ($showAddModifierGroupModal)
            @livewire('forms.AddModifierGroup')
            @endif
        </x-slot>
    </x-right-modal> --}}
</div>
