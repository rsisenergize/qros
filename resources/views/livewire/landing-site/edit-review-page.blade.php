<div>
    <form wire:submit.prevent="editReviewPage">
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
                <div class="mt-4">
                    <label for="reviewerName" class="block text-sm font-medium text-gray-700">
                        @lang('modules.settings.reviewerName')
                    </label>
                    <input type="text"
                           id="reviewerName"
                           wire:model="reviewerName"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="Enter reviewer name">
                    <x-input-error for="reviewerName" class="mt-2" />
                </div>
                <div class="mt-4">
                    <label for="reviewerDesignation" class="block text-sm font-medium text-gray-700">
                        @lang('modules.settings.reviewerDesignation')
                    </label>
                    <input type="text"
                           id="reviewerDesignation"
                           wire:model="reviewerDesignation"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="Enter designation">
                    <x-input-error for="reviewerDesignation" class="mt-2" />
                </div>
                <div class="mt-4">
                    <label for="reviewText" class="block text-sm font-medium text-gray-700">
                        @lang('modules.settings.editReview')
                    </label>
                    <textarea id="reviewText"
                              wire:model="reviewText"
                              rows="3" data-gramm="false"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    <x-input-error for="reviewText" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="mt-4 flex justify-end space-x-2">
            <x-button type="submit">
                {{ __('app.save') }}
            </x-button>
            <x-secondary-button type="button" wire:click="$dispatch('hideEditReviewModal')">
                {{ __('app.cancel') }}
            </x-secondary-button>
        </div>
    </form>
</div>
