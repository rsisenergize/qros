<div>

    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
            <li class="mr-2" role="presentation">
                <button
                    wire:click="$set('activeTab', 'features')"
                    @class([
                        'py-4 px-6 focus:outline-none transition-colors duration-200 text-sm',
                        'border-b-2 border-blue-600 text-blue-600 dark:text-blue-500 dark:border-blue-500 font-semibold' => $activeTab === 'features',
                        'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' => $activeTab !== 'features',
                    ])
                    type="button">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        <span>@lang('modules.settings.feature')</span>
                    </div>
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button
                    wire:click="$set('activeTab', 'featureHeading')"
                    @class([
                        'py-4 px-6 focus:outline-none transition-colors duration-200 text-sm',
                        'border-b-2 border-blue-600 text-blue-600 dark:text-blue-500 dark:border-blue-500 font-semibold' => $activeTab === 'featureHeading',
                        'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' => $activeTab !== 'featureHeading',
                    ])
                    type="button">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        <span>@lang('modules.settings.featureHeading')</span>
                    </div>
                </button>
            </li>
        </ul>
    </div>
    @if($activeTab === 'features')
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 mt-4 ">
        <div class="flex items-center justify-between mb-4 ">
            <x-button class="m-3" type="button" wire:click="$toggle('addFeatureModal')">
                @lang('modules.settings.addFeature')
            </x-button>
        </div>

        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="py-3 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.settings.featureTitle')
                            </th>
                            <th class="py-3 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.settings.featureDescription')
                            </th>
                            <th class="py-3 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.settings.featureImage')
                            </th>
                            <th class="py-3 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.language')
                                </th>
                            <th class="py-3 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                @lang('app.action')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse ($frontDetails as $feature)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 even:bg-gray-50 dark:even:bg-gray-700" wire:key='feature-{{ $feature->id . microtime() }}'>
                                <td class="py-3 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $feature->title }}
                                </td>
                                <td class="py-3 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($feature->description), 50) }}
                                </td>
                                <td class="py-3 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    <img src="{{ $feature->image_url }}" alt="Feature Image" class="w-16 h-16 object-cover rounded">
                                </td>
                                 <td class="py-3 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $feature->language ? $feature->language->language_name : __('app.noLanguage') }}
                                    </td>
                                <td class="py-3 px-4 space-x-2 whitespace-nowrap text-right">
                                    <x-secondary-button-table wire:click='editFeature({{ $feature->id }})'
                                        wire:key='feature-edit-{{ $feature->id . microtime() }}'>
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                            </path>
                                            <path fill-rule="evenodd"
                                                d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        @lang('app.update')
                                    </x-secondary-button-table>

                                    <x-danger-button wire:click="deleteFrontFeature({{ $feature->id }})">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        @lang('app.delete')
                                    </x-danger-button>
                                </td>
                            </tr>
                        @empty
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="py-3 px-4 space-x-6 dark:text-gray-400" colspan="4">
                                    @lang('messages.noFeatures')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div wire:key='feature-paginate-{{ microtime() }}'
            class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center mb-4 sm:mb-0 w-full">
                {{ $frontDetails->links() }}
            </div>
        </div>

        <x-right-modal wire:model.live="addFeatureModal">
            <x-slot name="title">
                {{ __('modules.settings.addFeature') }}
            </x-slot>

            <x-slot name="content">
                <form wire:submit.prevent="saveFeature">
                    <div class="mb-4">
                        <label for="language" class="block text-sm font-medium text-gray-700">@lang('modules.settings.lanuage')</label>
                        <select id="language"
                                wire:model="language"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">@lang('app.selectLanguage')</option>
                            @foreach($languageEnable as $label)
                                <option value="{{ $label->id }}">{{ $label->language_name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="language" class="mt-2" />
                    </div>

                    <div class="sm:col-span-2">
                        <label for="featureTitle" class="block text-sm font-medium text-gray-700">@lang('modules.settings.featureTitle')</label>
                        <input type="text"
                               id="featureTitle"
                               wire:model="featureTitle"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <x-input-error for="featureTitle" class="mt-2" />
                    </div>

                    <div class="sm:col-span-2">
                        <label for="featureDescription" class="block text-sm font-medium text-gray-700">@lang('modules.settings.featureDescription')</label>

                        <input x-ref="featureDescription" id="featureDescription" name="featureDescription" wire:model.lazy="featureDescription"
                            value="{{ $featureDescription }}" type="hidden" />

                        <div wire:ignore class="mt-1">
                            <trix-editor class="trix-content text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                input="featureDescription"
                                data-gramm="false"
                                placeholder="{{ __('placeholders.featureDescriptionPlaceHolder') }}"
                                x-on:trix-change="
                                    $wire.set('featureDescription', $event.target.value, false);
                                "
                                x-ref="trixEditor"
                                x-init="
                                    // Prevent Livewire from interfering with Trix
                                    $el.addEventListener('trix-change', function(event) {
                                        $wire.set('featureDescription', event.target.value, false);
                                    });

                                    window.addEventListener('reset-trix-editor', () => {
                                        $refs.trixEditor.editor.loadHTML('');
                                    });
                                " >
                            </trix-editor>
                        </div>
                        <x-input-error for="featureDescription" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <label for="featureImage" class="block text-sm font-medium text-gray-700">@lang('modules.settings.featureImage')</label>
                        <input type="file"
                               id="featureImage"
                               wire:model="featureImage"
                               class="mt-1 block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100">
                        <x-input-error for="featureImage" class="mt-2" />
                    </div>

                    @if ($existingImageUrl)
                        <div class="mt-4">
                            <img src="{{ $existingImageUrl }}" alt="Current Image" class="w-32 h-32 object-cover rounded">
                        </div>
                    @endif

                    <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
                        <x-button type="submit">@lang('app.save')</x-button>
                        <x-button-cancel wire:click="$dispatch('hideEditFeature')" wire:loading.attr="disabled">
                            @lang('app.cancel')
                        </x-button-cancel>
                    </div>
                </form>
            </x-slot>
        </x-right-modal>

       <x-right-modal wire:model.live="showEditFrontFeatureModal">
            <x-slot name="title">
                {{ __("modules.customer.editCustomer") }}
            </x-slot>

            <x-slot name="content">
                @if ($frontDetail)
                    @livewire('landing-site.edit-front-feature', ['frontDetail' => $frontDetail, 'languageEnable' => $languageEnable], key(str()->random(50)))
                @endif
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$set('showEditFrontFeatureModal', false)" wire:loading.attr="disabled">
                    {{ __('app.close') }}
                </x-secondary-button>
            </x-slot>
        </x-right-modal>

    </div>
    @else
        @livewire('LandingSite.FeatureHeading', key(str()->random(50)))
    @endif
</div>
