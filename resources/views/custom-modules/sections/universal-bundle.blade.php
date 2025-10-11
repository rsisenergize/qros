@if ($universalBundle)
    @php
        $fetchSetting = null;
        if (in_array(strtolower($universalBundle->getName()), custom_module_plugins()) && config(strtolower($universalBundle->getName()) . '.setting')) {
            $fetchSetting = config(strtolower($universalBundle->getName()) . '.setting')::first();
        }
    @endphp

    <div class='bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 mb-3 overflow-hidden'>
        <!-- Header Section with Gradient -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600 px-4 py-2.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <!-- Icon Container -->
                    <div class="flex-shrink-0 bg-white dark:bg-gray-600 p-2 rounded-lg shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 148.319 148.319">
                            <g id="Group_1192" data-name="Group 1192" transform="translate(0 0)">
                                <path id="Path_1092" data-name="Path 1092" d="M222.36,482h8.691v8.691H222.36Z"
                                    transform="translate(-157.945 -342.371)" fill="#fc6" />
                                <path id="Path_1093" data-name="Path 1093" d="M0,259.64H8.691v8.691H0Z"
                                    transform="translate(0 -184.426)" fill="#ffe666" />
                                <path id="Path_1094" data-name="Path 1094"
                                    d="M87.9,343.049H96.6v8.691H87.9ZM24.383,368.021c10.842-10.485,22.345,1.1,22.345,1.1,30.347-8.212,29.264-38.96,29.264-38.96L64.876,310.85,20.835,363.085A21.736,21.736,0,0,0,24.383,368.021Z"
                                    transform="translate(-14.799 -220.801)" fill="#fc6" />
                                <path id="Path_1095" data-name="Path 1095"
                                    d="M.755,329.786l8.483,5.51c-7.059,8.145-5.2,15.831-2.667,20.6L54.71,307.76,39.5,300.74s-30.53-1.3-38.741,29.046Z"
                                    transform="translate(-0.536 -213.614)" fill="#ffe666" />
                                <path id="Path_1096" data-name="Path 1096"
                                    d="M138.662,330.161l-11.117-19.31-18.483,26.676A32.686,32.686,0,0,0,138.662,330.161Z"
                                    transform="translate(-77.469 -220.802)" fill="#cc295f" />
                                <path id="Path_1097" data-name="Path 1097"
                                    d="M106.7,330.359l22.579-22.579-15.213-7.02A32.686,32.686,0,0,0,106.7,330.359Z"
                                    transform="translate(-75.108 -213.634)" fill="#f37" />
                                <path id="Path_1098" data-name="Path 1098"
                                    d="M172.865,302.977l11.117,11.117,6.553-6.553-11.117-19.31Z"
                                    transform="translate(-122.788 -204.734)" fill="#cfcfe6" />
                                <path id="Path_1099" data-name="Path 1099"
                                    d="M141.043,278.14l-6.552,6.553,11.116,11.117,10.649-10.649Z"
                                    transform="translate(-95.531 -197.567)" fill="#ece6f2" />
                                <path id="Path_1100" data-name="Path 1100"
                                    d="M213.154,284.922l-11.117-19.31-6.552,14.746L206.6,291.475Z"
                                    transform="translate(-138.856 -188.668)" fill="#a3aacc" />
                                <path id="Path_1101" data-name="Path 1101"
                                    d="M163.662,255.521l-6.552,6.552,11.117,11.117,10.649-10.649Z"
                                    transform="translate(-111.597 -181.5)" fill="#cfcfe6" />
                                <path id="Path_1102" data-name="Path 1102"
                                    d="M312.615,257.181c4.392,17.012-20.209,41.613-20.209,41.613L279.1,285.486l14.1-25.1Z"
                                    transform="translate(-198.247 -182.679)" fill="#cc295f" />
                                <path id="Path_1103" data-name="Path 1103"
                                    d="M152.784,115.913c-17.012-4.392-41.613,20.209-41.613,20.209l13.308,13.308,25.1-14.1Z"
                                    transform="translate(-78.966 -81.961)" fill="#f37" />
                                <path id="Path_1104" data-name="Path 1104"
                                    d="M390.679,9.65,345.137,47l29.646,21.452a55.305,55.305,0,0,0,15.9-58.8Z"
                                    transform="translate(-245.156 -6.854)" fill="#cc295f" />
                                <path id="Path_1105" data-name="Path 1105"
                                    d="M299.369,18.692l17.355,25.549L358.17,2.8A55.305,55.305,0,0,0,299.369,18.692Z"
                                    transform="translate(-212.646 0)" fill="#f37" />
                                <path id="Path_1106" data-name="Path 1106"
                                    d="M263.1,138.579l-26.593,18.4-18.4,26.593,17.669,17.669,47.844-40.4q.471-.4.932-.807Z"
                                    transform="translate(-154.922 -98.435)" fill="#ece6f2" />
                                <path id="Path_1107" data-name="Path 1107"
                                    d="M198.32,64.526q-.409.462-.807.932L157.11,113.3l17.669,17.669,44.993-44.993Z"
                                    transform="translate(-111.597 -45.834)" fill="#fff5f5" />
                                <path id="Path_1108" data-name="Path 1108"
                                    d="M357.067,122.224a15.946,15.946,0,0,0,0-22.526l-15.36,7.166-7.166,15.36A15.946,15.946,0,0,0,357.067,122.224Z"
                                    transform="translate(-237.629 -70.817)" fill="#101040" />
                                <path id="Path_1109" data-name="Path 1109"
                                    d="M323.12,88.277a15.946,15.946,0,0,0,0,22.526l22.526-22.526A15.947,15.947,0,0,0,323.12,88.277Z"
                                    transform="translate(-226.208 -59.396)" fill="#283366" />
                                <path id="Path_1110" data-name="Path 1110"
                                    d="M365.991,131.147a7.246,7.246,0,0,0,0-10.236l-7.166,3.07-3.07,7.166A7.245,7.245,0,0,0,365.991,131.147Z"
                                    transform="translate(-252.698 -85.885)" fill="#cfcfe6" />
                                <path id="Path_1111" data-name="Path 1111"
                                    d="M350.564,115.722a7.246,7.246,0,0,0,0,10.236L360.8,115.722A7.246,7.246,0,0,0,350.564,115.722Z"
                                    transform="translate(-247.507 -80.695)" fill="#ece6f2" />
                            </g>
                        </svg>
                    </div>

                    <!-- Module Info -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $universalBundle->getName() }}</h3>
                        @if (config(strtolower($universalBundle->getName()) . '.setting'))
                            @php $envatoId = $plugins->where('envato_id', config(strtolower($universalBundle) . '.envato_item_id'))->first(); @endphp
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                                @include('custom-modules.sections.version', ['module' => $universalBundle])
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Toggle Switch -->
                @if (!config(strtolower($universalBundle->getName()) . '.name'))
                    <div class="flex-shrink-0 relative"
                         x-data="{ tooltip: false }"
                         x-on:mouseenter="tooltip = true"
                         x-on:mouseleave="tooltip = false">
                        <label class="relative inline-flex items-center cursor-pointer group">
                            <input type="checkbox" class="sr-only peer change-module-status"
                                id="module-{{ $universalBundle->getName() }}" data-module-name="{{ $universalBundle->getName() }}">
                            <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 dark:after:border-gray-500 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-blue-600 peer-checked:to-indigo-600 shadow-inner"></div>
                            <span class="ml-2 text-xs font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                                <span class="peer-checked:hidden">Off</span>
                                <span class="hidden peer-checked:inline">On</span>
                            </span>
                        </label>

                        <!-- Tooltip -->
                        <div x-show="tooltip"
                             x-cloak
                             class="absolute right-full mr-2 top-1/2 -translate-y-1/2 z-50 px-2.5 py-1.5 text-xs text-left text-white bg-gray-900 dark:bg-gray-700 rounded-md shadow-lg whitespace-normal min-w-[180px] max-w-[280px] break-words"
                             role="tooltip">
                            @lang('app.moduleSwitchMessage', ['name' => $universalBundle->getName()])
                        </div>
                    </div>
                @endif
                <!-- Right Side - Action Buttons -->
                @if ($fetchSetting)
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        @if (config(strtolower($universalBundle->getName()) . '.verification_required'))
                            @include('custom-modules.sections.purchase-code', ['module' => $universalBundle->getName()])
                        @endif

                        @if ($plugins->where('envato_id', config(strtolower($universalBundle->getName()) . '.envato_item_id'))->first())
                            @php $envatoId = config(strtolower($universalBundle->getName()) . '.envato_item_id'); @endphp
                            @include('custom-modules.sections.module-update', ['module' => $universalBundle, 'fetchSetting' => $fetchSetting])
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Content Section -->
        <div class="px-4 py-3">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <!-- Left Side - Badges and Info -->
                <div class="flex-1 space-y-2">
                    <!-- License & Status Badges -->
                    <div class="flex flex-wrap items-center gap-1.5">
                        @if ($fetchSetting?->license_type)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                <i class="fa fa-certificate mr-1.5 text-gray-500 dark:text-gray-400"></i>
                                {{ $fetchSetting->license_type }}
                            </span>
                            @if (str_contains($fetchSetting->license_type, 'Regular'))
                                <a href="{{ \Froiden\Envato\Helpers\FroidenApp::buyExtendedUrl(config(strtolower($universalBundle->getName()) . '.envato_item_id')) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-semibold bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-500 dark:to-indigo-500 text-white hover:from-blue-700 hover:to-indigo-700 dark:hover:from-blue-600 dark:hover:to-indigo-600 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fa fa-arrow-up text-xs"></i>
                                    <span>Upgrade to Extended</span>
                                </a>
                            @endif
                        @endif

                        @if (
                            $fetchSetting?->purchase_code &&
                            $fetchSetting?->supported_until &&
                            \Carbon\Carbon::parse($fetchSetting->supported_until)->isPast())
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800">
                                <i class="fa fa-exclamation-circle mr-1.5"></i>
                                Support Expired
                            </span>
                        @endif
                    </div>

                    <!-- Support Date Info -->
                    @if ($fetchSetting?->purchase_code && $fetchSetting?->supported_until)
                        <div class="flex items-center text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 rounded-md px-2.5 py-1.5 border border-gray-200 dark:border-gray-600">
                            <i class="fa fa-info-circle mr-1.5 text-blue-500 dark:text-blue-400 text-xs"></i>
                            <span>@include('custom-modules.sections.support-date')</span>
                        </div>
                    @endif
                </div>


            </div>
        </div>
    </div>

    @includeIf('universalbundle::install-modules')
@endif
