<div>
    <div class="mx-4 p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-3 pt-4 px-5 border-b">
                    <h4 class="card-title text-lg font-semibold mb-1">@lang('superadmin.desktopApplicationSettings')</h4>
                    <p class="card-text text-sm text-gray-500">@lang('superadmin.desktopApplicationSettingsDescription')</p>
                    <p class="mt-2 text-xs text-blue-700 dark:text-blue-300 font-medium">@lang('superadmin.desktopApplicationSettingsDescription2')</p>
                    <div class="mt-3 p-3 bg-yellow-100 border-l-4 border-yellow-400 text-yellow-800 rounded">
                        <p>@lang('superadmin.desktopApplicationSettingsNote')</p>
                    </div>
                </div>
                <div class="card-body p-5">

                    <!-- Desktop App Requirement Notice -->
                    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg dark:bg-amber-900/20 dark:border-amber-800">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h5 class="text-sm font-medium text-amber-800 dark:text-amber-200">@lang('superadmin.desktopAppRequired')</h5>
                                <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">@lang('superadmin.desktopAppRequiredDescription')</p>
                            </div>
                        </div>
                    </div>

                    <!-- TableTrack Desktop App Preview -->
                    @if(($windows_file_path === \App\Models\DesktopApplication::WINDOWS_FILE_PATH) && ($mac_file_path === \App\Models\DesktopApplication::MAC_FILE_PATH))
                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                        <div class="flex items-center justify-center">
                            <div class="text-center">
                                <div class="w-16 h-16 flex items-center justify-center mx-auto mb-3">
                                    <img src="{{ asset('img/icon.png') }}" alt="TableTrack Desktop App" class="w-14 h-14 object-contain">
                                </div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">TableTrack Desktop Application</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Download the official TableTrack desktop app for enhanced functionality</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Desktop App Demo Video -->
                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                        <div class="text-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">@lang('superadmin.desktopAppDemo')</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Watch how the TableTrack desktop application works</p>
                        </div>
                        <div class="flex justify-center">
                            <a href="https://www.youtube.com/watch?v=KKla4E_e_tY"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                                Watch Demo on YouTube
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Windows Application URL -->
                        <div class="p-4 border rounded bg-gray-50 dark:bg-gray-800">
                            <div class="flex items-center mb-3">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6"><rect x="3" y="3" width="7" height="7" rx="1" fill="#2563eb"></rect><rect x="14" y="3" width="7" height="7" rx="1" fill="#2563eb"></rect><rect x="3" y="14" width="7" height="7" rx="1" fill="#2563eb"></rect><rect x="14" y="14" width="7" height="7" rx="1" fill="#2563eb"></rect></svg>
                                <h5 class="ml-1 text-base font-semibold text-gray-900 dark:text-white">@lang('superadmin.windowsApplication')</h5>
                            </div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('superadmin.downloadUrl')</label>
                            <div class="relative">
                                <input type="url" wire:model="windows_file_path"
                                       class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                       placeholder="https://example.com/windows-app.exe">
                                @if(!empty($windows_file_path))
                                    <button type="button" wire:click="$set('windows_file_path', '')"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            @error('windows_file_path')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="flex gap-2 mt-3">
                                @if($windows_file_path !== \App\Models\DesktopApplication::WINDOWS_FILE_PATH)
                                    <button type="button" wire:click="resetWindowsUrl" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-600 text-sm">@lang('superadmin.resetToDefault')</button>
                                @endif
                                @if(!empty($windows_file_path))
                                    <a href="{{ $windows_file_path }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        @lang('superadmin.downloadNow')
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Mac Application URL -->
                        <div class="p-4 border rounded bg-gray-50 dark:bg-gray-800">
                            <div class="flex items-center mb-3">
                                <svg fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"></path></svg>
                                <h5 class="text-base font-semibold text-gray-900 dark:text-white">@lang('superadmin.macApplication')</h5>
                            </div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('superadmin.downloadUrl')</label>
                            <div class="relative">
                                <input type="url" wire:model="mac_file_path"
                                       class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded text-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                       placeholder="https://example.com/mac-app.dmg">
                                @if(!empty($mac_file_path))
                                    <button type="button" wire:click="$set('mac_file_path', '')"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            @error('mac_file_path')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="flex gap-2 mt-3">
                                @if($mac_file_path !== \App\Models\DesktopApplication::MAC_FILE_PATH)
                                    <button type="button" wire:click="resetMacUrl" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-600 text-sm">@lang('superadmin.resetToDefault')</button>
                                @endif
                                @if(!empty($mac_file_path))
                                    <a href="{{ $mac_file_path }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        @lang('superadmin.downloadNow')
                                    </a>
                                @endif
                            </div>
                        </div>

                    </div>
   <!-- Single Save Button -->
   <div class="flex justify-end mt-6 mb-6">
    <button type="button" wire:click="saveAll"
            class="relative px-6 py-2 bg-blue-600 text-sm text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            wire:loading.attr="disabled"
            wire:target="saveAll">
        <span wire:loading.remove wire:target="saveAll">@lang('superadmin.saveAllSettings')</span>
        <span wire:loading wire:target="saveAll" class="flex items-center">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            @lang('superadmin.saving')
        </span>
    </button>
</div>
                    <!-- White Label Custom Desktop App Section -->
                    <div class="py-10 mb-6 p-6 border rounded-lg bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 border-purple-200 dark:border-purple-800">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h5 class="text-lg font-semibold text-purple-900 dark:text-purple-100">@lang('superadmin.whiteLabelDesktopApp')</h5>
                                <p class="text-sm text-purple-700 dark:text-purple-300">@lang('superadmin.whiteLabelDesktopAppDescription')</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="font-medium text-purple-800 dark:text-purple-200 mb-3">@lang('superadmin.whiteLabelFeatures')</h6>
                            <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-purple-700 dark:text-purple-300">
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    @lang('superadmin.whiteLabelFeature1')
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    @lang('superadmin.whiteLabelFeature2')
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    @lang('superadmin.whiteLabelFeature3')
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    @lang('superadmin.whiteLabelFeature4')
                                </li>
                            </ul>
                        </div>

                        <!-- Order Button -->
                        <div class="flex justify-center">
                            <a href="https://envato.froid.works/my-account?tab=desktop-app"
                                target="_blank"
                                class="inline-flex items-center justify-center px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                @lang('superadmin.orderWhiteLabelApp')
                            </a>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
