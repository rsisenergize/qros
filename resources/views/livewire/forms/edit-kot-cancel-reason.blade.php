<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="reason" value="{{ __('modules.settings.reason') }}" />
                <x-input id="reason" class="block mt-1 w-full" type="text"  wire:model='reason' />
                <x-input-error for="reason" class="mt-2" />
            </div>

            <!-- Cancellation Types -->
            <div>
                <x-label value="{{ __('modules.settings.cancellationTypes') }}" class="mb-2" />
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="cancel_order" class="w-4 h-4 bg-gray-100 rounded border-gray-300 text-skin-base focus:ring-skin-base dark:focus:ring-skin-base dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" @checked($cancel_order)>
                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">@lang('modules.order.cancelOrder')</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" wire:model="cancel_kot" class="w-4 h-4 bg-gray-100 rounded border-gray-300 text-skin-base focus:ring-skin-base dark:focus:ring-skin-base dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" @checked($cancel_kot)>
                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">@lang('modules.order.cancelKot')</span>
                    </label>
                </div>
                <x-input-error for="cancel_order" class="mt-2" />
            </div>

        </div>

        <div class="flex pb-4 mt-6 space-x-4 w-full rtl:space-x-reverse">
            <x-button>@lang('app.update')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideEditKotReason')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
