<div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 mt-4">
    <div class="space-y-8">
        <section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3">
            <form wire:submit="saveHeading">
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
                <div class="mb-6">
                    <label for="heading" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        @lang('modules.settings.title')
                    </label>
                    <input type="text" id="heading" wire:model="faqHeading"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700
                        dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <x-input-error for="heading" class="mt-2" />
                </div>
                <div class="mb-6">
                    <label for="faqDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        @lang('modules.settings.description')
                    </label>
                    <input x-ref="faqDescription" id="faqDescription" name="faqDescription" wire:model="faqDescription"
                        value="{{ $faqDescription }}" type="hidden" />
                    <div wire:ignore class="mt-1">
                        <trix-editor class="trix-content text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            input="faqDescription"
                            data-gramm="false"
                            placeholder="{{ __('placeholders.featureDescriptionPlaceHolder') }}"
                            x-on:trix-change="$wire.set('faqDescription', $event.target.value)"
                            x-ref="trixEditor"
                            x-init="
                                window.addEventListener('reset-trix-editor', () => {
                                    $refs.trixEditor.editor.loadHTML('');
                                });" >
                        </trix-editor>
                    </div>
                    <x-input-error for="faqDescription" class="mt-2" />
                </div>
                <x-button class="mt-4">@lang('app.update')</x-button>
            </form>
        </section>

        <section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    @lang('modules.settings.faq')
                </h2>
                <x-button wire:click="addFaqModal" class="flex items-center">
                    @lang('modules.settings.addFaq')
                </x-button>
            </div>

            <div class="space-y-4">
                @forelse($faqDetails as $faq)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200
                        dark:border-gray-600 transition-all hover:shadow-md">
                        <p class="text-gray-900 font-bold">{{ $faq->question }}</p>
                         <hr class="my-2 border-gray-300">
                        <p class="text-gray-700 dark:text-gray-300 mb-4">{!! $faq->answer !!}</p>

                        <div class="flex flex-wrap gap-2 justify-end">
                            <x-secondary-button-table
                                wire:click='editFaqPage({{ $faq->id }})'
                                wire:key='faq-edit-{{ $faq->id . microtime() }}'
                                class="text-sm">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"/>
                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                                </svg>
                                @lang('app.update')
                            </x-secondary-button-table>
                            <x-danger-button
                                wire:click="deleteFaq({{ $faq->id }})"
                                class="text-sm">
                                 <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"/>
                                </svg>
                                @lang('app.delete')
                            </x-danger-button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">@lang('modules.settings.noFaq')</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <x-dialog-modal wire:model.live="editFaqPageModal">
        <x-slot name="title">
            @lang('modules.settings.editFaq')
        </x-slot>
        <x-slot name="content">
            @if($faqDetail)
                @livewire('landing-site.edit-faq-page', ['faqDetail' => $faqDetail, 'languageEnable' => $languageEnable], key('edit-faq-page-' . microtime()))
            @endif
        </x-slot>
    </x-dialog-modal>
     <x-dialog-modal wire:model.live="showAddFaqModal">
                <x-slot name="title">
                    @lang('modules.settings.addFaq')
                </x-slot>
                <x-slot name="content">
                    <form wire:submit="saveFaq">
                        @csrf
                        <div class="mb-4">
                            <x-label for="faqAnswer" value="{{ __('modules.settings.question') }}" />
                            <input type="text" id="faqQuestion" wire:model="faqQuestion"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <x-input-error for="faqQuestion" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-label for="faqAnswer" value="{{ __('modules.settings.answer') }}" />
                            <input x-ref="faqAnswer" id="faqAnswer" name="faqAnswer" wire:model="faqAnswer"
                                value="{{$faqAnswer}}" type="hidden" />
                            <div wire:ignore class="mt-2">
                                <trix-editor class="trix-content text-sm" input="faqAnswer" data-gramm="false"
                                    placeholder="{{ __('placeholders.menuContentPlaceHolder') }}"
                                    x-on:trix-change="$wire.set('faqAnswer', $event.target.value)" x-ref="trixEditor"
                                    x-init="window.addEventListener('reset-trix-editor', () => {
                                        $refs.trixEditor.editor.loadHTML('');
                                    });">
                                </trix-editor>
                            </div>
                            <x-input-error for="faqAnswer" class="mt-2" />

                        </div>
                        <div class="flex w-full pb-4 space-x-4 mt-6 justify-end">
                            <x-button>@lang('app.save')</x-button>
                            <x-button-cancel wire:click="$set('showAddFaqModal', false)">@lang('app.cancel')</x-button-cancel>
                        </div>
                    </form>
                </x-slot>
            </x-dialog-modal>

</div>
