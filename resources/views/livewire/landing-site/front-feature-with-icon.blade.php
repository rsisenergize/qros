<div>
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
            <li class="mr-2" role="presentation">
                <button
                    wire:click="$set('activeTab', 'featuresIcon')"
                    @class([
                        'py-4 px-6 focus:outline-none transition-colors duration-200 text-sm',
                        'border-b-2 border-blue-600 text-blue-600 dark:text-blue-500 dark:border-blue-500 font-semibold' => $activeTab === 'featuresIcon',
                        'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' => $activeTab !== 'featuresIcon',
                    ])
                    type="button">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        <span>@lang('modules.settings.featureIcon')</span>
                    </div>
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button
                    wire:click="$set('activeTab', 'featureIconHeading')"
                    @class([
                        'py-4 px-6 focus:outline-none transition-colors duration-200 text-sm',
                        'border-b-2 border-blue-600 text-blue-600 dark:text-blue-500 dark:border-blue-500 font-semibold' => $activeTab === 'featureIconHeading',
                        'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' => $activeTab !== 'featureIconHeading',
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

    @if($activeTab === 'featuresIcon')

        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 mt-4">
            <div class="flex items-center justify-between mb-4 ">
                <x-button class="m-3" type="button" wire:click="$toggle('addFeatureModal')">
                    @lang('modules.settings.addFeatureWithIcon')
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
                                    @lang('modules.settings.featureIcon')
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
                                        {{ \Illuminate\Support\Str::limit($feature->description, 40) }}
                                    </td>
                                    <td class="py-3 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        {!! $feature->image !!}
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
                                id="title"
                                wire:model="featureTitle"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <x-input-error for="featureTitle" class="mt-2" />
                        </div>

                            <div class="sm:col-span-2 mt-4">
                                <label for="featureDescription" class="block text-sm font-medium text-gray-700">@lang('modules.settings.featureDescription')</label>
                                <input x-ref="featureDescription" id="featureDescription" name="featureDescription" wire:model="featureDescription"
                                    value="{{ $featureDescription }}" type="hidden" />

                                <div wire:ignore class="mt-1">
                                    <trix-editor class="trix-content text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        input="featureDescription"
                                        data-gramm="false"
                                        placeholder="{{ __('placeholders.featureDescriptionPlaceHolder') }}"
                                        x-on:trix-change="$wire.set('featureDescription', $event.target.value)"
                                        x-ref="trixEditor"
                                        x-init="
                                            window.addEventListener('reset-trix-editor', () => {
                                                $refs.trixEditor.editor.loadHTML('');
                                            });" >
                                    </trix-editor>
                                </div>
                                <x-input-error for="featureDescription" class="mt-2" />
                            </div>

                        <div class="sm:col-span-2 mt-4">
                            <label for="featureIcon" class="block text-sm font-medium text-gray-700 mb-2">
                                @lang('modules.settings.featureIcon')
                            </label>

                        <div class="relative" x-data="{ open: false }">
                            <div
                                class="border rounded w-full px-3 py-2 flex items-center cursor-pointer bg-white"
                                @click="open = !open"
                            >
                                @if ($selectedIcon)
                                    <x-dynamic-component :component="'heroicon-o-' . $selectedIcon" class="w-5 h-5 text-gray-600 mr-2" />
                                    <span class="text-gray-700">{{ $selectedIcon }}</span>
                                @else
                                    <span class="text-gray-400">@lang('app.selectIcon')</span>
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

                        </div>

                        <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
                            <x-button type="submit">@lang('app.save')</x-button>
                            <x-button-cancel wire:click="$dispatch('closeModal')" wire:loading.attr="disabled">
                               @lang('app.cancel')
                            </x-button-cancel>
                        </div>
                    </form>
                </x-slot>
            </x-right-modal>
        <x-right-modal wire:model.live="showEditFrontFeatureModal">
                <x-slot name="title">
                    {{ __("modules.settings.editFeatureWithicon") }}
                </x-slot>
                <x-slot name="content">
                    @if ($frontDetail)
                    @livewire('landing-site.edit-front-with-icon-feature', ['frontDetail' => $frontDetail ,'languageEnable' => $languageEnable], key(str()->random(50)))
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
        @livewire('LandingSite.FeatureIconHeading', key(str()->random(50)))
    @endif
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    <script>
        document.addEventListener('livewire:load', function () {
            $('#iconPicker').iconpicker()
                .on('iconpickerSelected', function(event){
                    @this.set('icon', event.iconpickerValue);
                });
        });
    </script>
</div>
