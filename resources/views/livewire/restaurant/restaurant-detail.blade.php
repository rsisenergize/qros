<div>
    <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                @lang('modules.restaurant.restaurantDetails')</h1>

        </div>
    </div>
    <div class="px-4 pt-6 xl:gap-4 dark:bg-gray-900">

        <div class="grid lg:grid-cols-2 lg:gap-6 mb-4">
            <div
                class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="items-center flex  gap-x-4">
                    <img class="mb-4 rounded-lg w-20 sm:mb-0 " src="{{ $restaurant->logoUrl }}"
                        alt="{{ $restaurant->name }}">
                    <div>
                        <h3 class="mb-1 text-xl font-bold text-gray-900 dark:text-white">{{ $restaurant->name }}


                            <a href="{{ module_enabled('Subdomain') ? 'https://' . $restaurant->sub_domain : route('shop_restaurant', [$restaurant->hash]) }}" target="_blank" class="inline-flex justify-center items-center gap-1 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">

                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5"/>
                                    <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z"/>
                                </svg>
                            </a>
                            <x-secondary-button class="lg:ms-3 inline-flex items-center gap-1 group relative" wire:click="impersonate({{$restaurant->id}})" wire:loading.attr="disabled">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="m4.736 1.968-.892 3.269-.014.058C2.113 5.568 1 6.006 1 6.5 1 7.328 4.134 8 8 8s7-.672 7-1.5c0-.494-1.113-.932-2.83-1.205l-.014-.058-.892-3.27c-.146-.533-.698-.849-1.239-.734C9.411 1.363 8.62 1.5 8 1.5s-1.411-.136-2.025-.267c-.541-.115-1.093.2-1.239.735m.015 3.867a.25.25 0 0 1 .274-.224c.9.092 1.91.143 2.975.143a30 30 0 0 0 2.975-.143.25.25 0 0 1 .05.498c-.918.093-1.944.145-3.025.145s-2.107-.052-3.025-.145a.25.25 0 0 1-.224-.274M3.5 10h2a.5.5 0 0 1 .5.5v1a1.5 1.5 0 0 1-3 0v-1a.5.5 0 0 1 .5-.5m-1.5.5q.001-.264.085-.5H2a.5.5 0 0 1 0-1h3.5a1.5 1.5 0 0 1 1.488 1.312 3.5 3.5 0 0 1 2.024 0A1.5 1.5 0 0 1 10.5 9H14a.5.5 0 0 1 0 1h-.085q.084.236.085.5v1a2.5 2.5 0 0 1-5 0v-.14l-.21-.07a2.5 2.5 0 0 0-1.58 0l-.21.07v.14a2.5 2.5 0 0 1-5 0zm8.5-.5h2a.5.5 0 0 1 .5.5v1a1.5 1.5 0 0 1-3 0v-1a.5.5 0 0 1 .5-.5"/>
                                </svg>
                                {{ __('app.impersonate') }}
                                <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-96 rounded bg-gray-900 px-2 py-1 text-sm text-white opacity-0 group-hover:opacity-100 mb-8">
                                    {{ __('app.impersonateTooltip') }}
                                </span>
                            </x-secondary-button>
                        </h3>
                        @if(module_enabled('Subdomain'))
                            <div class="mb-2">
                                <a href="https://{{ $restaurant->sub_domain }}" target="_blank" class="underline flex items-center gap-1 underline-offset-1 font-normal dark:text-white">https://{{ $restaurant->sub_domain }}
                                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5"/>
                                        <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {!! nl2br(e($restaurant->address)) !!}
                        </div>

                    </div>

                </div>

            </div>

            <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">@lang('modules.restaurant.currentPackage')</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-semibold">@lang('modules.package.packageName'):</span> {{ $restaurant->package?->package_name ?? __('messages.noPackageFound') }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1"><span class="font-semibold">@lang('modules.package.packageType'):</span> {{ ucfirst($restaurant?->package_type) }}({{ ucfirst($restaurant->package?->package_type->value) }})</p>
                        @if ($restaurant->package?->package_type->value == 'trial')
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                            <span class="font-semibold">@lang('modules.package.trialExpireOn'):</span> {{ $restaurant?->trial_ends_at ? \Carbon\Carbon::parse($restaurant->trial_ends_at)->format('D, d M Y') : '--' }}
                        </p>
                        @elseif ($restaurant->package?->package_type->value != 'lifetime')
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                            <span class="font-semibold">@lang('modules.package.licenceExpiresOn'):</span> {{ optional($restaurant->license_expire_on)->format('D, d M Y') ?? '--' }}
                        </p>
                        @endif
                    </div>
                    <!--for future use -->
                    {{-- <x-button >
                        Manage button
                    </x-button> --}}
                </div>
            </div>

        </div>

        <div class="grid lg:grid-cols-2 lg:gap-6 mb-4">
            <div
            class="p-4  bg-white border border-gray-200 rounded-lg shadow-sm  dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <!-- List -->
                <div class="space-y-3">
                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('app.id')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li
                                    class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    {{ $restaurant->id }}
                                </li>
                            </ul>
                        </dd>
                    </dl>
                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('app.status')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    @if ($restaurant->is_active == true)
                                        <span class="bg-green-100 uppercase text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">@lang('app.active')</span>
                                    @else
                                        <span class="bg-red-100 uppercase text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">@lang('app.inactive')</span>
                                    @endif
                                </li>
                            </ul>
                        </dd>
                    </dl>
                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.restaurant.phone')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li
                                    class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    {{ $restaurant->phone_code ? '+' . $restaurant->phone_code . ' ' . $restaurant->phone_number : $restaurant->phone_number }}
                                </li>
                            </ul>
                        </dd>
                    </dl>

                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.restaurant.email')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li
                                    class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    {{ $restaurant->email }}
                                </li>
                            </ul>
                        </dd>
                    </dl>

                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.restaurantTimezone')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li
                                    class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    {{ $restaurant->timezone }}
                                </li>
                            </ul>
                        </dd>
                    </dl>

                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.restaurantCountry')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li
                                    class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    <img class="h-3.5 w-3.5 rounded-full mr-2"
                                    src="{{ $restaurant->country->flagUrl }}" alt="">
                                    {{ $restaurant->country->countries_name }}
                                </li>
                            </ul>
                        </dd>
                    </dl>

                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.restaurantCurrency')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li
                                    class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    {{ $restaurant?->currency?->currency_name }} ({{ $restaurant?->currency?->currency_code }})
                                </li>
                            </ul>
                        </dd>
                    </dl>
                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('app.dateTime')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li
                                    class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    {{ $restaurant->created_at->timezone(global_setting()->timezone ?? 'Asia/Kolkata')->translatedFormat('D, d M Y, h:i A') }}
                                </li>
                            </ul>
                        </dd>
                    </dl>

                </div>
                <!-- End List -->
            </div>

            @if ($restaurantAdmin)
                <div class="w-full bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 flex  flex-col items-center justify-center">
                    <h5 class="mb-6  text-lg font-medium text-gray-900 dark:text-white">@lang('modules.restaurant.firstAdmin')</h5>

                    <div class="flex flex-col items-center">
                        <img class="w-24 h-24 mb-3 rounded-full" src="{{ $restaurantAdmin->profile_photo_url }}" alt="Bonnie image"/>
                        <h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">{{ $restaurantAdmin->name }}</h5>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $restaurantAdmin->email }}</span>
                        <div class="flex mt-4 md:mt-6">
                            <x-button wire:click="$set('showPasswordModal', true)">@lang('modules.restaurant.changePassword')</x-button>
                        </div>
                    </div>
                </div>
            @else
                <div class="w-full bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 flex  flex-col items-center justify-center">
                    <h5 class="mb-6  text-lg font-medium text-gray-900 dark:text-white">@lang('messages.noAdminFound')</h5>
                </div>
            @endif

        </div>

        @php
            $restaurantHasSms = false;
            if ($restaurant->package) {
                $packageModules = $restaurant->package->modules->pluck('name')->toArray();
                $additionalFeatures = json_decode($restaurant->package->additional_features ?? '[]', true);
                $allModules = array_unique(array_merge($packageModules, $additionalFeatures));
                $restaurantHasSms = in_array('Sms', $allModules);
            }
        @endphp

        @if(module_enabled('Sms') && $restaurantHasSms)
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold dark:text-white">@lang('sms::modules.menu.smsSettings')</h3>
                    @if($packageSmsCount != -1)
                    <x-button wire:click="$set('showSmsTopupModal', true)" class="inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        @lang('sms::modules.sms.addSmsTopup')
                    </x-button>
                    @endif
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <!-- Widget 1: Package Limit with Status Badge -->
                    <div class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                        <div class="w-full">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">@lang('sms::modules.package.packageLimit')</h3>
                                @if($packageSmsCount == -1)
                                    <span class="bg-green-100 uppercase text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                        @lang('sms::modules.package.unlimited')
                                    </span>
                                @else
                                    @if($isSmsLimitReached)
                                        <span class="bg-red-100 uppercase text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                            @lang('sms::modules.package.exhausted')
                                        </span>
                                    @else
                                        <span class="bg-blue-100 uppercase text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                            @lang('sms::modules.package.active')
                                        </span>
                                    @endif
                                @endif
                            </div>
                            @if($packageSmsCount == -1)
                                <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">∞</span>
                                <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center mr-1.5 text-sm text-gray-500 dark:text-gray-400">
                                        @lang('sms::modules.package.unlimitedMessagesAllowed')
                                    </span>
                                </p>
                            @else
                                <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $packageSmsCount }}</span>
                                <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center mr-1.5 text-sm text-gray-500 dark:text-gray-400">
                                        @lang('sms::modules.package.totalSmsInPackage')
                                    </span>
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Widget 2: Used SMS Count -->
                    <div class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                        <div class="w-full">
                            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">@lang('sms::modules.package.usedSmsCount')</h3>
                            <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $usedSmsCount }}</span>
                            <p class="flex items-center text-base font-normal text-gray-500 dark:text-gray-400">
                                @if($packageSmsCount != -1)
                                    @php
                                        $usagePercent = $packageSmsCount > 0 ? round(($usedSmsCount / $packageSmsCount) * 100, 1) : 0;
                                    @endphp
                                    <span class="flex items-center mr-1.5 text-sm {{ $usagePercent > 80 ? 'text-red-500 dark:text-red-400' : 'text-blue-500 dark:text-blue-400' }}">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            @if($usagePercent > 80)
                                                <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z"></path>
                                            @else
                                                <path clip-rule="evenodd" fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z"></path>
                                            @endif
                                        </svg>
                                        {{ $usagePercent }}%
                                    </span>
                                    @lang('sms::modules.package.ofPackageUsed')
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <div class="flex flex-col mb-12">
        <h3 class="px-4 mb-4 text-xl font-semibold dark:text-white">@lang('modules.settings.branches')</h3>
        <div class="overflow-x-auto ">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    #
                                </th>
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.branchName')
                                </th>

                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.branchAddress')
                                </th>

                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.order.totalOrder')
                                </th>

                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>

                            @foreach ($restaurant->branches as $item)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='member-{{ $item->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $loop->index+1 }}
                                </td>

                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item->name }}
                                </td>

                                <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item->address }}
                                </td>

                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item->orders_count }}
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
{{--    PAYMENTS--}}
    <div class="flex flex-col mb-12">
        <h3 class="px-4 mb-4 text-xl font-semibold dark:text-white">@lang('menu.payments')</h3>

        <livewire:billing.invoice-table :restaurantId='$restaurant->id' :search='$search' key='payment-table-{{ microtime() }}' />

    </div>

   <x-dialog-modal wire:model.live="showPasswordModal">
            <x-slot name="title">
                @lang('modules.restaurant.changePassword')
            </x-slot>

            <x-slot name="content">
                <form wire:submit="submitForm">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <x-label for="password" value="{{ __('Password') }}"/>
                            <x-input id="password" class="block mt-1 w-full" type="password" autocomplete="new-password"
                                    wire:model='password'/>
                            <x-input-error for="password" class="mt-2"/>
                        </div>
                        <x-button>@lang('app.save')</x-button>
                    </div>
                </form>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('showPasswordModal')" wire:loading.attr="disabled">@lang('app.cancel')</x-secondary-button>
            </x-slot>
    </x-dialog-modal>

    <!-- SMS Top-up Modal -->
    <x-dialog-modal wire:model.live="showSmsTopupModal">
        <x-slot name="title">
            @lang('sms::modules.sms.addSmsTopup')
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="smsTopupAmount" value="{{ __('sms::modules.package.smsCount') }}" />
                    <x-input id="smsTopupAmount" class="block mt-1 w-full" type="number" min="1"
                        wire:model='smsTopupAmount' placeholder="{{ __('sms::modules.sms.enterSmsCount') }}" />
                    <x-input-error for="smsTopupAmount" class="mt-2"/>
                </div>

                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm text-blue-800 dark:text-blue-300">
                                @lang('sms::modules.sms.currentSmsBalance'): <span class="font-semibold">{{ $packageSmsCount == -1 ? '∞ (Unlimited)' : $packageSmsCount }}</span>
                            </p>
                            <p class="text-sm text-blue-800 dark:text-blue-300 mt-1">
                                @lang('sms::modules.sms.usedSms'): <span class="font-semibold">{{ $usedSmsCount }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('showSmsTopupModal')" wire:loading.attr="disabled">
                @lang('app.cancel')
            </x-secondary-button>

            <x-button class="ml-3" wire:click="addSmsTopup" wire:loading.attr="disabled">
                @lang('sms::modules.sms.addTopup')
            </x-button>
        </x-slot>
    </x-dialog-modal>

</div>
