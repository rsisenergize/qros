<div
    class="flex flex-col h-auto min-h-screen px-2 py-4 pr-4 bg-white border-l lg:w-6/12 dark:border-gray-700 dark:bg-gray-800">
    <div>

        <div class="flex items-center justify-between mt-1 mb-2">
            <div class="inline-flex items-center gap-1 py-2 font-medium dark:text-neutral-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="w-6 h-6 bi bi-receipt" viewBox="0 0 16 16">
                    <path
                        d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27m.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0z" />
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
                        version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 44.999 44.999" xml:space="preserve">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <g>
                                <g>
                                    <path
                                        d="M42.558,23.378l2.406-10.92c0.18-0.816-0.336-1.624-1.152-1.803c-0.816-0.182-1.623,0.335-1.802,1.151l-2.145,9.733 h-9.647c-0.835,0-1.512,0.677-1.512,1.513c0,0.836,0.677,1.513,1.512,1.513h0.573l-3.258,7.713 c-0.325,0.771,0.034,1.657,0.805,1.982c0.19,0.081,0.392,0.12,0.588,0.12c0.59,0,1.15-0.348,1.394-0.925l2.974-7.038l4.717,0.001 l2.971,7.037c0.327,0.77,1.215,1.127,1.982,0.805c0.77-0.325,1.13-1.212,0.805-1.982l-3.257-7.713h0.573 C41.791,24.564,42.403,24.072,42.558,23.378z">
                                    </path>
                                    <path
                                        d="M14.208,24.564h0.573c0.835,0,1.512-0.677,1.512-1.513c0-0.836-0.677-1.513-1.512-1.513H5.134L2.99,11.806 C2.809,10.99,2,10.472,1.188,10.655c-0.815,0.179-1.332,0.987-1.152,1.803l2.406,10.92c0.153,0.693,0.767,1.187,1.477,1.187h0.573 L1.234,32.28c-0.325,0.77,0.035,1.655,0.805,1.98c0.768,0.324,1.656-0.036,1.982-0.805l2.971-7.037l4.717-0.001l2.972,7.038 c0.244,0.577,0.804,0.925,1.394,0.925c0.196,0,0.396-0.039,0.588-0.12c0.77-0.325,1.13-1.212,0.805-1.98L14.208,24.564z">
                                    </path>
                                    <path
                                        d="M24.862,31.353h-0.852V18.308h8.13c0.835,0,1.513-0.677,1.513-1.512s-0.678-1.513-1.513-1.513H12.856 c-0.835,0-1.513,0.678-1.513,1.513c0,0.834,0.678,1.512,1.513,1.512h8.13v13.045h-0.852c-0.835,0-1.512,0.679-1.512,1.514 s0.677,1.513,1.512,1.513h4.728c0.837,0,1.514-0.678,1.514-1.513S25.699,31.353,24.862,31.353z">
                                    </path>
                                </g>
                            </g>
                        </g>
                    </svg>
                    {{ $tableNo }}
                    @if (user_can('Update Order'))
                        <x-secondary-button wire:click="$toggle('showTableModal')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-gear" viewBox="0 0 16 16">
                                <path
                                    d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                                <path
                                    d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
                            </svg>
                        </x-secondary-button>
                    @endif
                @elseif(user_can('Update Order'))
                    <x-secondary-button wire:click="$toggle('showTableModal')">@lang('modules.order.setTable')</x-secondary-button>
                @endif
            </div>
            @endif
        </div>
        <div class="flex items-center justify-between gap-2">
            @if ($orderType == 'dine_in')
                <div class="inline-flex items-center gap-1 py-2 text-sm dark:text-gray-300">
                    @lang('modules.order.noOfPax') <x-input type="number" step='1' min='1' class="w-16 text-sm"
                        wire:model='noOfPax' />
                </div>

                @if (user_can('Update Order') && !auth()->user()->roles->pluck('display_name')->contains('Waiter'))
                    <div class="inline-flex items-center gap-2">
                        <x-select class="text-sm w-36 xl:w-fit" wire:model.live='selectWaiter'>
                            <option value="">@lang('modules.order.selectWaiter')</option>
                            @foreach ($users as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </x-select>
                    </div>
                @elseif($selectWaiter)
                    <div class="inline-flex items-center gap-1 py-2 text-sm dark:text-gray-300">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3m0 14.2a7.2 7.2 0 0 1-6-3.22c.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08a7.2 7.2 0 0 1-6 3.22" />
                        </svg>
                        <span>@lang('modules.order.waiter'):</span>
                        <span class="font-medium">{{ optional($users->firstWhere('id', $selectWaiter))->name }}</span>
                    </div>
                @endif
            @endif

            @if ($orderType == 'delivery' && user_can('Update Order'))
                <div class="flex items-center justify-between gap-2">
                    <div class="inline-flex items-center gap-2">
                        <svg class="w-6 h-6 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                            fill="currentColor" version="1.0" viewBox="0 0 512 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <g transform="translate(0 512) scale(.1 -.1)">
                                <path
                                    d="m2605 4790c-66-13-155-48-213-82-71-42-178-149-220-221-145-242-112-552 79-761 59-64 61-67 38-73-13-4-60-24-104-46-151-75-295-249-381-462-20-49-38-91-39-93-2-2-19 8-40 22s-54 30-74 36c-59 16-947 12-994-4-120-43-181-143-122-201 32-33 76-33 106 0 41 44 72 55 159 55h80v-135c0-131 1-137 25-160l24-25h231 231l24 25c24 23 25 29 25 161v136l95-4c82-3 97-6 117-26l23-23v-349-349l-46-46-930-6-29 30c-17 16-30 34-30 40 0 7 34 11 95 11 88 0 98 2 120 25 16 15 25 36 25 55s-9 40-25 55c-22 23-32 25-120 25h-95v80 80h55c67 0 105 29 105 80 0 19-9 40-25 55l-24 25h-231-231l-24-25c-33-32-33-78 0-110 22-23 32-25 120-25h95v-80-80h-175c-173 0-176 0-200-25-33-32-33-78 0-110 24-25 27-25 197-25h174l12-45c23-88 85-154 171-183 22-8 112-12 253-12h220l-37-43c-103-119-197-418-211-669-7-115-7-116 19-142 26-25 29-26 164-26h138l16-69c55-226 235-407 464-466 77-20 233-20 310 0 228 59 409 240 463 464l17 71h605 606l13-62c58-281 328-498 621-498 349 0 640 291 640 640 0 237-141 465-350 569-89 43-193 71-271 71h-46l-142 331c-78 183-140 333-139 335 2 1 28-4 58-12 80-21 117-18 145 11l25 24v351 351l-26 26c-24 24-30 25-91 20-130-12-265-105-317-217l-23-49-29 30c-16 17-51 43-79 57-49 26-54 27-208 24-186-3-227 9-300 87-43 46-137 173-137 185 0 3 10 6 23 6s48 12 78 28c61 31 112 91 131 155 7 25 25 53 45 70 79 68 91 152 34 242-17 27-36 65-41 85-13 46-13 100 0 100 6 0 22 11 35 25 30 29 33 82 10 190-61 290-332 508-630 504-38-1-88-5-110-9zm230-165c87-23 168-70 230-136 55-57 108-153 121-216l6-31-153-4c-131-3-161-6-201-25-66-30-133-96-165-162-26-52-28-66-31-210l-4-153-31 6c-63 13-159 66-216 121-66 62-113 143-136 230-88 339 241 668 580 580zm293-619c7-41 28-106 48-147l36-74-24-15c-43-28-68-59-68-85 0-40-26-92-54-110-30-20-127-16-211 8l-50 14-3 175c-2 166-1 176 21 218 35 67 86 90 202 90h91l12-74zm-538-496c132-25 214-88 348-269 101-137 165-199 241-237 31-15 57-29 59-30s-6-20-17-43c-12-22-27-75-33-117-12-74-12-76-38-71-149 30-321 156-424 311-53 80-90 95-140 55-48-38-35-89 52-204l30-39-28-36c-42-54-91-145-110-208l-18-57-337-3-338-2 6 82c9 112 47 272 95 400 135 357 365 522 652 468zm1490-630c0-254 1-252-83-167-54 53-77 104-77 167s23 114 77 168c84 84 83 86 83-168zm-454 63c18-13 41-46 57-83l26-61-45-19c-75-33-165-52-244-54l-75-1-3 29c-8 72 44 166 113 201 42 22 132 16 171-12zm-2346-63v-80h-120-120v80 80h120 120v-80zm1584-184c80-52 154-84 261-111l90-23 112-483c68-295 112-506 112-540 1-68-21-134-56-171l-26-27-17 48c-29 86-99 159-177 186l-38 13-6 279c-5 297-5 297-64 414-58 113-212 233-328 254-21 4-41 14-44 21-12 32 88 201 111 186 6-4 37-24 70-46zm1099-493 185-433-348-490h-138-138l33 68c40 81 56 176 44 252-8 47-203 894-217 941-4 13 9 17 75 23 80 6 230 44 280 71 14 7 29 10 32 7 4-4 90-202 192-439zm-1323 187c118-22 229-99 275-190 37-74 45-138 45-375v-225h-160-160v115c0 179-47 289-158 369-91 67-141 76-417 76h-244l10 32c5 18 9 72 9 120v88h374c209 0 397-4 426-10zm-319-402c50-15 111-67 135-115 16-32 20-70 24-244l5-205 36-72 35-72h-759-759l7 63c17 164 95 400 165 502 47 68 129 124 215 145 52 13 853 12 896-2zm2114-323c256-67 415-329 350-580-48-184-202-326-390-358-197-34-412 76-500 257-19 39-38 86-41 104l-6 32h80 81l24-53c31-69 86-123 156-156 77-36 192-36 266-1 63 31 124 91 156 155 33 68 34 197 2 267-27 60-95 127-156 157-95 46-229 36-311-22-18-12-26-15-21-6 13 22 126 182 143 202 19 22 86 23 167 2zm-1315-243c39-21 87-99 77-125-6-15-27-17-178-17-193 0-231 7-289 58-35 29-70 78-70 97 0 3 96 5 213 5 187 0 217-2 247-18zm1288-89c51-38 67-70 67-133s-16-95-69-134c-43-33-132-29-179 7-20 15-37 32-37 38 0 5 36 9 80 9 73 0 83 3 105 25 33 32 33 78 0 110-22 22-32 25-105 25-44 0-80 4-80 8 0 12 29 37 65 57 39 21 117 15 153-12zm-397-46c-10-9-11-8-5 6 3 10 9 15 12 12s0-11-7-18zm-2460-217c45-106 169-184 289-184s244 78 289 184l22 50h81 81l-7-32c-13-65-66-159-123-219-186-195-500-195-686 0-57 60-110 154-123 219l-6 32h80 81l22-50zm419 41c0-16-51-50-91-63-30-8-48-8-78 0-40 13-91 47-91 63 0 5 57 9 130 9s130-4 130-9z" />
                            </g>
                        </svg>

                        <x-select class="w-full text-sm" wire:model.live='selectDeliveryExecutive'
                            wire:change='saveDeliveryExecutive'>
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

        @if ($orderStatus->value === 'cancelled')
            <span class="inline-block px-2 py-1 my-2 text-xs font-medium text-red-800 bg-red-100 rounded-full">
                @lang('modules.order.info_cancelled')
            </span>
        @else
            <div class="p-4 mb-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                @php
                    $statuses = match ($orderType) {
                        'delivery' => ['placed', 'confirmed', 'preparing', 'out_for_delivery', 'delivered'],
                        'pickup' => ['placed', 'confirmed', 'preparing', 'ready_for_pickup', 'delivered'],
                        default => ['placed', 'confirmed', 'preparing', 'served'],
                    };

                    $currentIndex = array_search($orderStatus->value, $statuses);
                    $currentIndex = $currentIndex !== false ? $currentIndex : 0;
                    $nextIndex = min($currentIndex + 1, count($statuses) - 1);
                @endphp

                <div class="flex flex-col space-y-4">
                    <div class="flex items-center justify-between text-gray-900 dark:text-white">
                        <h3 class="text-lg font-semibold">
                            {{ __('modules.order.orderStatus') }}
                        </h3>
                        <span class="px-3 py-1 text-sm font-medium rounded-full" @class([
                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' =>
                                $orderStatus->value === 'delivered' || $orderStatus->value === 'served',
                            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' =>
                                $orderStatus->value === 'placed',
                            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' =>
                                $orderStatus->value !== 'delivered' &&
                                $orderStatus->value !== 'served' &&
                                $orderStatus->value !== 'placed',
                        ])>
                            {{ __('modules.order.' . App\Enums\OrderStatus::from($orderStatus->value)->label()) }}
                        </span>
                    </div>

                    <div class="relative">
                        <div class="relative flex justify-between">
                            @foreach ($statuses as $index => $status)
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center mb-2
                                    @if ($index <= $currentIndex) bg-skin-base text-white
                                    @else
                                        bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 @endif">
                                        @switch($status)
                                            @case('placed')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            @break

                                            @case('confirmed')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @break

                                            @case('preparing')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 7.68 7.68" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M7.584 3.072 6.72 3.72v1.8a0.961 0.961 0 0 1 -0.96 0.96H1.92a0.961 0.961 0 0 1 -0.96 -0.96v-1.8L0.096 3.072a0.24 0.24 0 0 1 0.288 -0.384L0.96 3.12V2.64a0.481 0.481 0 0 1 0.48 -0.48h4.8a0.481 0.481 0 0 1 0.48 0.48v0.48l0.576 -0.432a0.24 0.24 0 0 1 0.288 0.384M4.8 1.68a0.24 0.24 0 0 0 0.24 -0.24V0.48a0.24 0.24 0 0 0 -0.48 0v0.96a0.24 0.24 0 0 0 0.24 0.24m-0.96 0a0.24 0.24 0 0 0 0.24 -0.24V0.48a0.24 0.24 0 0 0 -0.48 0v0.96a0.24 0.24 0 0 0 0.24 0.24m-0.96 0a0.24 0.24 0 0 0 0.24 -0.24V0.48a0.24 0.24 0 0 0 -0.48 0v0.96a0.24 0.24 0 0 0 0.24 0.24"
                                                        stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            @break

                                            @case('out_for_delivery')
                                            @case('ready_for_pickup')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                            @break

                                            @case('delivered')
                                            @case('served')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            @break
                                        @endswitch
                                    </div>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('modules.order.' . App\Enums\OrderStatus::from($status)->label()) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if (user_can('Update Order'))
                        <div class="flex justify-end items-center mt-4 space-x-2 rtl:!space-x-reverse">
                            @if ($orderStatus->value === 'placed')
                                <x-danger-button class="inline-flex items-center gap-2 dark:text-gray-200"
                                    wire:click="$toggle('confirmDeleteModal')">
                                    <span>{{ __('modules.order.cancelOrder') }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </x-danger-button>
                            @endif

                            @if ($currentIndex < count($statuses) - 1)
                                <x-secondary-button class="inline-flex items-center gap-2"
                                    wire:click="$set('orderStatus', '{{ $statuses[$nextIndex] }}')">
                                    <span>{{ __('modules.order.moveTo') }}
                                        {{ __('modules.order.' . App\Enums\OrderStatus::from($statuses[$nextIndex])->label()) }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </x-secondary-button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @foreach ($kotList as $kot)
            @php
                // Only show KOT if it has non-cancelled items in the orderItemList
                $hasItems = false;
                foreach ($orderItemList as $key => $item) {
                    if (strpos($key, 'kot_' . $kot->id) !== false) {
                        $hasItems = true;
                        break;
                    }
                }
            @endphp

            @if ($hasItems)
            <div class="flex justify-between p-2 text-xs font-medium text-gray-500 bg-gray-100 dark:bg-gray-700">
                <div>@lang('menu.kot') #{{ $kot->kot_number }}</div>

                <div>{{ $kot->created_at->timezone(timezone())->translatedFormat('d F, h:i A') }}</div>
            </div>

            <div class="flex flex-col rounded">
                <table class="flex-1 min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead>
                        <tr>
                            <th scope="col" class="p-3 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-left">
                                @lang('modules.menu.itemName')
                            </th>
                            <th scope="col" class="p-3 text-xs font-medium text-center text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.order.qty')
                            </th>
                            <th scope="col"
                                class="p-2 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.order.price')
                            </th>
                            <th scope="col"
                                class="p-2 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.order.amount')
                            </th>
                            @if (user_can('Delete Order'))
                            <th scope="col" class="p-3 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                @lang('app.action')
                            </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                        wire:key='menu-item-list-{{ microtime() }}'>

                        @forelse ($orderItemList as $key => $item)
                            @continue(!strpos($key, 'kot_' . $kot->id))

                        @php
                            $itemName = $item->item_name;
                            $itemVariation = (isset($orderItemVariation[$key]) ? $orderItemVariation[$key]->variation : '');
                            // Use display price (base price without tax for inclusive items)
                            $displayPrice = $this->getItemDisplayPrice($key);
                            // Total amount per line (what customer pays)
                            $totalAmount = $orderItemAmount[$key];
                        @endphp
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='menu-item-{{ $key . microtime() }}' wire:loading.class.delay='opacity-10'>
                            <td class="flex flex-col p-2 mr-12 lg:min-w-28">
                                <div class="inline-flex items-center text-xs text-gray-900 dark:text-white">
                                    {{ $itemName }}
                                </div>
                                <div class="inline-flex items-center text-xs text-gray-600 dark:text-white">
                                    {{  $itemVariation }}
                                </div>
                                @if (!empty($itemModifiersSelected[$key]))
                                <div class="text-xs text-gray-600 dark:text-white">
                                    @foreach ($itemModifiersSelected[$key] as $modifierOptionId)
                                            <div class="flex justify-between items-center px-1 py-0.5 mb-1 text-xs bg-gray-200 rounded-md border-l-2 border-blue-500 dark:bg-gray-900">
                                                <span class="text-gray-900 dark:text-white">{{ $this->modifierOptions[$modifierOptionId]->name }}</span>
                                                <span class="text-gray-600 dark:text-gray-300">{{ currency_format($this->modifierOptions[$modifierOptionId]->price , restaurant()->currency_id) }}</span>
                                            </div>
                                    @endforeach
                                </div>
                                @endif
                            </td>

                                <td class="p-2 text-xs font-medium text-right text-gray-700 whitespace-nowrap dark:text-white">

                                <div class="relative flex items-center max-w-[8rem] mx-auto" wire:key='orderItemQty-{{ $key }}-counter'>
                                    <button type="button" wire:click="subQty('{{ $key }}')" class="h-8 p-3 border border-gray-300 bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 rounded-s-md">
                                        <svg class="w-2 h-2 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16"/>
                                        </svg>
                                    </button>

                                    <input type="text" wire:model='orderItemQty.{{ $key }}' class="block py-2.5 w-full h-8 text-sm text-center text-gray-900 bg-white border-gray-300 min-w-10 border-x-0 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" value="1" readonly  />

                                    <button type="button" wire:click="addQty('{{ $key }}')"  class="h-8 p-3 border border-gray-300 bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 rounded-e-md">
                                        <svg class="w-2 h-2 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
                                        </svg>
                                    </button>
                                </div>

                            </td>

                            <td class="p-2 text-xs font-medium text-right text-gray-700 whitespace-nowrap dark:text-white">
                                {{ currency_format($displayPrice, restaurant()->currency_id) }}
                            </td>

                            <td class="p-2 text-xs font-medium text-right text-gray-900 whitespace-nowrap dark:text-white">
                                {{ currency_format($totalAmount, restaurant()->currency_id) }}
                            </td>
                            @if (user_can('Delete Order'))
                            <td class="p-2 text-right whitespace-nowrap">
                                <button class="p-2 text-gray-800 border rounded dark:text-gray-400 dark:border-gray-500 hover:bg-gray-200 dark:hover:bg-gray-900/20" wire:click="deleteCartItems('{{ $key }}')">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </td>
                            @endif
                        @empty
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="p-2 space-x-6" colspan="5">
                                    @lang('messages.noItemAdded')
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
            @endif
        @endforeach

    </div>

    <div>
        <div class="w-full h-auto p-4 mt-3 space-y-4 text-center rounded select-none bg-gray-50 dark:bg-gray-700">
            @if (count($orderItemList) > 0 && user_can('Update Order') && user_can('Add Discount on POS'))
                <div class="text-left">
                    <x-secondary-button wire:click="showAddDiscount">
                        <svg class="w-5 h-5 text-current me-1" width="24" height="24" viewBox="0 0 16 16"
                            xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                            <path d="m7.25 14.25-5.5-5.5 7-7h5.5v5.5z" />
                            <circle cx="11" cy="5" r=".5" fill="#000" />
                        </svg>
                        @lang('modules.order.addDiscount')
                    </x-secondary-button>
                </div>
            @endif

            <div class="flex justify-between text-sm text-gray-500 dark:text-neutral-400">
                <div>
                    @lang('modules.order.totalItem')
                </div>
                <div>
                    {{ count($orderItemList) }}
                </div>
            </div>
            <div class="flex justify-between text-sm text-gray-500 dark:text-neutral-400">
                <div>
                    @lang('modules.order.subTotal')
                </div>
                <div>
                    {{ currency_format($subTotal, restaurant()->currency_id) }}
                </div>
            </div>

            @if ($discountAmount)
                <div wire:key="discountAmount"
                    class="flex justify-between text-sm text-green-500 dark:text-green-400">

                    <div class="inline-flex items-center gap-x-1">@lang('modules.order.discount') @if ($discountType == 'percent')
                            ({{ $discountValue }}%)
                        @endif
                        <span class="text-red-500 cursor-pointer hover:scale-110 active:scale-100"
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

            @foreach ($extraCharges as $charge)
                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                    <div class="inline-flex items-center gap-x-1">{{ $charge->charge_name }}
                        @if ($charge->charge_type == 'percent')
                            ({{ $charge->charge_value }}%)
                        @endif
                        @if (user_can('Update Order'))
                            <span class="text-red-500 cursor-pointer hover:scale-110 active:scale-100"
                                wire:click="removeExtraCharge('{{ $charge->id }}', '{{ $orderType }}')">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </div>
                    <div>
                        {{ currency_format($charge->getAmount($subTotal - ($discountAmount ?? 0)), restaurant()->currency_id) }}
                    </div>
                </div>
            @endforeach

            @if ($tipAmount > 0)
                <div class="flex justify-between text-sm text-gray-500 dark:text-neutral-400">
                    <div>
                        @lang('modules.order.tip')
                    </div>
                    <div>
                        {{ currency_format($tipAmount, restaurant()->currency_id) }}
                    </div>
                </div>
            @endif

            @if ($orderType === 'delivery' && !is_null($deliveryFee))
                <div class="flex justify-between text-sm text-gray-500 dark:text-neutral-400">
                    <div>
                        @lang('modules.delivery.deliveryFee')
                    </div>
                    <div>
                        @if ($deliveryFee > 0)
                            {{ currency_format($deliveryFee, restaurant()->currency_id) }}
                        @else
                            <span class="font-medium text-green-500">@lang('modules.delivery.freeDelivery')</span>
                        @endif
                    </div>
                </div>
            @endif

            @if ($taxMode == 'order')
                @foreach ($taxes as $item)
                    <div class="flex justify-between text-sm text-gray-500 dark:text-neutral-400">
                        <div>
                            {{ $item->tax_name }} ({{ $item->tax_percent }}%)
                        </div>
                        <div>
                            {{ currency_format(($item->tax_percent / 100) * ($subTotal - ($discountAmount ?? 0)), restaurant()->currency_id) }}
                        </div>
                    </div>
                @endforeach
            @else
                @php
                    // Show item-wise tax breakdown above total tax
                    $taxTotals = [];
                    foreach ($orderItemTaxDetails as $item) {
                        $qty = $item['qty'] ?? 1;
                        if (!empty($item['tax_breakup'])) {
                            foreach ($item['tax_breakup'] as $taxName => $taxInfo) {
                                if (!isset($taxTotals[$taxName])) {
                                    $taxTotals[$taxName] = [
                                        'percent' => $taxInfo['percent'],
                                        'amount' => 0
                                    ];
                                }
                                $taxTotals[$taxName]['amount'] += $taxInfo['amount'] * $qty;
                            }
                        }
                    }
                @endphp
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
                    </div>
                    <div>
                        {{ currency_format($totalTaxAmount, restaurant()->currency_id) }}
                    </div>
                </div>
            @endif

            <div class="flex justify-between font-medium text-gray-900 dark:text-gray-100">
                <div>
                    @lang('modules.order.total')
                </div>
                <div>
                    {{ currency_format($total, restaurant()->currency_id) }}
                </div>
            </div>
        </div>

        <div class="w-full h-auto pt-3 pb-4 select-none">
            @if ($orderDetail->status == 'kot' && user_can('Update Order'))
                <div class="grid grid-cols-2 gap-4">
                    <button class="w-full p-2 text-white rounded bg-skin-base" wire:click="saveOrder('bill')">
                        @lang('modules.order.bill')
                    </button>

                    <button class="w-full p-2 text-white bg-green-500 rounded"
                        wire:click="saveOrder('bill', 'payment')">
                        @lang('modules.order.billAndPayment')
                    </button>
                    <button class="w-full p-2 text-white bg-blue-500 rounded" wire:click="saveOrder('bill', 'print')">
                        @lang('modules.order.createBillAndPrintReceipt')
                    </button>

                    <a href="{{ route('pos.kot', ['id' => $orderDetail->id]) }}"
                        class="w-full p-2 text-center bg-white border rounded text-skin-base border-skin-base">
                        @lang('modules.order.newKot')
                    </a>

                    @if (user_can('Delete Order'))
                    <button class="w-full p-2 text-white bg-red-500 rounded" wire:click="$set('deleteOrderModal', true)">
                        @lang('modules.order.deleteOrder')
                    </button>
                    @endif
                </div>
            @endif

            @if ($orderDetail->status == 'billed' && user_can('Update Order'))
                <div class="flex gap-2">
                    <button class="w-full p-2 text-white rounded bg-skin-base" wire:click="saveOrder('bill')">
                        @lang('modules.order.addPayment')
                    </button>
                </div>
            @endif

            @if ($orderType == 'delivery' && $orderDetail->delivery_address)
                <div class="flex flex-col gap-2 p-3 mt-3 rounded-lg bg-gray-50 dark:bg-gray-700">
                    @if ($orderDetail->customer)
                        <div class="flex gap-1.5 items-center text-gray-800 dark:text-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                            </svg>
                            <span class="text-gray-800 dark:text-gray-200">
                                {{ $orderDetail->customer->name }}
                            </span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex gap-1.5 items-center text-gray-800 dark:text-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                            </svg>
                            @lang('modules.customer.address')
                        </div>

                        @if ($orderDetail->customer_lat && $orderDetail->customer_lng && branch()->lat && branch()->lng)
                            <a href="https://www.google.com/maps/dir/?api=1&travelmode=two-wheeler&origin={{ branch()->lat }},{{ branch()->lng }}&destination={{ $orderDetail->customer_lat }},{{ $orderDetail->customer_lng }}"
                                target="_blank"
                                class="flex items-center gap-1 text-sm text-blue-500 transition-colors hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                <span>@lang('modules.order.viewOnMap')</span>
                                <svg width="24" height="24" class="w-4 h-4" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg" fill="none">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M10 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-4m-8-2 8-8m0 0v5m0-5h-5" />
                                </svg>
                            </a>
                        @endif
                    </div>

                    <div
                        class="p-2 text-sm text-gray-600 bg-white border border-gray-200 rounded dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600">
                        {!! nl2br(e($orderDetail->delivery_address)) !!}
                    </div>
                </div>
            @endif



        </div>
    </div>


    <x-confirmation-modal wire:model="confirmDeleteModal">
        <x-slot name="title">
            <div class="flex items-center gap-4">

                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">@lang('modules.order.cancelOrder')</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">This action cannot be undone</p>
                </div>
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">
                <!-- Warning Message -->
                <div class="p-4 border bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800 rounded-xl">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">@lang('modules.order.cancelOrderMessage')</p>
                            <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">Please select a reason for cancellation</p>
                        </div>
                    </div>
                </div>

                <!-- Reason Selection -->
                    <div>
                    <x-label for="cancelReason" value="{{ __('modules.settings.selectCancelReason') }}" class="text-sm font-medium text-gray-700 dark:text-gray-200" />
                    <x-select id="cancelReason" class="block w-full mt-2" wire:model="cancelReason">
                        <option value="">{{ __('modules.settings.selectCancelReason') }}</option>
                        @foreach ($cancelReasons as $reason)
                            <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="cancelReason" class="mt-2" />
                </div>

                <!-- Custom Reason Textarea -->
                <textarea
                            wire:model.defer="cancelReasonText"
                            id="cancelReasonText"
                            rows="4"
                            class="block w-full px-4 py-3 transition-all duration-200 border-2 border-gray-300 shadow-sm resize-none dark:border-gray-600 rounded-xl focus:ring-2 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="@lang('modules.settings.enterCancelReason')"
                        ></textarea>
            </div>
        </x-slot>

        <x-slot name="footer">
        <x-secondary-button wire:click="$set('confirmDeleteModal', false)" wire:loading.attr="disabled">
            {{ __('app.cancel') }}
        </x-secondary-button>

        <x-danger-button class="ml-3" wire:click="cancelOrder" wire:loading.attr="disabled">
            @lang('modules.order.cancelOrder')
        </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal wire:model="deleteOrderModal">
        <x-slot name="title">
            @lang('modules.order.deleteOrder')?
        </x-slot>

        <x-slot name="content">
            @lang('modules.order.deleteOrderMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('deleteOrderModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="saveOrder('cancel')"
                wire:loading.attr="disabled">
                @lang('modules.order.deleteOrder')
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

</div>
