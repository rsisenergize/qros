<div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 mt-4">
    <div class="space-y-8">
    <section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3">

    <form wire:submit="saveFeatureIconHeading">
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
        <div class="mb-4">
            <label for="featureHeading" class="block text-sm font-medium text-gray-700">
                @lang('modules.settings.featureHeading')
            </label>
            <input type="text"
                id="featureWithIconHeading"
                wire:model="featureWithIconHeading"
                class="mt-1 block w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            <x-input-error for="featureWithIconHeading" class="mt-2" />
        </div>
        <x-button class="mt-4">@lang('app.update')</x-button>
    </form>
    </section>
</div>
</div>
