<div>
    <form wire:submit="editFrontFeature">
        <div class="space-y-4">

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
                            // Load existing content into the editor
                            $nextTick(() => {
                                if ($refs.trixEditor && $refs.featureDescription.value) {
                                    $refs.trixEditor.editor.loadHTML($refs.featureDescription.value);
                                }
                            });

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

                            window.addEventListener('reset-trix-editor', () => {
                                $refs.trixEditor.editor.loadHTML('');
                            });" >
                    </trix-editor>
                </div>
                <x-input-error for="featureDescription" class="mt-2" />
            </div>


            <!-- Feature Image -->
            <div class="p-5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                            @lang('modules.settings.featureImage')
                        </h4>

                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    <div class="p-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow transition-shadow duration-300 border border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col items-center space-y-2">
                            <div id="featureImagePreview"
                                class="h-32 w-32 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-center overflow-hidden relative"
                                style="background-image: url('{{ $featureImage ? $featureImage->temporaryUrl() : ($existingImageUrl ?? asset('images/default-feature.png')) }}'); background-size: contain; background-position: center; background-repeat: no-repeat;">
                                {{-- Loading State --}}
                                <div wire:loading wire:target="featureImage"
                                    class="absolute inset-0 bg-gray-900/60 rounded-lg flex items-center justify-center">
                                    <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>
                            </div>

                            <div class="text-center w-full">
                                <input type="file" id="featureImage" class="hidden"
                                    accept="image/png, image/gif, image/jpeg, image/webp, image/svg+xml"
                                    wire:model="featureImage" x-ref="featureImage"
                                    x-on:change="
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            document.getElementById('featureImagePreview').style.backgroundImage = 'url(' + e.target.result + ')';
                                        };
                                        reader.readAsDataURL($refs.featureImage.files[0]);
                                    " />

                                <x-secondary-button type="button"
                                    x-on:click.prevent="$refs.featureImage.click()"
                                    class="w-full flex items-center justify-center text-xs py-1 px-1">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    {{ __('app.upload') }}
                                </x-secondary-button>

                                <x-input-error for="featureImage" class="mt-1 text-xs" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel wire:click="$dispatch('hideEditFeature')" wire:loading.attr="disabled">
                @lang('app.cancel')
            </x-button-cancel>
        </div>
    </form>
</div>
