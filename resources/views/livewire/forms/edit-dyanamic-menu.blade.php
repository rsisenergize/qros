<div>
    <form wire:submit="editDynamicMenu" x-data="{ content: @entangle('editMenuContent').live }">
        <div class="space-y-4">
            <div>
                <x-label for="editMenuName" value="{{ __('modules.settings.menuName') }}" />
                <x-input id="editMenuName" class="block mt-1 w-full" type="text"
                    placeholder="{{ __('placeholders.menuNamePlaceHolder') }}" wire:model.live="editMenuName" wire:keyup="generateSlug" required />
                <x-input-error for="editMenuName" class="mt-2" />
            </div>

            <div>
                <x-label for="editMenuSlug" value="{{ __('modules.settings.menuSlug') }}" />
                <x-input id="editMenuSlug" class="block mt-1 w-full" type="text"
                    placeholder="{{ __('placeholders.menuSlugPlaceHolder') }}" wire:model="editMenuSlug" required />
                <x-input-error for="editMenuSlug" class="mt-2" />
            </div>

            <div>
                <x-label for="editMenuContent" value="{{ __('modules.settings.menuContent') }}" />

                <input x-ref="editMenuContent" id="editMenuContent" name="editMenuContent" wire:model="editMenuContent"
                    value="{{ $editMenuContent }}" type="hidden" />

                <div wire:ignore>
                    <trix-editor class="trix-content text-sm" input="editMenuContent" data-gramm="false"
                        x-on:trix-change="$wire.editMenuContent = $event.target.value" x-ref="trixEditor"></trix-editor>
                </div>

                <x-input-error for="editMenuContent" class="mt-2" />
            </div>


            <div>
                <x-label for="editMenuPosition" value="{{ __('app.position') }}" />
                <ul class="flex w-full gap-4 mt-1">
                    <li>
                        <input type="radio" id="edit-header" value="header" class="hidden peer"
                            wire:model='editMenuPosition'>
                        <label for="edit-header"
                            class="inline-flex items-center justify-between p-2 text-gray-600 bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-green-600 peer-checked:border-green-600 peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 text-sm font-medium">
                            @lang('app.header')
                        </label>
                    </li>
                    <li>
                        <input type="radio" id="edit-footer" value="footer" class="hidden peer"
                            wire:model='editMenuPosition'>
                        <label for="edit-footer"
                            class="inline-flex items-center justify-between p-2 text-gray-600 bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-red-600 peer-checked:border-red-600 peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 text-sm font-medium">
                            @lang('app.footer')
                        </label>
                    </li>
                </ul>
                <x-input-error for="editMenuPosition" class="mt-2" />
            </div>

            <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
                <x-button>@lang('app.save')</x-button> 
            </div>
        </div>
    </form>
</div>
