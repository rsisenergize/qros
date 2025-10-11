<div x-data="{ activeTab: @entangle('activeSetting') }" class="mx-4">

    <!-- Tabs Navigation -->
    <div class="flex flex-wrap border-b border-gray-200 dark:border-gray-700 mb-4">
        <button @click="activeTab = 'settings'; $wire.set('activeSetting', 'settings')"
            :class="activeTab === 'settings'
                ?
                'border-b-2 border-skin-base text-skin-base dark:text-skin-base dark:border-skin-base font-semibold' :
                'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
            class="py-4 px-6 focus:outline-none transition-colors duration-200 text-sm">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>@lang('modules.settings.disableLandingSite')</span>
            </div>
        </button>
        @if ($landingType == 'dynamic')

        <button @click="activeTab = 'showdynamicPage'; $wire.set('activeSetting', 'showdynamicPage')"
            :class="activeTab === 'showdynamicPage'
            ?
            'border-b-2 border-skin-base text-skin-base dark:text-skin-base dark:border-skin-base font-semibold' :
            'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
            class="py-4 px-6 focus:outline-none transition-colors duration-200 text-sm">
            <div class="flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
            <span>@lang('modules.settings.showMoreWebPage')</span>
            </div>
        </button>
        <!-- header Page Tab -->
        <button @click="activeTab = 'headerPage'; $wire.set('activeSetting', 'headerPage')"
            :class="activeTab === 'headerPage'
            ?
            'border-b-2 border-skin-base text-skin-base dark:text-skin-base dark:border-skin-base font-semibold' :
            'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
            class="py-4 px-6 focus:outline-none transition-colors duration-200 text-sm">
            <div class="flex items-center space-x-2">
           <svg class="w-5 h-5 text-current" viewBox="0 -5.5 21 21" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>header [#1539]</title> <desc>Created with Sketch.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="Dribbble-Light-Preview" transform="translate(-99.000000, -165.000000)" fill="currentColor"> <g id="icons" transform="translate(56.000000, 160.000000)"> <path d="M46.15,5 C47.88985,5 49.3,6.343 49.3,8 C49.3,9.657 47.88985,11 46.15,11 C44.41015,11 43,9.657 43,8 C43,6.343 44.41015,5 46.15,5 L46.15,5 Z M46.15,7 C45.57145,7 45.1,7.449 45.1,8 C45.1,8.551 45.57145,9 46.15,9 C46.72855,9 47.2,8.551 47.2,8 C47.2,7.449 46.72855,7 46.15,7 L46.15,7 Z M43,15 L64,15 L64,13 L43,13 L43,15 Z M51.4,7 L64,7 L64,5 L51.4,5 L51.4,7 Z M51.4,11 L64,11 L64,9 L51.4,9 L51.4,11 Z" id="header-[#1539]"> </path> </g> </g> </g> </g></svg>
            <span>@lang('modules.settings.headerPage')</span>
            </div>
        </button>
       {{-- End --}}
        <!-- Feature image Page Tab -->
        <button @click="activeTab = 'featureWithImage'; $wire.set('activeSetting', 'featureWithImage')"
            :class="activeTab === 'featureWithImage'
            ?
            'border-b-2 border-blue-600 text-blue-600 dark:text-blue-500 dark:border-blue-500 font-semibold' :
            'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
            class="py-4 px-6 focus:outline-none transition-colors duration-200 text-sm">
            <div class="flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
            <span>@lang('modules.settings.featureWithImage')</span>
            </div>
        </button>
       {{-- End --}}

       <!-- Feature image Page Tab -->
        <button @click="activeTab = 'featureWithIcon'; $wire.set('activeSetting', 'featureWithIcon')"
            :class="activeTab === 'featureWithIcon'
            ?
            'border-b-2 border-skin-base text-skin-base dark:text-skin-base dark:border-skin-base font-semibold' :
            'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
            class="py-4 px-6 focus:outline-none transition-colors duration-200 text-sm">
            <div class="flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
            <span>@lang('modules.settings.featureWithIcon')</span>
            </div>
        </button>
       {{-- End --}}

       <!-- review Page Tab -->
        <button @click="activeTab = 'reviewPage'; $wire.set('activeSetting', 'reviewPage')"
            :class="activeTab === 'reviewPage'
            ?
            'border-b-2 border-skin-base text-skin-base dark:text-skin-base dark:border-skin-base font-semibold' :
            'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
            class="py-4 px-6 focus:outline-none transition-colors duration-200 text-sm">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-current" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M4,23H20a1,1,0,0,0,1-1V6a1,1,0,0,0-.293-.707l-4-4A1,1,0,0,0,16,1H4A1,1,0,0,0,3,2V22A1,1,0,0,0,4,23ZM5,3H15.586L19,6.414V21H5Zm8,13v1a1,1,0,0,1-2,0V16a1,1,0,0,1,2,0Zm1.954-7.429a3.142,3.142,0,0,1-1.789,3.421.4.4,0,0,0-.165.359V13a1,1,0,0,1-2,0v-.649a2.359,2.359,0,0,1,1.363-2.191A1.145,1.145,0,0,0,12.981,8.9a1.069,1.069,0,0,0-.8-.88.917.917,0,0,0-.775.2,1.155,1.155,0,0,0-.4.9,1,1,0,1,1-2,0,3.151,3.151,0,0,1,1.127-2.436,2.946,2.946,0,0,1,2.418-.632A3.085,3.085,0,0,1,14.954,8.571Z"></path></g></svg>
            <span>@lang('modules.settings.reviewSetting')</span>
            </div>
        </button>
       {{-- End --}}
       <!-- faq Page Tab -->
        <button @click="activeTab = 'faqPage'; $wire.set('activeSetting', 'faqPage')"
            :class="activeTab === 'faqPage'
            ?
            'border-b-2 border-skin-base text-skin-base dark:text-skin-base dark:border-skin-base font-semibold' :
            'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
            class="py-4 px-6 focus:outline-none transition-colors duration-200 text-sm">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-current" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill="currentColor" fill-rule="evenodd" d="M8,0 C8.55228,0 9,0.447715 9,1 L11,1 L11,2 L13,2 C13.5523,2 14,2.44772 14,3 L14,15 C14,15.5523 13.5523,16 13,16 L3,16 C2.44772,16 2,15.5523 2,15 L2,3 C2,2.44772 2.44772,2 3,2 L5,2 L5,1 L7,1 C7,0.447715 7.44772,0 8,0 Z M5,4 L4,4 L4,14 L12,14 L12,4 L11,4 L11,5 L5,5 L5,4 Z M6,10 L10,10 C10.5523,10 11,10.4477 11,11 C11,11.51285 10.613973,11.9355092 10.1166239,11.9932725 L10,12 L6,12 C5.44772,12 5,11.5523 5,11 C5,10.48715 5.38604429,10.0644908 5.88337975,10.0067275 L6,10 Z M10,7 C10.5523,7 11,7.44772 11,8 C11,8.55228 10.5523,9 10,9 L6,9 C5.44772,9 5,8.55228 5,8 C5,7.44772 5.44772,7 6,7 L10,7 Z M8,2 C7.44772,2 7,2.44772 7,3 C7,3.55228 7.44772,4 8,4 C8.55228,4 9,3.55228 9,3 C9,2.44772 8.55228,2 8,2 Z"></path> </g></svg>
            <span>@lang('modules.settings.faqSetting')</span>
            </div>
        </button>
       {{-- End --}}

        <!-- contact Page Tab -->
        <button @click="activeTab = 'contactPage'; $wire.set('activeSetting', 'contactPage')"
            :class="activeTab === 'contactPage'
            ?
            'border-b-2 border-skin-base text-skin-base dark:text-skin-base dark:border-skin-base font-semibold' :
            'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
            class="py-4 px-6 focus:outline-none transition-colors duration-200 text-sm">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-current" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M20,1H4A1,1,0,0,0,3,2V22a1,1,0,0,0,1,1H20a1,1,0,0,0,1-1V2A1,1,0,0,0,20,1ZM19,21H5V3H19ZM9,8.5a3,3,0,1,1,3,3A3,3,0,0,1,9,8.5Zm-2,9a5,5,0,0,1,10,0,1,1,0,0,1-2,0,3,3,0,0,0-6,0,1,1,0,0,1-2,0Z"></path></g></svg>
            <span>@lang('modules.settings.contactSetting')</span>
            </div>
        </button>
       {{-- End --}}

        <!-- footer setting Page Tab -->
        <button @click="activeTab = 'priceSetting'; $wire.set('activeSetting', 'priceSetting')"
            :class="activeTab === 'priceSetting'
            ?
            'border-b-2 border-skin-base text-skin-base dark:text-skin-base dark:border-skin-base font-semibold' :
            'text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
            class="py-4 px-6 focus:outline-none transition-colors duration-200 text-sm">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M17 18h-4v-1h4zm-9 0h3v-1H8zm12-4h-7v1h7zm-9 0H4v1h7zM23 3v18H1V3zm-1 9H2v8h20zm0-8H2v7h20z"></path><path fill="none" d="M0 0h24v24H0z"></path></g></svg>
            <span>@lang('modules.settings.priceSetting')</span>
            </div>
        </button>
       {{-- End --}}
    @endif

    </div>
    <!-- Settings Tab -->
    <div x-show="activeTab === 'settings'"
        class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 mt-4">
        <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.settings.disableLandingSite')</h3>
        <x-help-text class="mb-6">@lang('modules.settings.disableLandingSiteHelp')</x-help-text>
        <form wire:submit="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div class="col-span-2 mb-4">
                    <div class="flex items-center space-x-6 rtl:space-x-reverse dark:text-gray-200">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="landingType" value="static"
                                wire:model="landingType"
                                {{ $landingType === 'static' ? 'checked' : '' }}
                                class="form-radio h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600">
                            <span class="ms-2 text-gray-900 dark:text-gray-300"> @lang('modules.settings.staticLandingPage')</span>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="landingType" value="dynamic"
                                wire:model="landingType"
                                {{ $landingType === 'dynamic' ? 'checked' : '' }}
                                class="form-radio h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600">
                            <span class="ms-2 text-gray-900 dark:text-gray-300"> @lang('modules.settings.dynamicLandingPage')</span>
                        </label>
                    </div>
                </div>
                    <div class="border p-4 rounded-lg dark:border-gray-500 space-y-4">
                     <div>
                        <x-label for="disableLandingSite">
                            <div class="flex items-center cursor-pointer">
                                <x-checkbox name="disableLandingSite" id="disableLandingSite"
                                    wire:model.live='disableLandingSite' />
                                <div class="ms-2">
                                    @lang('modules.settings.disableLandingSite')
                                </div>
                            </div>
                        </x-label>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            @lang('modules.settings.disableLandingSiteHelpDescription')
                        </p>
                     </div>
                     @if (!$disableLandingSite)
                        <div>
                            <x-label for="landingSiteType" :value="__('modules.settings.landingSiteType')" />
                            <x-select id="landingSiteType" class="mt-1 block w-full" wire:model.live="landingSiteType">
                                <option value="theme">@lang('modules.settings.theme')</option>
                                <option value="custom">@lang('modules.settings.custom')</option>
                            </x-select>
                            <x-input-error for="landingSiteType" class="mt-2" />
                        </div>

                        @if ($landingSiteType == 'custom')
                            <div>
                                <x-label for="landingSiteUrl" value="Landing Site URL" />
                                <x-input id="landingSiteUrl" class="block mt-1 w-full" type="text"
                                    wire:model='landingSiteUrl' />
                                <x-input-error for="landingSiteUrl" class="mt-2" />
                            </div>
                        @endif

                        @if ($landingSiteType == 'theme')
                            <div>
                                <x-label for="facebook" value="{{ __('modules.settings.facebook_link') }}" />
                                <x-input id="facebook" class="block mt-1 w-full" type="url"
                                    placeholder="{{ __('placeholders.facebookPlaceHolder') }}" autofocus
                                    wire:model='facebook' />
                                <x-input-error for="facebook" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="instagram" value="{{ __('modules.settings.instagram_link') }}" />
                                <x-input id="instagram" class="block mt-1 w-full" type="url"
                                    placeholder="{{ __('placeholders.instagramPlaceHolder') }}" autofocus
                                    wire:model='instagram' />
                                <x-input-error for="instagram" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="twitter" value="{{ __('modules.settings.twitter_link') }}" />
                                <x-input id="twitter" class="block mt-1 w-full" type="url"
                                    placeholder="{{ __('placeholders.twitterPlaceHolder') }}" autofocus
                                    wire:model='twitter' />
                                <x-input-error for="twitter" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="yelp" value="{{ __('modules.settings.yelp_link') }}" />
                                <x-input id="yelp" class="block mt-1 w-full" type="url"
                                    placeholder="{{ __('placeholders.yelpPlaceHolder') }}" autofocus
                                    wire:model='yelp' />
                                <x-input-error for="yelp" class="mt-2" />
                            </div>
                        @endif
                     @endif
                </div>

                <div class="border p-4 rounded-lg dark:border-gray-500 space-y-4">
                    <div>
                        <x-label for="metaTitle" value="{{ __('modules.settings.metaTitle') }}" />
                        <x-input id="metaTitle" class="block mt-1 w-full" type="text"
                            placeholder="{{ __('placeholders.metaTtilePlaceHolder') }}" autofocus
                            wire:model='metaTitle' />
                        <x-input-error for="metaTitle" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="metaKeyword" value="{{ __('modules.settings.metaKeyword') }}" />
                        <x-input id="metaKeyword" class="block mt-1 w-full" type="text"
                            placeholder="{{ __('placeholders.metaKeywordPlaceHolder') }}" autofocus
                            wire:model='metaKeyword' />
                        <x-input-error for="metaKeyword" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="metaDescription" value="{{ __('modules.settings.metaDescription') }}" />
                        <x-textarea id="metaDescription" class="block mt-1 w-full"
                            placeholder="{{ __('placeholders.metaDescriptionPlaceHolder') }}" autofocus
                            wire:model='metaDescription'></x-textarea>
                        <x-input-error for="metaDescription" class="mt-2" />
                    </div>
                </div>

                <div class="md:col-span-2 mt-4">
                    <x-button>@lang('app.save')</x-button>
                </div>
            </div>
        </form>
    </div>
    {{-- End --}}
 @if ($landingType == 'dynamic')


    <!-- Dynamic Web Page Tab -->
    <div x-show="activeTab === 'showdynamicPage'"
        class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800 mt-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold dark:text-white">@lang('modules.settings.showMoreWebPage')</h3>
            <x-button class="m-3" type="button" wire:click="$set('addDyanamicMenuModal', true)">
                @lang('modules.settings.addDyanamicMenu')
            </x-button>
        </div>
        <div class="mb-4">
            <div class="flex gap-4">
                <label class="flex items-center cursor-pointer">
                    <input type="radio" wire:model.live="menuPosition" value="header" class="peer sr-only">
                    <span class="px-4 py-2 rounded-md text-sm border border-gray-200 dark:border-gray-700
                        peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900
                        peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400
                        dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        @lang('app.header')
                    </span>
                </label>

                <label class="flex items-center cursor-pointer">
                    <input type="radio" wire:model.live="menuPosition" value="footer" class="peer sr-only">
                    <span class="px-4 py-2 rounded-md text-sm border border-gray-200 dark:border-gray-700
                        peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900
                        peer-checked:text-indigo-600 dark:peer-checked:text-indigo-400
                        dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        @lang('app.footer')
                    </span>
                </label>
            </div>
        </div>
        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th
                                class="py-3 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.settings.menuName')
                            </th>
                            <th
                                class="py-3 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.settings.menuSlug')
                            </th>
                            <th
                                class="py-3 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.settings.isActive')
                            </th>
                            <th
                                class="py-3 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                @lang('app.action')
                            </th>
                        </tr>
                    </thead>
                    <tbody wire:sortable="sortCustomMenu" class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse ($customMenu as $menu)
                            <tr wire:sortable.item="{{ $menu->id }}" class="hover:bg-gray-100 dark:hover:bg-gray-700 even:bg-gray-50 dark:even:bg-gray-700">
                                <td class="py-3 flex space-x-1 items-center px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    <span class="group cursor-move">
                                        <svg wire:sortable.handle class="w-5 h-5 text-gray-400 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-gray-200 transition-colors duration-150" viewBox="0 0 48 48">
                                            <g fill="currentColor">
                                                <circle cx="18" cy="12" r="4"/>
                                                <circle cx="18" cy="24" r="4"/>
                                                <circle cx="18" cy="36" r="4"/>
                                                <circle cx="30" cy="12" r="4"/>
                                                <circle cx="30" cy="24" r="4"/>
                                                <circle cx="30" cy="36" r="4"/>
                                            </g>
                                        </svg>
                                    </span>
                                    {{ $menu->menu_name }}
                                    <a href="{{ route('customMenu', $menu->menu_slug) }}" target="_blank" class="text-blue-600 hover:text-blue-700 dark:text-blue-500 dark:hover:text-blue-400 font-medium">
                                        <svg class="inline-block w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $menu->menu_slug }}
                                </td>


                                <td class="py-3 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="isActive-{{ $menu->id }}"
                                            wire:click="toggleMenuStatus({{ $menu->id }})"
                                            @if ($menu->is_active) checked @endif
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">

                                        <label for="isActive-{{ $menu->id }}"
                                            class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                            @lang('app.active')
                                        </label>
                                    </div>
                                </td>

                                <td class="py-3 px-4 space-x-2 whitespace-nowrap text-right">
                                    <x-secondary-button-table wire:click='showEditDynamicMenu({{ $menu->id }})'
                                        wire:key='menu-edit-{{ $menu->id . microtime() }}'>
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                            </path>
                                            <path fill-rule="evenodd"
                                                d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        @lang('app.update')
                                    </x-secondary-button-table>


                                    <button type="button"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        wire:click="confirmDeleteMenu({{ $menu->id }})">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        @lang('app.delete')
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="py-3 px-4 space-x-6 dark:text-gray-400" colspan="4">
                                    @lang('messages.noCustomMenu')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>


        <div wire:key='custom-menu-paginate-{{ $menuPosition }}'
            class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center mb-4 sm:mb-0 w-full">
                {{ $customMenu->links() }}
            </div>
        </div>

    </div>
    {{-- End --}}

    {{-- header setting  --}}
    <div x-show="activeTab === 'headerPage'">
        @livewire('LandingSite.HeaderPage')
    </div>
    {{-- End --}}

    {{-- feature image setting  --}}
    <div x-show="activeTab === 'featureWithImage'">
        @livewire('LandingSite.FrontFeaturePage')
    </div>
    {{-- End --}}

    {{-- feature icon setting  --}}
    <div x-show="activeTab === 'featureWithIcon'">
        @livewire('LandingSite.FrontFeaturePageIcon')
    </div>
    {{-- End --}}

     {{-- reviews setting  --}}
    <div x-show="activeTab === 'reviewPage'">
        @livewire('LandingSite.ReviewPage')
    </div>
    {{-- End --}}

    {{-- faq setting  --}}
    <div x-show="activeTab === 'faqPage'">
        @livewire('LandingSite.faqPage')
    </div>
    {{-- End --}}

    {{-- contact setting  --}}
    <div x-show="activeTab === 'contactPage'">
        @livewire('LandingSite.contactPage')
    </div>
    {{-- End --}}

    {{-- header setting  --}}
    <div x-show="activeTab === 'priceSetting'">
        @livewire('LandingSite.priceSetting')
    </div>
 @endif

 <x-right-modal wire:model.live="addDyanamicMenuModal">
        <x-slot name="title">
            {{ __('modules.settings.addDynamicMenu') }}
        </x-slot>

        <x-slot name="content">
            @livewire('forms.add-dyanamic-menu')
        </x-slot>

    </x-right-modal>

    <x-right-modal wire:model.live="showEditDynamicMenuModal">
        <x-slot name="title">
            {{ __('modules.settings.editDynamicMenu') }}
        </x-slot>

        <x-slot name="content">
            @if ($menuId)
                @livewire('forms.edit-dyanamic-menu', ['menuId' => $menuId], key(str()->random(50)))
            @endif
        </x-slot>

    </x-right-modal>


    <x-dialog-modal wire:model="menuIdToDelete">
        <x-slot name="title">
            @lang('modules.settings.deleteDyanamicMenu')
        </x-slot>

        <x-slot name="content">
            @lang('messages.confrmDeleteDyanamicMenu')
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-2">
                <x-secondary-button wire:click="$set('menuIdToDelete', null)" class="px-4 py-2 text-sm">
                    @lang('app.cancel')
                </x-secondary-button>

                <x-danger-button wire:click="deleteMenu" class="px-4 py-2 text-sm">
                    @lang('app.delete')
                </x-danger-button>
            </div>
        </x-slot>
    </x-dialog-modal>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>
    @endpush
</div>
