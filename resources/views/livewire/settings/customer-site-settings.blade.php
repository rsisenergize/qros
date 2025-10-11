<div class="mx-4 p-6 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 dark:bg-gray-800">
    <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
            @lang('modules.settings.customerSiteSettings')
        </h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            @lang('modules.settings.customerSiteSettingsDescription')
        </p>
    </div>
    <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
        <ul class="flex flex-wrap items-center -mb-px">
            <li class="me-2">
                <span wire:click="$set('activeTab', 'settings')" @class([
                    'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                    'border-transparent' => $activeTab != 'settings',
                    'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => $activeTab == 'settings',
                ])>
                @lang('modules.settings.customerSiteSettings')
                </span>
            </li>

            <li class="me-2">
                <span wire:click="$set('activeTab', 'customizeHeader')" @class([
                    'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                    'border-transparent' => $activeTab != 'customizeHeader',
                    'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => $activeTab == 'customizeHeader',
                ])>
                @lang('modules.settings.customizeHeader')
                </span>
            </li>
        </ul>
    </div>

    @if($activeTab === 'settings')
        <form wire:submit="submitForm" class="space-y-6">
            <div class="grid gap-6 grid-cols-1 md:grid-cols-2">
                <!-- Order Settings Section -->
                <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 p-6 rounded-lg">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        @lang('modules.settings.orderSettings')
                    </h4>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="flex-1">
                                <x-label for="allowCustomerOrders" :value="__('modules.settings.allowCustomerOrders')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.allowCustomerOrdersDescription')</p>
                            </div>
                            <x-checkbox name="allowCustomerOrders" id="allowCustomerOrders"
                                wire:model.live='allowCustomerOrders' class="ml-4" />
                        </div>

                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="flex-1">
                                <x-label for="customerLoginRequired" :value="__('modules.settings.customerLoginRequired')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.customerLoginRequiredDescription')</p>
                            </div>
                            <x-checkbox name="customerLoginRequired" id="customerLoginRequired"
                                wire:model='customerLoginRequired' class="ml-4" />
                        </div>

                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="flex-1">
                                <x-label for="allowDineIn" :value="__('modules.settings.allowDineIn')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.allowDineInDescription')</p>
                            </div>
                            <x-checkbox name="allowDineIn" id="allowDineIn" wire:model='allowDineIn' class="ml-4" />
                        </div>

                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="flex-1">
                                <x-label for="allowCustomerDeliveryOrders" :value="__('modules.settings.allowCustomerDeliveryOrders')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.allowCustomerDeliveryOrdersDescription')</p>
                            </div>
                            <x-checkbox name="allowCustomerDeliveryOrders" id="allowCustomerDeliveryOrders"
                                wire:model='allowCustomerDeliveryOrders' class="ml-4" />
                        </div>

                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="flex-1">
                                <x-label for="allowCustomerPickupOrders" :value="__('modules.settings.allowCustomerPickupOrders')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.allowCustomerPickupOrdersDescription')</p>
                            </div>
                            <x-checkbox name="allowCustomerPickupOrders" id="allowCustomerPickupOrders"
                                wire:model.live='allowCustomerPickupOrders' class="ml-4" />
                        </div>

                        @if ($allowCustomerPickupOrders)
                            <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                <div class="flex-1">

                                    <div class="mt-2 flex flex-col gap-2">
                                        <div>
                                            <x-label for="pickupDaysRange" :value="__('modules.settings.pickupDaysRange')" class="!mb-1" />
                                            <x-input type="number" id="pickupDaysRange" name="pickupDaysRange" wire:model="pickupDaysRange" class="mt-1 block w-full" />
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="flex-1">
                                <x-label for="enableTipShop" :value="__('modules.settings.enableTipShop')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.enableTipShopDescription')</p>
                            </div>
                            <x-checkbox name="enableTipShop" id="enableTipShop" wire:model='enableTipShop' class="ml-4" />
                        </div>

                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="flex-1">
                                <x-label for="enableTipPos" :value="__('modules.settings.enableTipPos')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.enableTipPosDescription')</p>
                            </div>
                            <x-checkbox name="enableTipPos" id="enableTipPos" wire:model='enableTipPos' class="ml-4" />
                        </div>

                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="flex-1">
                                <x-label for="autoConfirmOrders" :value="__('modules.settings.autoConfirmOrders')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.autoConfirmOrdersDescription')</p>
                            </div>
                            <x-checkbox name="autoConfirmOrders" id="autoConfirmOrders" wire:model='autoConfirmOrders' class="ml-4" />
                        </div>

                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="flex-1">
                                <x-label for="showVeg" :value="__('modules.settings.showVeg')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.showVegDescription')</p>
                            </div>
                            <x-checkbox name="showVeg" id="showVeg" wire:model='showVeg' class="ml-4" />
                        </div>

                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="flex-1">
                                <x-label for="showHalal" :value="__('modules.settings.showHalal')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.showHalalDescription')</p>
                            </div>
                            <x-checkbox name="showHalal" id="showHalal" wire:model='showHalal' class="ml-4" />
                        </div>

                    </div>
                </div>

                <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 p-6 rounded-lg">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        @lang('modules.settings.callWaiterSettings')
                    </h4>

                    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">
                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="flex-1">
                                <x-label for="isWaiterRequestEnabled" :value="__('modules.settings.isWaiterRequestEnabled')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.isWaiterRequestEnabledDescription')</p>
                            </div>
                            <x-checkbox name="isWaiterRequestEnabled" id="isWaiterRequestEnabled"
                                wire:model.live='isWaiterRequestEnabled' class="ml-4" />
                        </div>

                        @if ($isWaiterRequestEnabled)
                            <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                <div class="flex-1">
                                    <x-label for="onDesktop" :value="__('modules.settings.onDesktop')" class="!mb-1" />
                                    <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.onDesktopDescription')</p>
                                </div>
                                <x-checkbox name="isWaiterRequestEnabledOnDesktop" id="isWaiterRequestEnabledOnDesktop"
                                    wire:model.live='isWaiterRequestEnabledOnDesktop' class="ml-4" />
                            </div>

                            <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                <div class="flex-1">
                                    <x-label for="onMobile" :value="__('modules.settings.onMobile')" class="!mb-1" />
                                    <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.onMobileDescription')</p>
                                </div>
                                <x-checkbox name="isWaiterRequestEnabledOnMobile" id="isWaiterRequestEnabledOnMobile"
                                    wire:model='isWaiterRequestEnabledOnMobile' class="ml-4" />
                            </div>

                            <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                <div class="flex-1">
                                    <x-label for="openViaQrCode" :value="__('modules.settings.openViaQrCode')" class="!mb-1" />
                                    <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.openViaQrCodeDescription')</p>
                                </div>
                                <x-checkbox name="isWaiterRequestEnabledOpenByQr" id="isWaiterRequestEnabledOpenByQr"
                                    wire:model='isWaiterRequestEnabledOpenByQr' class="ml-4" />
                            </div>
                        @endif

                    </div>
                </div>
                <!-- Dine-in Settings Section -->
                <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 p-6 rounded-lg">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        @lang('modules.settings.dineInSettings')
                    </h4>
                    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">

                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="flex-1">
                                <x-label for="tableRequired" :value="__('modules.settings.tableRequiredDineIn')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.tableRequiredDineInDescription')</p>
                            </div>
                            <x-checkbox name="tableRequired" id="tableRequired" wire:model='tableRequired' class="ml-4" />
                        </div>

                        <div class="sm:col-span-2 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <x-label for="defaultReservationStatus" :value="__('modules.settings.defaultReservationStatus')" class="!mb-1" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">@lang('modules.settings.defaultReservationStatusDescription')</p>
                            <select id="defaultReservationStatus" wire:model="defaultReservationStatus"
                                class="w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                                <option value="Confirmed">@lang('modules.settings.reservationStatusConfirmed')</option>
                                <option value="Pending">@lang('modules.settings.reservationStatusPending')</option>
                                <option value="Checked_In">@lang('modules.settings.reservationStatusCheckedIn')</option>
                                <option value="Cancelled">@lang('modules.settings.reservationStatusCancelled')</option>
                                <option value="No_Show">@lang('modules.settings.reservationStatusNoShow')</option>
                            </select>
                        </div>

                    </div>


                </div>
                <!-- Pwa Settings Section -->
                <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 p-6 rounded-lg">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        @lang('modules.settings.pwaSettings')
                    </h4>
                    <div class="grid gap-4 grid-cols-1 sm:grid-cols-2">

                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="flex-1">
                                <x-label for="pwaAlertShow" :value="__('modules.settings.enbalePwaApp')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.enablePwadescription')</p>
                            </div>
                            <x-checkbox name="pwaAlertShow" id="pwaAlertShow" wire:model='pwaAlertShow' class="ml-4" />
                        </div>
                    </div>

                </div>
                <!-- Social Media Links Section -->
                <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 p-6 rounded-lg">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        @lang('modules.settings.socialMediaLinks')
                    </h4>
                    <div class="grid gap-4">
                        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <x-label for="facebook" :value="__('modules.settings.facebook_link')" class="!mb-1" />
                            <x-input id="facebook" class="block w-full mt-1" type="url"
                                placeholder="{{ __('placeholders.facebookPlaceHolder') }}" wire:model='facebook' />
                            <x-input-error for="facebook" class="mt-2" />
                        </div>

                        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <x-label for="instagram" :value="__('modules.settings.instagram_link')" class="!mb-1" />
                            <x-input id="instagram" class="block w-full mt-1" type="url"
                                placeholder="{{ __('placeholders.instagramPlaceHolder') }}" wire:model='instagram' />
                            <x-input-error for="instagram" class="mt-2" />
                        </div>

                        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <x-label for="twitter" :value="__('modules.settings.twitter_link')" class="!mb-1" />
                            <x-input id="twitter" class="block w-full mt-1" type="url"
                                placeholder="{{ __('placeholders.twitterPlaceHolder') }}" wire:model='twitter' />
                            <x-input-error for="twitter" class="mt-2" />
                        </div>

                        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <x-label for="yelp" :value="__('modules.settings.yelp_link')" class="!mb-1" />
                            <x-input id="yelp" class="block w-full mt-1" type="url"
                                placeholder="{{ __('placeholders.yelpPlaceHolder') }}" wire:model='yelp' />
                            <x-input-error for="yelp" class="mt-2" />
                        </div>

                    </div>
                </div>

                <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 p-6 rounded-lg">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                @lang('modules.settings.seo')
                    </h4>
                    <div class="grid gap-4">

                        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <x-label for="metaKeyword" value="{{ __('modules.settings.metaKeyword') }}" />
                            <x-input id="metaKeyword" class="block mt-1 w-full" type="text"
                                placeholder="{{ __('placeholders.metaKeywordPlaceHolder') }}" autofocus
                                wire:model='metaKeyword' />
                            <x-input-error for="metaKeyword" class="mt-2" />
                        </div>

                        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <x-label for="metaDescription" value="{{ __('modules.settings.metaDescription') }}" />
                            <x-textarea id="metaDescription" class="block mt-1 w-full"
                                placeholder="{{ __('placeholders.metaDescriptionPlaceHolder') }}" autofocus
                                wire:model='metaDescription'></x-textarea>
                            <x-input-error for="metaDescription" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-6">
                <x-button>
                    @lang('app.save')
                </x-button>
            </div>
        </form>
    @endif

    @if($activeTab === 'customizeHeader')
        <form wire:submit="submitForm" class="space-y-6">
            <div class="grid gap-6 grid-cols-1 md:grid-cols-2">
                <!-- Header Customization Section -->
                <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/50 p-6 rounded-lg">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        @lang('modules.settings.headerCustomization')
                    </h4>
                    
                    <div class="grid gap-4">
                        <!-- Header Type Selection -->
                        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <x-label for="headerType" :value="__('modules.settings.headerType')" class="!mb-1" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">@lang('modules.settings.headerTypeDescription')</p>
                            <div class="flex gap-4">
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="headerType" value="text" class="mr-2">
                                    <span class="text-sm">@lang('modules.settings.textHeader')</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model.live="headerType" value="image" class="mr-2">
                                    <span class="text-sm">@lang('modules.settings.imageHeader')</span>
                                </label>
                            </div>
                        </div>

                        <!-- Text Header Section -->
                        @if($headerType === 'text')
                            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                <x-label for="headerText" :value="__('modules.settings.headerText')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">@lang('modules.settings.headerTextDescription')</p>
                                <x-textarea id="headerText" class="block w-full" rows="3"
                                    placeholder="{{ __('placeholders.headerTextPlaceHolder') }}" wire:model='headerText'></x-textarea>
                                <x-input-error for="headerText" class="mt-2" />
                            </div>
                        @endif

                        <!-- Image Header Section -->
                        @if($headerType === 'image')
                            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                <x-label for="newImages" :value="__('modules.settings.headerImages')" class="!mb-1" />
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">@lang('modules.settings.headerImagesDescription')</p>
                                <div class="mt-1">
                                    <input type="file" 
                                           wire:model="newImages" 
                                           multiple 
                                           accept="image/*" 
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-skin-base file:text-white hover:file:bg-skin-base/80 border border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <x-input-error for="newImages" class="mt-2" />
                                
                                <!-- Existing Images -->
                                @if($headerImages && count($headerImages) > 0)
                                    <div class="mt-4">
                                        <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-2">@lang('modules.settings.existingImages')</h5>
                                        <div class="flex flex-wrap gap-4">
                                            @foreach($headerImages as $image)
                                                <div class="relative group w-32 h-32">
                                                    <img src="{{ $image->image_url }}" alt="{{ $image->alt_text }}" class="w-full h-full object-cover rounded-lg shadow-md">
                                                    <button type="button" wire:click="removeImage({{ $image->id }})" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all duration-200 hover:bg-red-600 shadow-lg hover:scale-110 z-10 pointer-events-auto">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-6">
                <x-button>
                    @lang('app.save')
                </x-button>
            </div>
        </form>
    @endif
</div>
