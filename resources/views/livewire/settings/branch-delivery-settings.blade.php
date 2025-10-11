<div>
    <div class="p-6 bg-white border border-gray-200 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6 space-y-4 md:space-y-0">
            <h3 class="text-xl font-semibold dark:text-white">@lang('modules.delivery.deliverySettings')</h3>
        </div>

        @if(is_null($branch->lat) && is_null($branch->lng))
        <div class="mb-4">
            <x-alert type="warning" class="flex items-center justify-between gap-4">
                <div class="flex-1">
                    <strong class="font-medium">@lang('messages.noBranchCoordinates')</strong>
                </div>
                <x-secondary-link href="{{ route('settings.index').'?tab=branch' }}" wire:navigate>
                    <span>@lang('modules.settings.branchSettings')</span>
                    <svg width="24" height="24" class="h-4 w-4 ms-1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-4m-8-2 8-8m0 0v5m0-5h-5"/></svg>
                </x-secondary-link>
            </x-alert>
        </div>
        @endif

        <form wire:submit="save" class="space-y-6">
            <!-- Enable/Disable Delivery (commented maybe useful later) -->
            {{-- <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <label class="relative inline-flex items-center cursor-pointer mb-2">
                    <x-checkbox id="enableDelivery" wire:model="isEnabled" />
                    <span class="ml-3 font-medium text-gray-900 dark:text-gray-100">@lang('modules.delivery.enableDelivery')</span>
                </label>
            </div> --}}

            <!-- Fee Type Specific Options -->
            <div class="p-5 bg-gray-50 border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
                <h4 class="text-lg font-medium mb-4 dark:text-white">@lang('modules.delivery.feeDetails')</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fee Type -->
                    <div>
                        <x-label for="selectedFeeType" value="{{ __('modules.delivery.feeCalculationMethod') }}" class="mb-2" />
                        <x-select id="selectedFeeType" class="block w-full" wire:model.live="selectedFeeType">
                            @foreach($feeTypes as $value => $label)
                            <option value="{{ $value }}">@lang($label)</option>
                            @endforeach
                        </x-select>
                        <x-input-error for="selectedFeeType" class="mt-2" />
                    </div>
                    <!-- Distance Unit -->
                    <div>
                        <x-label for="unit" value="{{ __('modules.delivery.distanceUnit') }}" class="mb-2" />
                        <x-select id="unit" class="block w-full" wire:model.live="unit">
                            <option value="km">@lang('modules.delivery.kilometers')</option>
                            <option value="miles">@lang('modules.delivery.miles')</option>
                        </x-select>
                        <x-input-error for="unit" class="mt-2" />
                    </div>

                </div>

                @if($selectedFeeType !== 'tiered')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-2">
                <!-- Delivery Radius -->
                <div>
                    <x-label for="maxRadius" :value="__('modules.delivery.maxRadius')" />
                    <div class="relative inline-flex items-center mt-1 rounded-md w-full">
                        <x-input id="maxRadius" class="block w-full text-gray-900 rounded pr-16 placeholder:text-gray-400"
                            type="number" wire:model="maxRadius" step="0.01" autocomplete="off" placeholder="0.00" min="0" required />
                        <div class="absolute inset-y-0 right-0 inline-flex items-center px-4 pointer-events-none">
                            <span class="text-gray-500">{{ $unit }}</span>
                        </div>
                    </div>
                    <x-input-error for="maxRadius" class="mt-2" />
                </div>
                @else
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    @lang('modules.delivery.maxRadiusAutoSet')
                </p>
                @endif

                @switch($selectedFeeType)
                    @case('fixed')
                        <div>
                            <x-label for="fixedFee" :value="__('modules.delivery.fixedFee')" />
                            <div class="relative inline-flex items-center mt-1 rounded-md w-full">
                                <div class="absolute inset-y-0 left-0 inline-flex items-center pl-4 pointer-events-none">
                                    <span class="text-gray-500">{{ currency() }}</span>
                                </div>
                                <x-input id="fixedFee" class="block w-full text-gray-900 rounded pl-10 placeholder:text-gray-400" type="number" wire:model="fixedFee" step="0.01" autocomplete="off" min="0" placeholder="0.00" />
                            </div>
                            <x-input-error for="fixedFee" class="mt-2" />
                        </div>
                        @break

                    @case('per_distance')
                        <div>
                            <x-label for="perDistanceRate" :value="__('modules.delivery.feePerDistance', ['unit' => $unit])" />
                            <div class="relative inline-flex items-center mt-1 rounded-md w-full">
                                <div class="absolute inset-y-0 left-0 inline-flex items-center pl-4 pointer-events-none">
                                    <span class="text-gray-500">{{ currency() }}</span>
                                </div>
                                <x-input id="perDistanceRate" class="block w-full text-gray-900 rounded pl-10 placeholder:text-gray-400" type="number" wire:model="perDistanceRate" step="0.01" min="0" autocomplete="off" placeholder="0.00" required />
                            </div>
                            <x-input-error for="perDistanceRate" class="mt-2" />
                        </div>
                        @break

                    @case('tiered')
                        <div class="my-4">
                            <div class="mb-4">
                                <h5 class="font-medium text-gray-900 dark:text-white">@lang('modules.delivery.distanceTiers')</h5>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @lang('modules.delivery.distanceTiersDescription')</p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-100 dark:bg-gray-800">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">
                                                @lang('modules.delivery.minDistance', ['unit' => $unit])
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">
                                                @lang('modules.delivery.maxDistance', ['unit' => $unit])
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">
                                                @lang('modules.delivery.fee') ({{ currency() }})
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400 w-24">
                                                @lang('app.action')
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-700 dark:divide-gray-600">
                                        @foreach($tiers as $index => $tier)
                                        <tr>
                                            <td class="px-4 py-2">
                                                <x-input type="number" min="0" step="0.01" class="w-full" wire:model="tiers.{{ $index }}.min_distance" />
                                                <x-input-error for="tiers.{{ $index }}.min_distance" class="mt-1" />
                                            </td>
                                            <td class="px-4 py-2">
                                                <x-input type="number" min="0" step="0.01" class="w-full" wire:model="tiers.{{ $index }}.max_distance" />
                                                <x-input-error for="tiers.{{ $index }}.max_distance" class="mt-1" />
                                            </td>
                                            <td class="px-4 py-2">
                                                <x-input type="number" min="0" step="0.01" class="w-full" wire:model="tiers.{{ $index }}.fee" />
                                                <x-input-error for="tiers.{{ $index }}.fee" class="mt-1" />
                                            </td>
                                            <td class="px-4 py-2">
                                                @if (count($tiers) > 1)
                                                    <x-danger-button type="button" wire:click="removeTier({{ $index }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                    </x-danger-button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="mt-4">
                                    <x-button secondary type="button" wire:click="addTier">
                                        <i class="fas fa-plus mr-1"></i> @lang('modules.delivery.addTier')
                                    </x-button>
                                    <x-input-error for="tiers" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        @break
                @endswitch

                @if($selectedFeeType !== 'tiered')
                </div>
                @endif
            </div>

            <!-- Free Delivery Options -->
            <div class="p-5 bg-gray-50 border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
                <h4 class="text-lg font-medium mb-4 dark:text-white">@lang('modules.delivery.freeDeliveryOptions')</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-label for="freeDeliveryOverAmount" :value="__('modules.delivery.freeDeliveryOverAmount')" />
                        <div class="relative inline-flex items-center mt-1 rounded-md w-full">
                            <div class="absolute inset-y-0 left-0 inline-flex items-center pl-4 pointer-events-none">
                                <span class="text-gray-500">{{ currency() }}</span>
                            </div>
                            <x-input id="freeDeliveryOverAmount" class="block w-full text-gray-900 rounded pl-10 placeholder:text-gray-400" type="number" wire:model="freeDeliveryOverAmount" step="0.01" autocomplete="off" min="0" placeholder="0.00" />
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            @lang('modules.delivery.leaveEmptyToDisable')</p>
                        <x-input-error for="freeDeliveryOverAmount" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="freeDeliveryWithinRadius" :value="__('modules.delivery.freeDeliveryWithinRadius')" />
                        <div class="relative inline-flex items-center mt-1 rounded-md w-full">
                            <x-input id="freeDeliveryWithinRadius" class="block w-full text-gray-900 rounded pr-16 placeholder:text-gray-400"
                                type="number" wire:model="freeDeliveryWithinRadius" step="0.01" autocomplete="off" placeholder="0.00" />
                            <div class="absolute inset-y-0 right-0 inline-flex items-center px-4 pointer-events-none">
                                <span class="text-gray-500">{{ $unit }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            @lang('modules.delivery.leaveEmptyToDisable')</p>
                        <x-input-error for="freeDeliveryWithinRadius" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Delivery Schedule -->
            <div class="p-5 bg-gray-50 border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
                <h4 class="text-lg font-medium mb-4 dark:text-white">@lang('modules.delivery.deliverySchedule')</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-label for="deliveryScheduleStart" value="{{ __('modules.delivery.deliveryHoursStart') }}" class="mb-2" />
                        <div class="relative">
                            <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <input type="time" id="deliveryScheduleStart" wire:model="deliveryScheduleStart" class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-800 dark:placeholder-gray-500 dark:text-white dark:focus:ring-gray-600 dark:focus:border-gray-600" min="00:00" max="23:59" />
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            @lang('modules.delivery.leave247Delivery')</p>
                        <x-input-error for="deliveryScheduleStart" class="mt-1" />
                    </div>
                    <div>
                        <x-label for="deliveryScheduleEnd" value="{{ __('modules.delivery.deliveryHoursEnd') }}" class="mb-2" />
                        <div class="relative">
                            <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <input type="time" id="deliveryScheduleEnd" wire:model="deliveryScheduleEnd" class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-800 dark:placeholder-gray-500 dark:text-white dark:focus:ring-gray-600 dark:focus:border-gray-600" min="00:00" max="23:59" />
                        </div>
                        <x-input-error for="deliveryScheduleEnd" class="mt-1" />
                    </div>
                </div>
            </div>

            <!-- Delivery Time Estimation -->
            <div class="p-5 bg-gray-50 border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
                <h4 class="text-lg font-medium mb-4 dark:text-white">@lang('modules.delivery.deliveryTimeEstimate')</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Average Delivery Speed -->
                    <div>
                        <x-label for="avgDeliverySpeedKmh" :value="__('modules.delivery.avgDeliverySpeed')" />
                        <div class="relative inline-flex items-center mt-1 rounded-md w-full">
                            <x-input id="avgDeliverySpeedKmh" class="block w-full text-gray-900 rounded pr-16 placeholder:text-gray-400"
                                type="number" wire:model="avgDeliverySpeedKmh" required min="0" placeholder="0.00" />
                            <div class="absolute inset-y-0 right-0 inline-flex items-center px-4 pointer-events-none">
                                <span class="text-gray-500">{{ $unit === 'km' ? 'km/h' : 'mph' }}</span>
                            </div>
                        </div>
                        <x-input-error for="avgDeliverySpeedKmh" class="mt-2" />
                    </div>

                    <!-- Additional ETA Buffer Time -->
                    <div>
                        <x-label for="additionalEtaBufferTime" :value="__('modules.delivery.additionalEtaBufferTime')" />
                        <div class="relative inline-flex items-center mt-1 rounded-md w-full">
                            <x-input id="additionalEtaBufferTime" class="block w-full text-gray-900 rounded pr-20 placeholder:text-gray-400"
                                type="number" wire:model="additionalEtaBufferTime" min="0" placeholder="0" />
                            <div class="absolute inset-y-0 right-0 inline-flex items-center px-4 pointer-events-none">
                                <span class="text-gray-500">@lang('modules.delivery.minutes')</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            @lang('modules.delivery.additionalEtaBufferTimeDescription')
                        </p>
                        <x-input-error for="additionalEtaBufferTime" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="pt-4">
                <x-button primary>
                    <i class="fas fa-save mr-1"></i> @lang('app.save')
                </x-button>
            </div>
        </form>
    </div>
</div>
