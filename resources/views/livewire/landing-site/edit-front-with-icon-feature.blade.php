<div>
    <form wire:submit ="editFrontFeature">
        <div class="space-y-4">
            <!-- Language Selection -->
            <div class="mb-4">
                <label for="languageSettingid" class="block text-sm font-medium text-gray-700">
                    @lang('modules.settings.language')
                </label>
                <select id="languageSettingid"
                        wire:model.live="languageSettingid"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">@lang('app.selectLanguage')</option>
                    @foreach($languageEnable as $label)
                        <option value="{{ $label->id }}">{{ $label->language_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="featureTitle" class="block text-sm font-medium text-gray-700">
                    @lang('modules.settings.featureTitle')
                </label>
                <input type="text"
                       id="featureTitle"
                       wire:model="featureTitle"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <x-input-error for="featureTitle" class="mt-2" />
            </div>

            <div class="sm:col-span-2">
                <label for="featureDescription" class="block text-sm font-medium text-gray-700">
                    @lang('modules.settings.featureDescription')
                </label>
                <input x-ref="featureDescription" id="featureDescription" name="featureDescription" wire:model.lazy="featureDescription"
                    value="{{ $featureDescription }}" type="hidden" />

                <div wire:ignore class="mt-1">
                    <trix-editor class="trix-content text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        input="featureDescription"
                        data-gramm="false"
                        placeholder="{{ __('placeholders.featureDescriptionPlaceHolder') }}"
                        x-ref="trixEditor"
                        x-init="
                            // Load initial content with a small delay to ensure Livewire has set the value
                            setTimeout(() => {
                                if ($refs.trixEditor && $refs.featureDescription.value) {
                                    $refs.trixEditor.editor.loadHTML($refs.featureDescription.value);
                                }
                            }, 100);

                            // Prevent Livewire from interfering with Trix
                            $el.addEventListener('trix-change', function(event) {
                                $wire.set('featureDescription', event.target.value, false);
                            });

                            // Listen for Livewire updates
                            $wire.on('description-updated', (value) => {
                                if ($refs.trixEditor) {
                                    $refs.trixEditor.editor.loadHTML(value);
                                }
                            });

                            // Reset functionality
                            window.addEventListener('reset-trix-editor', () => {
                                $refs.trixEditor.editor.loadHTML('');
                            });" >
                    </trix-editor>
                </div>
                <x-input-error for="featureDescription" class="mt-2" />
            </div>

            <!-- Feature Icon -->
            <div class="sm:col-span-2">
                <label for="featureIcon" class="block text-sm font-medium text-gray-700 mb-2">
                    @lang('modules.settings.featureIcon')
                </label>
                <div class="relative" x-data="{ open: false }">
                    <div
                        class="border rounded w-full px-3 py-2 flex items-center cursor-pointer bg-white"
                        @click="open = !open"
                    >
                        @if ($selectedIcon && View::exists('components.heroicon-o-' . $selectedIcon))
                            <x-dynamic-component :component="'heroicon-o-' . $selectedIcon" class="w-5 h-5 text-gray-600 mr-2" />
                            <span class="text-gray-700">{{ $selectedIcon }}</span>
                        @else
                            {{-- <span class="text-gray-400">@lang('app.selectIcon')</span> --}}
                        @endif
                        <svg class="w-4 h-4 text-gray-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    <div
                        x-show="open"
                        @click.outside="open = false"
                        class="absolute z-50 mt-1 bg-white border border-gray-300 rounded shadow-lg w-full max-h-60 overflow-y-auto"
                        x-cloak
                    >
                        <div class="p-2">
                            <input
                                type="text"
                                wire:model.debounce.300ms="search"
                                placeholder="@lang('app.searchIcon')"
                                class="w-full px-2 py-1 border border-gray-300 rounded mb-2"
                            >
                        </div>
                        <div class="grid grid-cols-6 gap-2 p-2">
                            @foreach ($icons as $icon)
                                @if ($search === '' || Str::contains($icon, strtolower($search)))
                                    <button
                                        wire:click.prevent="selectIcon('{{ $icon }}')"
                                        class="p-2 border rounded hover:bg-gray-100 flex items-center justify-center {{ $selectedIcon === $icon ? 'border-blue-500 bg-blue-100' : '' }}"
                                        title="{{ $icon }}"
                                        type="button"
                                    >
                                        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-5 h-5" />
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <x-input-error for="selectedIcon" class="mt-2" />
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel wire:click="$dispatch('closeModal')" wire:loading.attr="disabled">
                @lang('app.cancel')
            </x-button-cancel>
        </div>
    </form>
</div>
