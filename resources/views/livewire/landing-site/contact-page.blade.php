<div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 mt-4">
    <div class="space-y-8">
        <section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3">
            <form wire:submit="saveContactHeading">
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
                    <label for="contactHeading" class="block text-sm font-medium text-gray-700">
                        @lang('modules.settings.title')
                    </label>
                    <input type="text"
                        id="contactHeading"
                        wire:model="contactHeading"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <x-input-error for="contactHeading" class="mt-2" />
                </div>
                <x-button class="mt-4">@lang('app.update')</x-button>
            </form>
        </section>

        <section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3">
            <div class="space-y-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    @lang('modules.settings.addContact')
                </h2>
                <form wire:submit="saveContact" class="mt-4">
                    <div class="mb-4">
                        <x-label for="email" value="{{ __('modules.settings.email') }}" />
                        <input type="email"
                            id="email"
                            wire:model="email"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <x-input-error for="email" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-label for="contactCompany" value="{{ __('modules.settings.contactCompany') }}" />
                        <input type="text"
                            id="contactCompany"
                            wire:model="contactCompany"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <x-input-error for="contactCompany" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-label for="address" value="{{ __('modules.settings.address') }}" />
                        <textarea
                            id="address"
                            wire:model="address"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </textarea>
                        <x-input-error for="address" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-label for="contactImage" value="{{ __('modules.settings.contactImage') }}" />
                        <input
                            type="file"
                            id="contactImage"
                            wire:model.live="contactImage"
                            accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-md file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-indigo-50 file:text-indigo-700
                                   hover:file:bg-indigo-100"
                        >
                        <x-input-error for="contactImage" class="mt-2" />

                        {{-- Real-time preview for newly selected image --}}
                        @if ($tempImageUrl)
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    @lang('modules.settings.newImagePreview')
                                </label>
                                <div class="mt-2">
                                    <img src="{{ $tempImageUrl }}" alt="New Contact Image Preview"
                                        class="rounded-lg shadow-md w-32 h-auto border border-indigo-300">
                                </div>
                            </div>
                        @endif

                        {{-- Existing image preview --}}
                        @if ($existingImageUrl && !$tempImageUrl)
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    @lang('modules.settings.currentImage')
                                </label>
                                <div class="mt-2">
                                    @if (Str::endsWith($existingImageUrl, ['.jpg', '.jpeg', '.png', '.gif']))
                                        <img src="{{ $existingImageUrl }}" alt="Current Contact Image"
                                            class="rounded-lg shadow-md w-32 h-auto border">
                                    @endif
                                </div>
                            </div>
                        @elseif (!$existingImageUrl && !$tempImageUrl)
                            <p class="text-gray-500 mt-4 dark:text-gray-400">@lang('modules.settings.noImageSelected')</p>
                        @endif
                    </div>
                    <div class="flex w-full pb-4 space-x-4 mt-6 justify-start">
                        <x-button>@lang('app.update')</x-button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
