<div class="grid grid-cols-1 gap-6 mx-4 p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">

    <div >
        <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.settings.kotSettings')</h3>

        <form wire:submit="submitForm" class="grid gap-6 grid-cols-1 md:grid-cols-2">
            <div class="grid gap-6 border border-gray-200 dark:border-gray-700 p-4 rounded-lg">
                <div>
                    <div class="relative flex items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                        <div class="flex items-center h-5">
                            <input type="checkbox" id="enableItemLevelStatus" wire:model="enableItemLevelStatus" 
                                class="w-5 h-5 border-gray-300 rounded text-primary-600 focus:ring-primary-500">
                        </div>
                        <div class="ml-4">
                            <label for="enableItemLevelStatus" class="text-base font-medium text-gray-900 dark:text-white">
                                @lang('modules.settings.enableItemLevelStatus')
                            </label>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @lang('modules.settings.enableItemLevelStatusDescription')
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <x-label for="defaultKotStatus" :value="__('modules.settings.defaultKotStatus')" />
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.defaultKotStatusDescription')</p>
                    <div class="mt-4 grid gap-4">
                        <div class="relative flex items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                            <div class="flex items-center h-5">
                                <input id="statusPending" type="radio" wire:model="defaultKotStatus" value="pending" 
                                    class="w-5 h-5 border-gray-300 text-primary-600 focus:ring-primary-500">
                            </div>
                            <div class="ml-4">
                                <label for="statusPending" class="text-base font-medium text-gray-900 dark:text-white">@lang('modules.settings.kotStatusesPending')</label>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.kotStatusesPendingDescription')</p>
                            </div>
                        </div>

                        <div class="relative flex items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                            <div class="flex items-center h-5">
                                <input id="statusCooking" type="radio" wire:model="defaultKotStatus" value="cooking" 
                                    class="w-5 h-5 border-gray-300 text-primary-600 focus:ring-primary-500">
                            </div>
                            <div class="ml-4">
                                <label for="statusCooking" class="text-base font-medium text-gray-900 dark:text-white">@lang('modules.settings.kotStatusesCooking')</label>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.kotStatusesCookingDescription')</p>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>

            <div class="col-span-1 md:col-span-2">
                <x-button>@lang('app.save')</x-button>
            </div>
        </form>
    </div>

</div>
