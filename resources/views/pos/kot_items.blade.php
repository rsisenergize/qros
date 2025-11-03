<div
    class="lg:w-6/12 flex flex-col bg-white border-l dark:border-gray-700 min-h-screen h-auto pr-4 px-2 py-4 dark:bg-gray-800">

    {{-- Order Type Indicator --}}
    @if($orderTypeId)
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 pb-2 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500 dark:text-gray-400">@lang('modules.settings.orderType'):</span>
            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ \App\Models\OrderType::find($orderTypeId)?->order_type_name ?? ucfirst($orderType) }}
            </span>

            @if($orderTypeSlug === 'delivery' && $selectedDeliveryApp)
                <span class="text-xs text-gray-500 dark:text-gray-400 mx-2">•</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Platform:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">
                    @if($selectedDeliveryApp === 'default')
                        Default
                    @else
                        {{ \App\Models\DeliveryPlatform::find($selectedDeliveryApp)?->name ?? 'Unknown' }}
                    @endif
                </span>
            @endif
        </div>

        <button type="button" wire:click="changeOrderType" class="text-xs bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-3 py-2 rounded-full transition-all">
            Change
        </button>
    </div>
    @endif

    <div>
        <div class="mt-2">
            @if($customerId)
                <div class="flex items-center gap-2">
                    <div class="font-semibold text-gray-700 dark:text-gray-300">{{ $customer->name }}</div>
                    @if(user_can('Update Order'))
                        <button  wire:click="$dispatch('showAddCustomerModal', { customerId: {{ $customerId }} })" title="{{__('modules.order.updateCustomerDetails')}}" class="p-1 text-gray-500 transition-colors bg-gray-100 rounded-md hover:text-gray-700 hover:bg-gray-200 rtl:ml-2 ltr:mr-2 dark:text-gray-300 dark:bg-gray-600 dark:hover:text-gray-200 dark:hover:bg-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                            </svg>
                        </button>
                    @endif
                </div>

            @else
                <a href="javascript:;"
                    wire:click="$dispatch('showAddCustomerModal')"
                    class="text-sm underline underline-offset-2 dark:text-gray-300">&plus; @lang('modules.order.addCustomerDetails')</a>
            @endif
        </div>

        <div class="flex justify-between my-2 items-center">
            <div class="font-medium py-2 inline-flex items-center gap-1  dark:text-neutral-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-receipt w-6 h-6" viewBox="0 0 16 16">
                    <path
                        d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27m.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0z" />
                    <path
                        d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5" />
                </svg>

                @if(!isOrderPrefixEnabled())
                    @lang('modules.order.orderNumber') #{{ $orderNumber }}
                @else
                    {{ $formattedOrderNumber }}
                @endif
            </div>


            @if ($orderType == 'dine_in')
                <div class="inline-flex items-center gap-2 dark:text-gray-300">
                    @if (!is_null($tableNo))
                        <svg fill="currentColor"
                            class="w-5 h-5 transition duration-75 group-hover:text-gray-900 dark:text-gray-200 dark:group-hover:text-white"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44.999 44.999" xml:space="preserve">
                            <g stroke-width="0" />
                            <g stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="m42.558 23.378 2.406-10.92a1.512 1.512 0 0 0-2.954-.652l-2.145 9.733h-9.647a1.512 1.512 0 0 0 0 3.026h.573l-3.258 7.713a1.51 1.51 0 0 0 1.393 2.102c.59 0 1.15-.348 1.394-.925l2.974-7.038 4.717.001 2.971 7.037a1.512 1.512 0 1 0 2.787-1.177l-3.257-7.713h.573a1.51 1.51 0 0 0 1.473-1.187m-28.35 1.186h.573a1.512 1.512 0 0 0 0-3.026H5.134L2.99 11.806a1.511 1.511 0 1 0-2.954.652l2.406 10.92a1.51 1.51 0 0 0 1.477 1.187h.573L1.234 32.28a1.51 1.51 0 0 0 .805 1.98 1.515 1.515 0 0 0 1.982-.805l2.971-7.037 4.717-.001 2.972 7.038a1.514 1.514 0 0 0 1.982.805 1.51 1.51 0 0 0 .805-1.98z" />
                            <path
                                d="M24.862 31.353h-.852V18.308h8.13a1.513 1.513 0 1 0 0-3.025H12.856a1.514 1.514 0 0 0 0 3.025h8.13v13.045h-.852a1.514 1.514 0 0 0 0 3.027h4.728a1.513 1.513 0 1 0 0-3.027" />
                        </svg>
                        {{ $tableNo }}

                        <x-secondary-button wire:click="$toggle('showTableModal'); $dispatch('refreshSetTableComponent')" >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-gear" viewBox="0 0 16 16">
                                <path
                                    d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                                <path
                                    d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
                            </svg>
                        </x-secondary-button>
                    @else
                        <x-secondary-button
                            wire:click="$toggle('showTableModal'); $dispatch('refreshSetTableComponent')" >@lang('modules.order.setTable')</x-secondary-button>
                    @endif
                </div>

            @endif

        </div>

        <div class="flex justify-between items-center gap-2">

            @if ($orderType == 'dine_in')
                <div class="py-2 inline-flex items-center gap-1 text-sm dark:text-gray-300">
                    @lang('modules.order.noOfPax') <x-input type="number" step='1' min='1' class="w-16 text-sm"
                        wire:model.defer='noOfPax' />
                </div>

                <div class="gap-2 inline-flex items-center">
                    <x-secondary-button class="relative" wire:click="$toggle('showKotNote')" :title="__('modules.order.addNote')"
                        data-tooltip-target="tooltip-note">
                        @if ($this->orderNote)
                            <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" fill="currentColor"
                                class="absolute bi bi-circle-fill top-1 right-1 text-skin-base" viewBox="0 0 16 16">
                                <circle cx="8" cy="8" r="8" />
                            </svg>
                        @endif
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path
                                d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                            <path fill-rule="evenodd"
                                d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                        </svg>
                    </x-secondary-button>

                    <div id="tooltip-note" role="tooltip"
                        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                        @lang('modules.order.addNote')
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>

                    <div class="inline-flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-700 dark:text-gray-200 hidden lg:block" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" />
                        </svg>
                        <span class="text-sm text-gray-600 dark:text-gray-300">@lang('modules.order.waiter'):</span>
                        @if (auth()->user()->roles->pluck('display_name')->contains('Waiter'))
                            <span
                                class="text-sm w-36 px-2 py-1 border border-gray-300 rounded-md bg-gray-100 dark:text-gray-200 dark:bg-gray-600 dark:border-gray-700 truncate"
                                title="{{ $users->where('id', $selectWaiter)->first()->name ?? __('modules.order.selectWaiter') }}">
                                {{ $users->where('id', $selectWaiter)->first()->name ?? __('modules.order.selectWaiter') }}
                            </span>
                        @else
                            <x-select class="text-sm w-36" wire:model.defer='selectWaiter'>
                                <option value="">@lang('modules.order.selectWaiter')</option>
                                @foreach ($users as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </x-select>
                        @endif
                    </div>

                </div>
            @endif

            @if ($orderType == 'delivery')
                <div class="gap-2 flex justify-between items-center mb-2">
                    <div class="inline-flex items-center gap-2">
                        <svg class="w-6 h-6 transition duration-75 text-gray-700 dark:text-gray-200 group-hover:text-gray-900 dark:group-hover:text-white"
                            fill="currentColor" version="1.0" viewBox="0 0 512 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <g transform="translate(0 512) scale(.1 -.1)">
                                <path
                                    d="m2605 4790c-66-13-155-48-213-82-71-42-178-149-220-221-145-242-112-552 79-761 59-64 61-67 38-73-13-4-60-24-104-46-151-75-295-249-381-462-20-49-38-91-39-93-2-2-19 8-40 22s-54 30-74 36c-59 16-947 12-994-4-120-43-181-143-122-201 32-33 76-33 106 0 41 44 72 55 159 55h80v-135c0-131 1-137 25-160l24-25h231 231l24 25c24 23 25 29 25 161v136l95-4c82-3 97-6 117-26l23-23v-349-349l-46-46-930-6-29 30c-17 16-30 34-30 40 0 7 34 11 95 11 88 0 98 2 120 25 16 15 25 36 25 55s-9 40-25 55c-22 23-32 25-120 25h-95v80 80h55c67 0 105 29 105 80 0 19-9 40-25 55l-24 25h-231-231l-24-25c-33-32-33-78 0-110 22-23 32-25 120-25h95v-80-80h-175c-173 0-176 0-200-25-33-32-33-78 0-110 24-25 27-25 197-25h174l12-45c23-88 85-154 171-183 22-8 112-12 253-12h220l-37-43c-103-119-197-418-211-669-7-115-7-116 19-142 26-25 29-26 164-26h138l16-69c55-226 235-407 464-466 77-20 233-20 310 0 228 59 409 240 463 464l17 71h605 606l13-62c58-281 328-498 621-498 349 0 640 291 640 640 0 237-141 465-350 569-89 43-193 71-271 71h-46l-142 331c-78 183-140 333-139 335 2 1 28-4 58-12 80-21 117-18 145 11l25 24v351 351l-26 26c-24 24-30 25-91 20-130-12-265-105-317-217l-23-49-29 30c-16 17-51 43-79 57-49 26-54 27-208 24-186-3-227 9-300 87-43 46-137 173-137 185 0 3 10 6 23 6s48 12 78 28c61 31 112 91 131 155 7 25 25 53 45 70 79 68 91 152 34 242-17 27-36 65-41 85-13 46-13 100 0 100 6 0 22 11 35 25 30 29 33 82 10 190-61 290-332 508-630 504-38-1-88-5-110-9zm230-165c87-23 168-70 230-136 55-57 108-153 121-216l6-31-153-4c-131-3-161-6-201-25-66-30-133-96-165-162-26-52-28-66-31-210l-4-153-31 6c-63 13-159 66-216 121-66 62-113 143-136 230-88 339 241 668 580 580zm293-619c7-41 28-106 48-147l36-74-24-15c-43-28-68-59-68-85 0-40-26-92-54-110-30-20-127-16-211 8l-50 14-3 175c-2 166-1 176 21 218 35 67 86 90 202 90h91l12-74zm-538-496c132-25 214-88 348-269 101-137 165-199 241-237 31-15 57-29 59-30s-6-20-17-43c-12-22-27-75-33-117-12-74-12-76-38-71-149 30-321 156-424 311-53 80-90 95-140 55-48-38-35-89 52-204l30-39-28-36c-42-54-91-145-110-208l-18-57-337-3-338-2 6 82c9 112 47 272 95 400 135 357 365 522 652 468zm1490-630c0-254 1-252-83-167-54 53-77 104-77 167s23 114 77 168c84 84 83 86 83-168zm-454 63c18-13 41-46 57-83l26-61-45-19c-75-33-165-52-244-54l-75-1-3 29c-8 72 44 166 113 201 42 22 132 16 171-12zm-2346-63v-80h-120-120v80 80h120 120v-80zm1584-184c80-52 154-84 261-111l90-23 112-483c68-295 112-506 112-540 1-68-21-134-56-171l-26-27-17 48c-29 86-99 159-177 186l-38 13-6 279c-5 297-5 297-64 414-58 113-212 233-328 254-21 4-41 14-44 21-12 32 88 201 111 186 6-4 37-24 70-46zm1099-493 185-433-348-490h-138-138l33 68c40 81 56 176 44 252-8 47-203 894-217 941-4 13 9 17 75 23 80 6 230 44 280 71 14 7 29 10 32 7 4-4 90-202 192-439zm-1323 187c118-22 229-99 275-190 37-74 45-138 45-375v-225h-160-160v115c0 179-47 289-158 369-91 67-141 76-417 76h-244l10 32c5 18 9 72 9 120v88h374c209 0 397-4 426-10zm-319-402c50-15 111-67 135-115 16-32 20-70 24-244l5-205 36-72 35-72h-759-759l7 63c17 164 95 400 165 502 47 68 129 124 215 145 52 13 853 12 896-2zm2114-323c256-67 415-329 350-580-48-184-202-326-390-358-197-34-412 76-500 257-19 39-38 86-41 104l-6 32h80 81l24-53c31-69 86-123 156-156 77-36 192-36 266-1 63 31 124 91 156 155 33 68 34 197 2 267-27 60-95 127-156 157-95 46-229 36-311-22-18-12-26-15-21-6 13 22 126 182 143 202 19 22 86 23 167 2zm-1315-243c39-21 87-99 77-125-6-15-27-17-178-17-193 0-231 7-289 58-35 29-70 78-70 97 0 3 96 5 213 5 187 0 217-2 247-18zm1288-89c51-38 67-70 67-133s-16-95-69-134c-43-33-132-29-179 7-20 15-37 32-37 38 0 5 36 9 80 9 73 0 83 3 105 25 33 32 33 78 0 110-22 22-32 25-105 25-44 0-80 4-80 8 0 12 29 37 65 57 39 21 117 15 153-12zm-397-46c-10-9-11-8-5 6 3 10 9 15 12 12s0-11-7-18zm-2460-217c45-106 169-184 289-184s244 78 289 184l22 50h81 81l-7-32c-13-65-66-159-123-219-186-195-500-195-686 0-57 60-110 154-123 219l-6 32h80 81l22-50zm419 41c0-16-51-50-91-63-30-8-48-8-78 0-40 13-91 47-91 63 0 5 57 9 130 9s130-4 130-9z" />
                            </g>
                        </svg>

                        <x-select class="text-sm w-full" wire:model.defer='selectDeliveryExecutive'>
                            <option value="">@lang('modules.order.selectDeliveryExecutive')</option>
                            @foreach ($deliveryExecutives as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </x-select>
                    </div>
                </div>
            @endif

            @if ($orderType == 'pickup')
                <div class="gap-2 flex justify-between items-center mb-2">
                    <div class="inline-flex items-center gap-2 w-full">
                        <label for="delivery_datetime" class="text-sm text-gray-600 dark:text-gray-300">
                            @lang('modules.order.pickUpDateTime'):
                        </label>
                        <input type="datetime-local" id="delivery_datetime"
                        class="px-3 py-2 text-sm text-gray-900 bg-gray-100 border border-gray-300 rounded-md dark:border-gray-700 dark:bg-gray-600 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        wire:model.live="deliveryDateTime" min="{{ $minDate }}" max="{{ $maxDate }}"
                        value="{{ $defaultDate }}" style="color-scheme: light dark;" />
                        <x-input-error for="pickupDateTime" class="mt-2" />

                    </div>
                </div>
            @endif

        </div>

        <div class="flex flex-col rounded">
            <table class=" flex-1  min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="p-2 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 rtl:text-right ltr:text-left">
                            @lang('modules.menu.itemName')
                        </th>
                        <th scope="col"
                            class="p-2 text-xs font-medium text-center text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.order.qty')
                        </th>
                        <th scope="col"
                            class="p-2 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400 hidden lg:table-cell">
                            @lang('modules.order.price')
                        </th>
                        <th scope="col"
                            class="p-2 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.order.amount')
                        </th>
                        <th scope="col"
                            class="p-2 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                            @lang('app.action')
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                    wire:key='menu-item-list-pos'>

                    @forelse ($orderItemList as $key => $item)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700"
                            wire:key='menu-item-{{ $key . microtime() }}' wire:loading.class='opacity-50'>
                            <td class="flex flex-col p-2 mr-12 lg:min-w-20">
                                <div
                                    class="text-xs text-gray-900 dark:text-white inline-flex items-center lg:table-cell">
                                    {{ $item->item_name }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-white inline-flex items-center">
                                    {{ isset($orderItemVariation[$key]) ? $orderItemVariation[$key]->variation : '' }}
                                </div>

                                @if (!empty($itemModifiersSelected[$key]))
                                    <div class="text-xs text-gray-600 dark:text-white">
                                        @foreach ($itemModifiersSelected[$key] as $modifierOptionId)
                                            <div
                                                class="flex items-center justify-between text-xs mb-1 py-0.5 px-1 border-l-2 border-blue-500 bg-gray-200 dark:bg-gray-900 rounded-md">
                                                <span
                                                    class="text-gray-900 dark:text-white">{{ $this->modifierOptions[$modifierOptionId]->name }}</span>
                                                <span
                                                    class="text-gray-600 dark:text-gray-300">{{ currency_format($this->modifierOptions[$modifierOptionId]->price, restaurant()->currency_id) }}</span>
                                            </div>
                                        @endforeach
                                        {{-- </div> --}}
                                @endif

                                <x-pos.item-note :id="$key" :note="$itemNotes[$key] ?? ''" />
                            </td>
                            <td class="p-2 text-base text-gray-900 whitespace-nowrap text-center">

                                <div class="relative flex items-center max-w-[8rem] mx-auto"
                                    wire:key='orderItemQty-{{ $key }}-counter'>
                                    <button type="button" wire:click="subQty('{{ $key }}')"
                                        wire:loading.attr="disabled" wire:loading.class="opacity-50"
                                        class="bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-md p-3 h-8 relative">
                                        <svg class="w-2 h-2 text-gray-900 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                        </svg>
                                        {{-- Loading spinner for subQty --}}
                                        <div wire:loading.flex wire:target="subQty('{{ $key }}')"
                                            class="absolute inset-0 items-center justify-center">
                                            <svg class="animate-spin h-3 w-3 text-skin-base"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                        </div>
                                    </button>

                                    <input type="text" wire:model.lazy="orderItemQty.{{ $key }}" wire:change="updateQty('{{ $key }}')"
                                        class="min-w-10 bg-white border-x-0 border-gray-300 h-8 text-center text-gray-900 text-sm block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                        min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />

                                    <button type="button" wire:click="addQty('{{ $key }}')"
                                        wire:loading.attr="disabled" wire:loading.class="opacity-50"
                                        class="bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-md p-3 h-8 relative">
                                        <svg class="w-2 h-2 text-gray-900 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                        </svg>
                                        {{-- Loading spinner for addQty --}}
                                        <div wire:loading.flex wire:target="addQty('{{ $key }}')"
                                            class="absolute inset-0 items-center justify-center">
                                            <svg class="animate-spin h-3 w-3 text-skin-base"
                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                        </div>
                                    </button>
                                </div>

                            </td>

                            @php
                                $displayPrice = $this->getItemDisplayPrice($key);
                                $totalAmount = $orderItemAmount[$key];
                            @endphp
                            <td
                                class="p-2 text-xs font-medium text-gray-700 whitespace-nowrap dark:text-white text-right hidden lg:table-cell">
                                {{ currency_format($displayPrice, restaurant()->currency_id) }}
                            </td>
                            <td
                                class="p-2 text-xs font-medium text-gray-900 whitespace-nowrap dark:text-white text-right">
                                {{ currency_format($totalAmount, restaurant()->currency_id) }}
                            </td>
                            <td class="p-2 whitespace-nowrap text-right">
                                <button
                                    class="rounded text-gray-800 dark:text-gray-400 border dark:border-gray-500 hover:bg-gray-200 dark:hover:bg-gray-900/20 p-2 relative"
                                    wire:click="deleteCartItems('{{ $key }}')" wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50">
                                    <svg class="w-4 h-4 text-gray-700 dark:text-gray-200" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{-- Loading spinner for delete --}}
                                    <div wire:loading.flex wire:target="deleteCartItems('{{ $key }}')"
                                        class="absolute inset-0 items-center justify-center">
                                        <svg class="animate-spin h-4 w-4 text-skin-base"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <!-- Replace the existing empty state with this improved version -->
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="p-8 text-center" colspan="5">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <svg class="w-12 h-12 text-gray-500 dark:text-gray-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <div class="text-gray-500 dark:text-gray-400 text-base">
                                        @lang('messages.noItemAdded')
                                    </div>

                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

    <div class="lg:min-w-20">
        <div class="h-auto p-4 mt-3 select-none text-center bg-gray-50 rounded space-y-4 dark:bg-gray-700">
            @if (count($orderItemList) > 0 && user_can('Add Discount on POS'))
                <div class="text-left">
                    <x-secondary-button wire:click="showAddDiscount">
                        <svg class="h-5 w-5 text-current me-1" width="24" height="24" viewBox="0 0 16 16"
                            xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                            <path d="m7.25 14.25-5.5-5.5 7-7h5.5v5.5z" />
                            <circle cx="11" cy="5" r=".5" fill="#000" />
                        </svg>
                        @lang('modules.order.addDiscount')
                    </x-secondary-button>
                </div>
            @endif

            <div class="flex justify-between text-gray-500 text-sm dark:text-neutral-400">
                <div>
                    @lang('modules.order.totalItem')
                </div>
                <div>
                    {{ count($orderItemList) }}
                </div>
            </div>
            <div class="flex justify-between text-gray-500 text-sm dark:text-neutral-400">
                <div>
                    @lang('modules.order.subTotal')
                </div>
                <div>
                    {{ currency_format($subTotal, restaurant()->currency_id) }}
                </div>
            </div>

            @if ($discountAmount)
                <div wire:key="discountAmount"
                    class="flex justify-between text-green-500 text-sm dark:text-green-400">
                    <div class="inline-flex items-center gap-x-1">@lang('modules.order.discount') @if ($discountType == 'percent')
                            ({{ $discountValue }}%)
                        @endif
                        <span class="text-red-500 hover:scale-110 active:scale-100 cursor-pointer"
                            wire:click="removeCurrentDiscount">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    </div>
                    <div>
                        -{{ currency_format($discountAmount, restaurant()->currency_id) }}
                    </div>
                </div>
            @endif

            @if ($orderType === 'delivery')
                <div class="flex justify-between items-center text-gray-500 text-sm dark:text-neutral-400">
                    <div>
                        @lang('modules.delivery.deliveryFee')
                        <span class="text-xs text-gray-400">
                            @if($deliveryFee == 0)
                                (@lang('modules.delivery.freeDelivery'))
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <x-input type="number" step='1' min='0' class="w-16 text-sm"
                            wire:model.live='deliveryFee' />
                        </div>
                    </div>
                </div>
            @endif

            @if (!$orderID && count($orderItemList) > 0 && $extraCharges)
                @foreach ($extraCharges as $charge)
                    <div wire:key="extraCharge-{{ $loop->index }}"
                        class="flex justify-between text-gray-500 text-sm dark:text-neutral-400">
                        <div class="inline-flex items-center gap-x-1">{{ $charge->charge_name }}
                            @if ($charge->charge_type == 'percent')
                                ({{ $charge->charge_value }}%)
                            @endif
                            <span class="text-red-500 hover:scale-110 active:scale-100 cursor-pointer"
                                wire:click="removeExtraCharge('{{ $charge->id }}', '{{ $orderType }}')">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        <div>
                            {{ currency_format($charge->getAmount($discountedTotal), restaurant()->currency_id) }}
                        </div>
                    </div>
                @endforeach
            @endif

            @if ($taxMode == 'order')
                @foreach ($taxes as $item)
                    <div class="flex justify-between text-gray-500 text-sm dark:text-neutral-400">
                        <div>
                            {{ $item->tax_name }} ({{ $item->tax_percent }}%)
                        </div>
                        <div>
                            {{ currency_format(($item->tax_percent / 100) * $discountedTotal, restaurant()->currency_id) }}
                        </div>
                    </div>
                @endforeach
            @else
                @php
                    // Show item-wise tax breakdown above total tax
                    $taxTotals = [];
                    $isInclusive = restaurant()->tax_inclusive ?? false;

                    foreach ($orderItemTaxDetails as $item) {
                        $qty = $item['qty'] ?? 1;
                        if (!empty($item['tax_breakup'])) {
                            foreach ($item['tax_breakup'] as $taxName => $taxInfo) {
                                if (!isset($taxTotals[$taxName])) {
                                    $taxTotals[$taxName] = [
                                        'percent' => $taxInfo['percent'],
                                        'amount' => 0,
                                    ];
                                }
                                $taxTotals[$taxName]['amount'] += $taxInfo['amount'] * $qty;
                            }
                        }
                    }
                @endphp
                @if (!empty($taxTotals))
                    @foreach ($taxTotals as $taxName => $taxInfo)
                        <div class="flex justify-between text-gray-500 text-xs dark:text-neutral-400">
                            <div>
                                {{ $taxName }} ({{ $taxInfo['percent'] }}%)
                            </div>
                            <div>
                                {{ currency_format($taxInfo['amount'], restaurant()->currency_id) }}
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-between text-gray-500 text-sm dark:text-neutral-400">
                        <div>
                            @lang('modules.order.totalTax')
                            @if ($isInclusive)
                                <span class="text-xs text-gray-400">(@lang('modules.settings.taxInclusive'))</span>
                            @else
                                <span class="text-xs text-gray-400">(@lang('modules.settings.taxExclusive'))</span>
                            @endif
                        </div>
                        <div>
                            {{ currency_format($totalTaxAmount, restaurant()->currency_id) }}
                        </div>
                    </div>
                @endif
            @endif

            <div class="flex justify-between font-medium dark:text-neutral-300">
                <div>
                    @lang('modules.order.total')
                </div>
                <div>
                    {{ currency_format($total, restaurant()->currency_id) }}
                </div>
            </div>
        </div>

        <div class="h-auto pb-4 pt-3 select-none text-center w-full mb-16 md:mb-0">
            @if (in_array('KOT', restaurant_modules()))
                <div class="flex gap-3">
                    <button class="rounded bg-gray-700 text-white w-full p-2 relative" wire:click="saveOrder('kot')"
                        wire:loading.attr="disabled" wire:loading.class="opacity-50">
                        <span wire:loading.remove wire:target="saveOrder('kot')">@lang('modules.order.kot')</span>
                        <span wire:loading wire:target="saveOrder('kot')">
                            <svg class="animate-spin -ml-1 mr-1 h-4 w-4 inline-flex text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            @lang('modules.order.kot')
                        </span>
                    </button>
                    <button class="rounded bg-gray-700 text-white w-full p-2 relative"
                        wire:click="saveOrder('kot', 'print')" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50">
                        <span wire:loading.remove wire:target="saveOrder('kot', 'print')">@lang('modules.order.kotAndPrint')</span>
                        <span wire:loading wire:target="saveOrder('kot', 'print')" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            @lang('modules.order.kotAndPrint')
                        </span>
                    </button>
                    <button class="rounded bg-gray-700 text-white w-full p-2 relative"
                        wire:click="saveOrder('kot','bill','payment', 'print')" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50">
                        <span wire:loading.remove
                            wire:target="saveOrder('kot','bill','payment', 'print')">@lang('modules.order.kotBillAndPayment')</span>
                        <span wire:loading wire:target="saveOrder('kot','bill','payment', 'print')" >
                            <svg class="animate-spin inline-flex -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            @lang('modules.order.kotBillAndPayment')
                        </span>
                    </button>
                </div>
            @endif
            @if (!$orderID)
                <div class="flex gap-3 mt-3">
                    <button class="rounded bg-skin-base text-white w-full p-2 relative" wire:click="saveOrder('bill')"
                        wire:loading.attr="disabled" wire:loading.class="opacity-50">
                        <span wire:loading.remove wire:target="saveOrder('bill')">@lang('modules.order.bill')</span>
                        <span wire:loading wire:target="saveOrder('bill')">
                            <svg class="animate-spin inline-flex items-center -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            @lang('modules.order.bill')
                        </span>
                    </button>
                    <button class="rounded bg-green-500 text-white w-full p-2 relative"
                        wire:click="saveOrder('bill', 'payment')" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50">
                        <span wire:loading.remove wire:target="saveOrder('bill', 'payment')">@lang('modules.order.billAndPayment')</span>
                        <span wire:loading wire:target="saveOrder('bill', 'payment')">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-flex items-center" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            @lang('modules.order.billAndPayment')
                        </span>
                    </button>
                    <button class="rounded bg-blue-500 text-white w-full p-2 relative"
                        wire:click="saveOrder('bill', 'print')" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50">
                        <span wire:loading.remove wire:target="saveOrder('bill', 'print')">@lang('modules.order.createBillAndPrintReceipt')</span>
                        <span wire:loading wire:target="saveOrder('bill', 'print')" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            @lang('modules.order.createBillAndPrintReceipt')
                        </span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Reservation Confirmation Modal -->
    <x-dialog-modal wire:model.live="showReservationModal" maxWidth="md">
        <x-slot name="title">
            <div class="flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                @lang('modules.order.reservationConfirmation')
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-blue-100" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                        @lang('modules.order.tableHasReservation')
                    </h3>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <p>@lang('modules.order.reservationFor'): <strong>{{ $this->reservationCustomer?->name ?? 'N/A' }}</strong></p>
                        <p>@lang('modules.order.reservationTime'): <strong>{{ $this->reservation?->reservation_date_time?->format('M d, Y g:i A') ?? 'N/A' }}</strong></p>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-sm text-gray-700 dark:text-gray-300 text-center">
                        @lang('modules.order.isThisSameCustomer')
                    </p>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-between w-full">
                <x-button-cancel wire:click="closeReservationModal" wire:loading.attr="disabled">
                    @lang('app.cancel')
                </x-button-cancel>
                <div class="flex gap-2">
                    <x-secondary-button wire:click="confirmDifferentCustomer" wire:loading.attr="disabled">
                        @lang('modules.order.differentCustomer')
                    </x-secondary-button>
                    <x-button wire:click="confirmSameCustomer" wire:loading.attr="disabled">
                        @lang('modules.order.sameCustomer')
                    </x-button>
                </div>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
