<div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 mt-4">

    <form wire:submit.prevent="saveHeader">
         <!-- Language Enable Radio Buttons -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        @lang('modules.settings.selectLanguage')
                    </h3>
                    <div class="flex flex-wrap gap-4">
                        @foreach($languageEnable as $value => $label)
                            <label class="relative flex items-center group cursor-pointer">
                                <input type="radio"
                                    wire:model.live="languageSettingid"
                                    value="{{ $label->id }}"
                                    class="peer sr-only"
                                    @if($loop->first && !$languageSettingid) checked @endif>
                                <span class="px-4 py-2 rounded-md text-sm border border-gray-200 dark:border-gray-700
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900
                                    peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400
                                    dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    {{ $label->language_name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

        <!-- Content Section -->
        <div class="space-y-3 p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 mt-4">
            <!-- Header Title -->
            <div class="sm:col-span-2">
                <label for="headerTitle" class="block text-sm font-medium text-gray-700">
                    @lang('modules.settings.headerTitle')
                </label>
                <input type="text"
                       id="headerTitle"
                       wire:model="headerTitle"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <x-input-error for="headerTitle" class="mt-2" />

            </div>

            <!-- Header Description -->
            <div class="sm:col-span-2">
                <label for="headerDescription" class="block text-sm font-medium text-gray-700">
                    @lang('modules.settings.headerDescription')
                </label>

                <input x-ref="headerDescription" id="headerDescription" name="headerDescription" wire:model="headerDescription"
                    value="{{ $headerDescription }}" type="hidden" />

                <div wire:ignore class="mt-2">
                    <trix-editor class="trix-content text-sm" input="headerDescription" data-gramm="false"
                        placeholder="{{ __('placeholders.headerDescriptionPlaceHolder') }}"
                        x-on:trix-change="$wire.set('headerDescription', $event.target.value)" x-ref="trixEditor"  x-init="
                            window.addEventListener('reset-trix-editor', () => {
                                $refs.trixEditor.editor.loadHTML('');
                            });" >
                    </trix-editor>
                </div>
                <x-input-error for="headerDescription" class="mt-2" />

            </div>
            <!-- Select Image -->
            <div class="sm:col-span-2">
                <label for="headerImage" class="block text-sm font-medium text-gray-700">
                        @lang('modules.settings.headerImage')
                    </label>
                <input
                    class="block w-full text-sm border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 text-slate-500 mt-1"
                    type="file" wire:model="headerImage">
                <x-input-error for="headerImage" class="mt-2" />
            </div>
        @if ($existingImageUrl)
                <div class="mt-4">
                    <label for="headerImage" class="block text-sm font-medium text-gray-700">
                    @lang('modules.settings.preview')
                 </label>
                    <div class="mt-2 relative">
                        @if (Str::endsWith($existingImageUrl, ['.jpg', '.jpeg', '.png']))
                            <div class="relative inline-block">
                                <img src="{{ $existingImageUrl }}" alt="Expense Receipt"
                                    class="rounded-lg shadow-md w-32 h-auto border">
                                <button type="button" wire:click="removeImage"
                                    class="absolute -top-2 -right-2 bg-gray-500 text-white rounded-full p-1 hover:bg-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-gray-500 mt-4">@lang('modules.expenses.noReceiptAvailable')</p>
            @endif
        <!-- Save Button -->
            <x-button class="mt-4">@lang('app.update')</x-button>
        </div>

    </form>
</div>
