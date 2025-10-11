<div>
    <form wire:submit.prevent="editFaqPage">
        <div class="space-y-4">
            <div class="mb-4">
                <label for="languageSettingid" class="block text-sm font-medium text-gray-700">
                    @lang('modules.settings.lanuage')
                </label>
                <select id="languageSettingid"
                        wire:model="languageSettingid"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">@lang('app.selectLanguage')</option>
                    @foreach($languageEnable as $label)
                        <option value="{{ $label->id }}">{{ $label->language_name }}</option>
                    @endforeach
                </select>
                <x-input-error for="languageSettingid" class="mt-2" />
            </div>

            <div class="space-y-3">
                <x-label for="faqAnswer" value="{{ __('modules.settings.question') }}" />
                <input type="text"
                       id="faqQuestion"
                       wire:model="faqQuestion"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <x-input-error for="faqQuestion" class="mt-2" />
            </div>

            <x-label for="editMenuContent" value="{{ __('modules.settings.answer') }}" />
            <input x-ref="faqAnswer" id="faqAnswer" name="faqAnswer" wire:model="faqAnswer" value="{{ $faqAnswer }}" type="hidden" />

            <div wire:ignore>
                <trix-editor class="trix-content text-sm" input="faqAnswer" data-gramm="false"
                             x-on:trix-change="$wire.faqAnswer = $event.target.value" x-ref="trixEditor">
                </trix-editor>
            </div>
            <x-input-error for="faqAnswer" class="mt-2" />
        </div>

        <div class="mt-4 flex justify-end space-x-2">

            <x-button type="submit">
                {{ __('app.save') }}
            </x-button>
            <x-secondary-button type="button" wire:click="$dispatch('hideEditFaqModal')">
                {{ __('app.cancel') }}
            </x-secondary-button>
        </div>
    </form>
</div>
