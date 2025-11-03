<div>
    <div class="p-4 mx-4 mb-3 bg-white rounded-lg border border-gray-200 shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <h3 class="mb-3 text-xl font-semibold dark:text-white">@lang('modules.settings.orderSetting')</h3>
        <x-help-text class="mb-6">@lang('modules.settings.orderSettingsHelp')</x-help-text>

        <!-- tabs -->
        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
            <ul class="flex flex-wrap items-center -mb-px">
                <li class="me-2">
                    <span wire:click="$set('activeTab', 'prefix')"
                        @class([
                            'inline-flex items-center gap-x-2 cursor-pointer select-none p-3 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                            'border-transparent'=> $activeTab != 'prefix',
                            'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => $activeTab == 'prefix',
                        ])>
                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg bg-skin-base/20 text-skin-base font-extrabold text-md shadow dark:bg-skin-base/20">
                            #
                        </span>
                        @lang('modules.order.prefixSettings')
                    </span>
                </li>
                <li class="me-2">
                    <span wire:click="$set('activeTab', 'menuItemImage')"
                        @class([
                            'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                            'border-transparent'=> $activeTab != 'menuItemImage',
                            'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => $activeTab == 'menuItemImage',
                        ])>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                        </svg>
                        @lang('modules.order.menuItemImageSettings')
                    </span>
                </li>
                <li class="me-2">
                    <span wire:click="$set('activeTab', 'tokenSettings')"
                        @class([
                            'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                            'border-transparent'=> $activeTab != 'tokenSettings',
                            'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => $activeTab == 'tokenSettings',
                        ])>
                        <!-- SVG: Token Icon -->
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/>
                        </svg>
                        @lang('modules.order.tokenNumberSettings')
                    </span>
                </li>
                <li class="me-2">
                    <span wire:click="$set('activeTab', 'customOrderTypes')"
                        @class([
                            'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                            'border-transparent'=> $activeTab != 'customOrderTypes',
                            'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => $activeTab == 'customOrderTypes',
                        ])>
                        <!-- SVG: Custom Order Types Icon -->
                        <svg class="w-4 h-4 text-current" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                        @lang('modules.settings.customOrderTypes')
                    </span>
                </li>
                <li class="me-2">
                    <span wire:click="$set('activeTab', 'deliveryApps')"
                        @class([
                            'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                            'border-transparent'=> $activeTab != 'deliveryApps',
                            'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => $activeTab == 'deliveryApps',
                        ])>
                        <!-- SVG: Delivery Apps Icon -->
                        <svg class="w-5 h-5 text-current" height="32" viewBox="0 0 32 32" width="32" xmlns="http://www.w3.org/2000/svg"><g style="fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-width:2"><path d="M31.098 5.76H9.327v17.893h11.286m5.173 0h2.19V17.27M2.528 23.653H.902v-6.065l4.212-4.949h4.213v11.014H7.7"/><circle cx="23.2" cy="23.653" r="2.586"/><circle cx="5.114" cy="23.653" r="2.586"/><path d="M27.976 9.906h-4.777m6.827 4.149h-4.24"/></g></svg>
                        @lang('modules.settings.deliveryApps')
                    </span>
                </li>
            </ul>
        </div>

        <div>
            @switch($activeTab)
                @case('prefix')
                    <!-- Prefix Settings Form -->
                    <form wire:submit="saveOrderSettings" class="p-4 space-y-6 md:p-6">
                        <div class="flex gap-x-3 items-center p-4 bg-gray-100 rounded-lg shadow-sm dark:bg-gray-700">
                            <x-checkbox name="enableFeature" id="enableFeature" wire:model.live='enableFeature' class="mr-4" />
                            <div class="flex-1">
                                <x-label for="enableFeature" :value="__('modules.order.enableOrderPrefix')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.order.enableOrderPrefixDescription')
                                </p>
                            </div>
                        </div>

                        @if($enableFeature)
                            <!-- Compact Row for Prefix, Separator, Digits -->
                            <div class="grid grid-cols-1 gap-4 mt-2 md:grid-cols-2">
                                <div>
                                    <x-label for="prefix" class="mb-1" :value="__('modules.order.customPrefix')" />
                                    <x-input id="prefix" type="text" class="w-full" wire:model.live="prefix" />
                                    <x-input-error for="prefix" class="mt-1 text-xs" />
                                    <x-help-text class="mt-1 text-xs">@lang('modules.order.branchPrefixHelp')</x-help-text>
                                </div>
                                <div class="flex gap-2">
                                    <div class="w-1/2">
                                        <x-label for="separator" class="mb-1" :value="__('modules.order.separator')" />
                                        <x-input id="separator" type="text" maxlength="1" pattern="[^a-zA-Z0-9]" oninput="this.value = this.value.replace(/[a-zA-Z0-9]/g, '')" class="w-full" wire:model.live="separator" />
                                        <x-input-error for="separator" class="mt-1 text-xs" />
                                    </div>
                                    <div class="w-1/2">
                                        <x-label for="digits" class="mb-1" :value="__('modules.order.digits')" />
                                        <x-input id="digits" type="number" min="1" max="10" class="w-full" wire:model.live="digits" />
                                        <x-input-error for="digits" class="mt-1 text-xs" />
                                    </div>
                                </div>
                            </div>

                            <!-- Date Parts Simple Row -->
                            <div class="mt-3">
                                <x-label :value="__('modules.order.dateParts')" class="mb-2" />
                                <div class="flex flex-wrap gap-4 mt-2">
                                    <label for="showYear" class="flex gap-2 items-center px-4 py-2 bg-white rounded-lg border border-gray-200 shadow-sm transition cursor-pointer dark:bg-gray-900 dark:border-gray-700 hover:border-indigo-400">
                                        <x-checkbox wire:model.live="showYear" id="showYear" class="accent-indigo-600" />
                                        <span class="text-gray-700 dark:text-gray-200">@lang('modules.order.showYear')</span>
                                    </label>
                                    <label for="showMonth"
                                        class="flex gap-2 items-center px-4 py-2 bg-white rounded-lg border border-gray-200 shadow-sm transition cursor-pointer dark:bg-gray-900 dark:border-gray-700 hover:border-indigo-400">
                                        <x-checkbox wire:model.live="showMonth" id="showMonth" class="accent-indigo-600" />
                                        <span class="text-gray-700 dark:text-gray-200">@lang('modules.order.showMonth')</span>
                                    </label>
                                    <label for="showDay" class="flex gap-2 items-center px-4 py-2 bg-white rounded-lg border border-gray-200 shadow-sm transition cursor-pointer dark:bg-gray-900 dark:border-gray-700 hover:border-indigo-400">
                                        <x-checkbox wire:model.live="showDay" id="showDay" class="accent-indigo-600" />
                                        <span class="text-gray-700 dark:text-gray-200">@lang('modules.order.showDay')</span>
                                    </label>
                                    <label for="showTime" class="flex gap-2 items-center px-4 py-2 bg-white rounded-lg border border-gray-200 shadow-sm transition cursor-pointer dark:bg-gray-900 dark:border-gray-700 hover:border-indigo-400">
                                        <x-checkbox wire:model.live="showTime" id="showTime" class="accent-indigo-600" />
                                        <span class="text-gray-700 dark:text-gray-200">@lang('modules.order.showTime')</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Reset Daily Setting -->
                            <div class="mt-4">
                                <div class="flex gap-x-3 items-center p-4 bg-gray-100 rounded-lg shadow-sm dark:bg-gray-700">
                                    <x-checkbox name="resetDaily" id="resetDaily" wire:model.live='resetDaily'
                                        class="mr-4 accent-indigo-600" />
                                    <div class="flex-1">
                                        <x-label for="resetDaily" :value="__('modules.order.resetDaily')" class="!mb-1" />
                                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.order.resetDailyHelp')</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Preview Section -->
                        <div class="flex flex-col justify-center items-center p-4 space-y-4 bg-indigo-50 rounded-2xl border-2 border-indigo-200 border-dashed dark:bg-gray-950 md:p-6 dark:border-gray-700">
                            <span class="text-xl font-bold text-gray-500 dark:text-gray-400">@lang('modules.order.preview')</span>
                            <span class="font-mono text-4xl font-extrabold tracking-wider text-indigo-700 dark:text-indigo-400">
                                {{ $this->preview }}
                            </span>
                            <p class="max-w-sm text-sm text-center text-gray-500 dark:text-gray-400">
                                @lang('modules.order.previewHelp')
                            </p>
                        </div>

                        <!-- Save Button -->
                        <div class="pt-4">
                            <x-button type="submit" wire:loading.attr="disabled" wire:target='saveOrderSettings'
                                class="inline-flex gap-x-2 items-center">
                                <!-- Optimized right arrow icon -->
                                <div>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                        wire:loading.remove wire:target='saveOrderSettings'>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg aria-hidden="true" wire:loading wire:target='saveOrderSettings'
                                        class="w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-skin-base"
                                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                            fill="currentColor" />
                                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                            fill="currentFill" />
                                    </svg>
                                </div>
                                <span>
                                    @lang('modules.settings.save')
                                </span>
                            </x-button>
                        </div>
                    </form>
                    @break

                @case('menuItemImage')
                    <!-- Menu Item Image Settings Form -->
                    <form wire:submit="saveMenuItemImageSettings" class="p-4 space-y-6 md:p-6">
                        <div class="space-y-4">
                            <!-- Show Menu Image on POS -->
                            <div class="flex gap-x-3 items-center p-4 bg-gray-100 rounded-lg shadow-sm dark:bg-gray-700">
                                <x-checkbox name="hideMenuItemImageOnPos" id="hideMenuItemImageOnPos" wire:model.live='hideMenuItemImageOnPos'
                                    class="mr-4" />
                                <div class="flex-1">
                                    <x-label for="hideMenuItemImageOnPos" :value="__('modules.order.hideMenuItemImageOnPos')" class="!mb-1" />
                                    <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.order.hideMenuItemImageOnPosDescription')</p>
                                </div>
                            </div>

                            <!-- Show Menu Image on Customer Site -->
                            <div class="flex gap-x-3 items-center p-4 bg-gray-100 rounded-lg shadow-sm dark:bg-gray-700">
                                <x-checkbox name="hideMenuItemImageOnCustomerSite" id="hideMenuItemImageOnCustomerSite" wire:model.live='hideMenuItemImageOnCustomerSite'
                                    class="mr-4" />
                                <div class="flex-1">
                                    <x-label for="hideMenuItemImageOnCustomerSite" :value="__('modules.order.hideMenuItemImageOnCustomerSite')" class="!mb-1" />
                                    <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.order.hideMenuItemImageOnCustomerSiteDescription')</p>
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="pt-4">
                            <x-button type="submit" wire:loading.attr="disabled" wire:target='saveMenuItemImageSettings' class="inline-flex gap-x-2 items-center">
                                <!-- Optimized right arrow icon -->
                                <div>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" wire:loading.remove wire:target='saveMenuItemImageSettings'>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg aria-hidden="true" wire:loading wire:target='saveMenuItemImageSettings' class="w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-skin-base" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg>
                                </div>
                                <span>
                                    @lang('modules.settings.save')
                                </span>
                            </x-button>
                        </div>
                    </form>
                    @break

                @case('tokenSettings')
                    <!-- Token Settings Form -->
                    <form wire:submit="saveTokenSettings" class="p-4 space-y-6 md:p-6">
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">@lang('modules.order.tokenNumberSettings')</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                @lang('modules.order.tokenNumberSettingsDescription')
                            </p>
                        </div>

                        <div class="space-y-4">
                            @forelse($this->orderTypes as $orderType)
                                <div class="flex gap-x-3 items-center p-4 bg-gray-100 rounded-lg shadow-sm dark:bg-gray-700">
                                    <x-checkbox
                                        name="tokenSettings.{{ $orderType->id }}"
                                        id="tokenSettings_{{ $orderType->id }}"
                                        wire:model.live="tokenSettings.{{ $orderType->id }}"
                                        class="mr-4 accent-indigo-600" />
                                    <div class="flex-1">
                                        <x-label
                                            for="tokenSettings_{{ $orderType->id }}"
                                            :value="$orderType->order_type_name"
                                            class="!mb-1 font-medium" />
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            @lang('modules.order.tokenNumberSettingsDescriptionOrderType', ['orderType' => $orderType->order_type_name])
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    @lang('modules.order.noOrderTypesFound')
                                </div>
                            @endforelse
                        </div>

                        <!-- Save Button -->
                        <div class="pt-4">
                            <x-button type="submit" wire:loading.attr="disabled" wire:target='saveTokenSettings' class="inline-flex gap-x-2 items-center">
                                <!-- Optimized right arrow icon -->
                                <div>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" wire:loading.remove wire:target='saveTokenSettings'>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg aria-hidden="true" wire:loading wire:target='saveTokenSettings' class="w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-skin-base" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg>
                                </div>
                                <span>
                                    @lang('modules.settings.save')
                                </span>
                            </x-button>
                        </div>
                    </form>
                    @break

                @case('customOrderTypes')
                    @livewire('settings.customOrderTypes', ['settings' => $settings])
                    @break

                @case('deliveryApps')
                    @livewire('settings.deliveryApps', ['settings' => $settings])
                    @break
            @endswitch
        </div>
    </div>
</div>
