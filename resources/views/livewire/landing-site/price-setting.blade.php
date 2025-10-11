<div class="p-4 bg-white  dark:bg-gray-800 mt-4">
<div class="space-y-8">
    <section class="bg-white dark:bg-gray-800 rounded-lg p-3">
    <form wire:submit.prevent="priceSettingSave">
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
        <div class="space-y-3  bg-white dark:border-gray-700 dark:bg-gray-800 ">
            <!-- Header Title -->
            <div class="sm:col-span-2">
                <label for="priceTitle" class="block text-sm font-medium text-gray-700">
                    @lang('modules.settings.priceTitle')
                </label>
                <x-input type="text"
                       id="priceTitle" wire:model="priceTitle" class="mt-1 block w-full" />
                <x-input-error for="priceTitle" class="mt-2" />

            </div>

            <!-- Header Description -->
            <div class="sm:col-span-2">
                <label for="priceDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    @lang('modules.settings.priceDescription')
                </label>
                <input x-ref="priceDescription" id="priceDescription" name="priceDescription" wire:model="priceDescription"
                    value="{{ $priceDescription }}" type="hidden" />
                <div wire:ignore class="mt-1">
                    <trix-editor class="trix-content text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        input="priceDescription"
                        data-gramm="false"
                        placeholder="{{ __('placeholders.featureDescriptionPlaceHolder') }}"
                        x-on:trix-change="$wire.set('priceDescription', $event.target.value)"
                        x-ref="trixEditor"
                        x-init="
                            window.addEventListener('reset-trix-editor', () => {
                                $refs.trixEditor.editor.loadHTML('');
                            });" >
                    </trix-editor>
                </div>
                <x-input-error for="priceDescription" class="mt-2" />
            </div>


            <x-button class="mt-4">@lang('app.update')</x-button>
        </div>

    </form>
    </section>
</div>
</div>
