<div>
    <form wire:submit.prevent="submitDynamicWebPageForm">

        <x-help-text class="mb-6">@lang('modules.settings.addMoreWebPageHelp')</x-help-text>

        <div class="space-y-4">
            <div>
                <x-label for="menu_name" value="{{ __('modules.settings.menuName') }}" />
                <x-input id="menu_name" class="block mt-1 w-full" type="text"
                    placeholder="{{ __('placeholders.menuNamePlaceHolder') }}" wire:model.live="menuName" wire:keyup="generateSlug" required />
                <x-input-error for="menu_name" class="mt-2" />
                @error('menuName')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <x-label for="menu_slug" value="{{ __('modules.settings.menuSlug') }}" />
                <x-input id="menu_slug" class="block mt-1 w-full" type="text"
                    placeholder="{{ __('placeholders.menuSlugPlaceHolder') }}" wire:model="menuSlug" required />
                <x-input-error for="menu_slug" class="mt-2" />
                @error('menuSlug')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <x-label for="menuContent" value="{{ __('modules.settings.menuContent') }}" />

                <input x-ref="menuContent" id="menuContent" name="menuContent" wire:model="menuContent"
                    value="{{ $menuContent }}" type="hidden" />

                <div wire:ignore class="mt-2">
                    <trix-editor class="trix-content text-sm" input="menuContent" data-gramm="false"
                        placeholder="{{ __('placeholders.menuContentPlaceHolder') }}"
                        x-on:trix-change="$wire.set('menuContent', $event.target.value)" x-ref="trixEditor"  x-init="
                            window.addEventListener('reset-trix-editor', () => {
                                $refs.trixEditor.editor.loadHTML('');
                            });" >
                    </trix-editor>
                </div>
                <x-input-error for="menuContent" class="mt-2" />
                @error('menuContent')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <x-label for="position" value="{{ __('app.position') }}" />
                <ul class="flex w-full gap-4 mt-1">
                    <li>
                        <input type="radio" id="header"  value="header" class="hidden peer"
                            wire:model='position'>
                        <label for="header"
                            class="inline-flex items-center justify-between p-2 text-gray-600 bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-green-600 peer-checked:border-green-600 peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 text-sm font-medium">
                            @lang('app.header')
                        </label>
                    </li>
                    <li>
                        <input type="radio" id="footer" value="footer" class="hidden peer"
                            wire:model='position' />
                        <label for="footer"
                            class="inline-flex items-center justify-between p-2 text-gray-600 bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-red-600 peer-checked:border-red-600 peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 text-sm font-medium">
                            @lang('app.footer')
                        </label>
                    </li>
                </ul>
            </div>
            <div>
                <x-button>@lang('app.save')</x-button>
            </div>
        </div>
    </form>
</div>
