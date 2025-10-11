<div>
    <form wire:submit="saveSettings">
        <div class="space-y-6">
            <!-- Enable/Disable Reservation Settings -->
            <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">
                    @lang('modules.reservation.reservationSettings')
                </h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Admin Reservation Toggle -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                @lang('modules.reservation.enableAdminReservation')
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @lang('modules.reservation.enableAdminReservationDescription')
                            </p>
                        </div>
                        <x-checkbox wire:model="enable_admin_reservation" />
                    </div>

                    <!-- Customer Reservation Toggle -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                @lang('modules.reservation.enableCustomerReservation')
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @lang('modules.reservation.enableCustomerReservationDescription')
                            </p>
                        </div>
                        <x-checkbox wire:model="enable_customer_reservation" />
                    </div>

                    <!-- Minimum Party Size -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                        <div class="flex-1">
                            <label for="minimum_party_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                @lang('modules.reservation.minimumPartySize')
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                @lang('modules.reservation.minimumPartySizeDescription')
                            </p>
                        </div>
                        <div class="ml-4">
                            <input
                                type="number"
                                id="minimum_party_size"
                                wire:model="minimum_party_size"
                                min="1"
                                max="50"
                                class="block w-20 px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-skin-base focus:border-skin-base dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="1"
                            />
                            <x-input-error for="minimum_party_size" class="mt-1" />
                        </div>
                    </div>

                    <!-- Last Minute Booking Settings -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg dark:border-gray-700">
                        <div class="flex-1">
                            <label for="disableSlotMinutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                @lang('modules.reservation.disableSlotMinutes')
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                @lang('modules.reservation.disableSlotMinutesInfo')
                            </p>
                        </div>
                        <div class="ml-4">
                            <div class="relative w-32">
                                <input
                                    type="number"
                                    id="disableSlotMinutes"
                                    wire:model.live="disableSlotMinutes"
                                    min="0"
                                    max="1440"
                                    step="5"
                                    class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-skin-base focus:border-skin-base dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                                <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3 pointer-events-none text-sm text-gray-500 dark:text-gray-400">
                                    @lang('app.minutes')
                                </div>
                            </div>
                            <x-input-error for="disableSlotMinutes" class="mt-1" />
                        </div>
                    </div>
                </div>
                        </div>

            <!-- Save Button -->
            <div class="flex justify-end">
                <x-button type="submit">
                    @lang('app.save')
                </x-button>
            </div>
        </div>
    </form>
</div>
