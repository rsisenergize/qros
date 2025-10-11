<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="languageCode" value="{{ __('modules.language.languageCode') }}" />
                <x-input id="languageCode" class="block mt-1 w-full" type="text" placeholder="{{ __('placeholders.languageCodePlaceholder') }}" autofocus wire:model='languageCode' />
                <x-input-error for="languageCode" class="mt-2" />
            </div>

            <div>
                <x-label for="languageName" value="{{ __('modules.language.languageName') }}" />
                <x-input id="languageName" class="block mt-1 w-full" type="text" placeholder="{{ __('placeholders.languageNamePlaceholder') }}" wire:model='languageName' />
                <x-input-error for="languageName" class="mt-2" />
            </div>

            <div>
                <div class="flex items-center gap-2">
                    <x-label for="flagCode" value="{{ __('modules.language.flagCode') }}" />
                    <a href="https://flagicons.lipis.dev/" class="text-skin-base underline underline-offset-1 font-medium" target="_blank">
                        @lang('modules.language.flagCodeHelp')
                    </a>
                </div>

                <x-input id="flagCode" class="block mt-1 w-full" type="text" placeholder="{{ __('placeholders.flagCodePlaceholder') }}" wire:model='flagCode' />
                <x-input-error for="flagCode" class="mt-2" />
            </div>
            <div>
                <x-label for="isRtl" value="{{ __('modules.language.rtl') }}" />
                <div class="flex items-center gap-4 mt-2">
                    <label class="inline-flex items-center">
                        <x-radio name="isRtl" id="isRtlYes" wire:model.live='isRtl' value="1" />
                        <span class="ms-2">@lang('app.yes')</span>
                    </label>
                    <label class="inline-flex items-center">
                        <x-radio name="isRtl" id="isRtlNo" wire:model.live='isRtl' value="0" />
                        <span class="ms-2">@lang('app.no')</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideEditLanguage')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>

