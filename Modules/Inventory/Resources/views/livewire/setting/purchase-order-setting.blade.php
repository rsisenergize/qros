<div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 mx-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="">
            <h3 class="text-lg font-medium text-gray-900">@lang('inventory::modules.menu.purchaseOrderSettings')</h3>
        </div>
    </div>
    <form wire:submit.prevent="submitForm">
        <div class="p-4 space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <x-checkbox name="allowPurchaseOrder" id="allowPurchaseOrder" wire:model="allowPurchaseOrder" />

                    <div>
                        <label for="allowPurchaseOrder" class="font-medium text-gray-900 dark:text-white">
                            @lang('inventory::modules.settings.allowPurchaseOrder')
                        </label>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @lang('inventory::modules.settings.allowPurchaseOrderDescription')
                        </p>
                    </div>
                </div>
            </div>
            <x-button type="submit" class="w-full">
                @lang('app.save')
            </x-button>
        </div>

    </form>
</div>
