<div>

    @if ($showFilters)
        @include('expenses.filter')
    @endif
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.expenseTitle')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.category')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.amount')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.expenseDate')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.paymentStatus')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.paymentDate')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.paymentDueDate')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.expenses.paymentMethod')
                                </th>
                                <th
                                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.action')
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @if ($expenses->count() > 0)
                                @foreach ($expenses as $expense)
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <td class="py-2.5 px-2 text-gray-900 dark:text-white">
                                            <button wire:click="showExpenseDetails({{ $expense->id }})"
                                                wire:key="expense-detail-button-{{ $expense->id }}"
                                                class="inline-flex items-center justify-center w-6 h-6 text-xs font-medium text-gray-800 bg-gray-100 rounded-full hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </button>
                                        </td>

                                        <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                            {{ Str::title($expense->expense_title)}}</td>
                                        <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                            {{ optional($expense->category)->name ?? '--' }}</td>
                                        <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                            {{ currency_format($expense->amount, restaurant()->currency_id) }}</td>
                                        <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                            {{ $expense->expense_date->translatedFormat('d M, Y') }}
                                        </td>
                                        <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                            <span @class([
                                                'bg-green-100 text-green-800 rounded px-2 py-1 text-xs' =>
                                                    $expense->payment_status == 'paid',
                                                'bg-red-100 text-red-800 rounded px-2 py-1 text-xs' =>
                                                    $expense->payment_status == 'pending',
                                            ])>

                                                @lang('modules.expenses.status.' . $expense->payment_status)
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                            {{ $expense->payment_date ? $expense->payment_date->translatedFormat('d M, Y') : '--' }}
                                        </td>
                                        <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                            {{ $expense->payment_due_date ? $expense->payment_due_date->translatedFormat('d M, Y') : '--' }}
                                        </td>

                                        <td class="py-2.5 px-4 text-gray-900 dark:text-white">
                                            @if (!$expense->payment_method)
                                                <span
                                                    class="ml-2 inline-flex items-center px-2.5 py-1.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                    @lang('--')
                                                </span>
                                            @else
                                                @lang('modules.expenses.methods.' . $expense->payment_method)

                                                @if ($expense->payment_method == 'cash')
                                                    <span
                                                        class="ml-2 inline-flex items-center px-2.5 py-1.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                        <svg class="w-4 h-4" version="1.1" id="_x32_"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"
                                                            xml:space="preserve" fill="#000000">
                                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                stroke-linejoin="round"></g>
                                                            <g id="SVGRepo_iconCarrier">
                                                                <style type="text/css">
                                                                    .st0 {
                                                                        fill: #000000;
                                                                    }
                                                                </style>
                                                                <g>
                                                                    <path class="st0"
                                                                        d="M120,504.063h272v-432H120V504.063z M152,147.25c23.453-2.344,42.297-20.219,46.188-43.188h115.656 c3.875,22.969,22.703,40.844,46.156,43.188v92.938c-20.375-30.016-59.25-50.313-104-50.313c-44.734,0-83.625,20.281-104,50.297 V147.25z M289.266,273.453c-2.453-3.078-5.672-4.609-9.641-4.609c-3.594,0-6.313,1.078-8.234,3.281 c-1.922,2.188-3.172,5.891-3.688,11.094l-1.25,14.594c-0.969,10.844-3.844,19.031-8.641,24.578 c-4.781,5.578-11.781,8.328-20.953,8.328c-5.516,0-10.297-1.094-14.391-3.281c-4.156-2.188-7.578-5.234-10.391-9.047 c-2.828-3.859-4.906-8.438-6.297-13.766c-0.578-2.344-0.984-4.797-1.313-7.281h-15.563v-18.563h15.234 c0.453-5.297,1.281-10.344,2.594-15.094c1.984-7.266,10-16.781,10-16.781c0.484-0.813,1.313-1.344,2.25-1.469 c0.922-0.094,1.891,0.219,2.531,0.875l9.313,9.313c0.984,0.969,1.172,2.531,0.438,3.75c0,0-6.047,6.969-7.484,12.25 c-1.422,5.25-2.156,10.484-2.156,15.719c0,6.578,1.156,12.031,3.5,16.344c2.344,4.328,5.953,6.469,10.906,6.469 c3.563,0,6.359-1.063,8.438-3.188c2.047-2.125,3.328-5.719,3.922-10.797l1.422-16.672c0.953-9.844,3.672-17.469,8.203-22.797 c4.531-5.359,11.391-8.031,20.578-8.031c5.063,0,9.578,1.031,13.563,3.078c3.969,2.063,7.328,4.859,10.078,8.438 c2.734,3.563,4.781,7.719,6.172,12.469c0.547,1.969,0.953,4.031,1.297,6.125h13.375v18.563h-13.141 c-0.406,4.344-1.109,8.453-2.156,12.281c-1.797,6.531-7.125,13.375-7.125,13.375c-0.438,0.844-1.266,1.438-2.234,1.594 c-0.953,0.125-1.922-0.188-2.609-0.859L286.953,315c-0.938-0.906-1.156-2.344-0.563-3.531c0,0,3.672-5.172,4.844-9.641 c1.172-4.453,1.734-9.141,1.734-14.078C292.969,281.313,291.766,276.531,289.266,273.453z M152,335.953 c20.375,30.016,59.266,50.297,104,50.297c44.75,0,83.625-20.281,104-50.313v92.906c-23.453,2.344-42.281,20.25-46.156,43.219 H198.172c-3.875-22.969-22.719-40.875-46.172-43.219V335.953z">
                                                                    </path>
                                                                    <path class="st0"
                                                                        d="M432,7.938H80c-44.188,0-80,35.813-80,80c0,44.172,35.813,80,80,80h24v-48c-17.656,0-32-14.359-32-32 c0-17.656,14.344-32,32-32h304c17.656,0,32,14.344,32,32c0,17.641-14.344,32-32,32v48h24c44.188,0,80-35.828,80-80 C512,43.75,476.188,7.938,432,7.938z">
                                                                    </path>
                                                                    <circle class="st0" cx="256" cy="157.188"
                                                                        r="13.75"></circle>
                                                                    <circle class="st0" cx="256" cy="418.938"
                                                                        r="13.75"></circle>
                                                                </g>
                                                            </g>
                                                        </svg>
                                                    </span>
                                                @elseif ($expense->payment_method == 'bank_transfer')
                                                    <span
                                                        class="ml-2 inline-flex items-center px-2.5 py-1.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-4 h-4" fill="#000000" version="1.1" id="Layer_1"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 496 496"
                                                            xml:space="preserve">
                                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                stroke-linejoin="round"></g>
                                                            <g id="SVGRepo_iconCarrier">
                                                                <g>
                                                                    <g>
                                                                        <g>
                                                                            <path
                                                                                d="M208,120c0-13.232,10.768-24,24-24h40V80h-40c-22.056,0-40,17.944-40,40h-9.888L0,211.056V256h16v32h16v128H16v32H0v48 h400v-48h-16v-32h-16V288h16v-32h16v-44.944L217.888,120H208z M32,432h48v16H32V432z M128,288v128h-16v32H96v-32H80V288h16v-32 h16v32H128z M224,288v128h-16v32h-16v-32h-16V288h16v-32h16v32H224z M320,288v128h-16v32h-16v-32h-16V288h16v-32h16v32H320z M368,432v16h-48v-16H368z M336,416V288h16v128H336z M272,272h-48v-16h48V272z M256,288v128h-16V288H256z M272,432v16h-48v-16 H272z M176,272h-48v-16h48V272z M160,288v128h-16V288H160z M176,432v16h-48v-16H176z M80,272H32v-16h48V272z M64,288v128H48V288 H64z M384,464v16H16v-16H384z M368,272h-48v-16h48V272z M384,240h-80h-16h-80h-16h-80H96H16v-19.056L185.888,136H192v56h16v-56 h6.112L384,220.944V240z">
                                                                            </path>
                                                                            <rect x="64" y="208" width="272"
                                                                                height="16"></rect>
                                                                            <path
                                                                                d="M408,0c-48.52,0-88,39.48-88,88s39.48,88,88,88c48.52,0,88-39.48,88-88S456.52,0,408,0z M408,160 c-39.704,0-72-32.296-72-72s32.296-72,72-72c39.704,0,72,32.296,72,72S447.704,160,408,160z">
                                                                            </path>
                                                                            <path
                                                                                d="M400,64h16c4.416,0,8,3.584,8,8h16c0-13.232-10.768-24-24-24V32h-16v16c-13.232,0-24,10.768-24,24s10.768,24,24,24h16 c4.416,0,8,3.584,8,8s-3.584,8-8,8h-16c-4.416,0-8-3.584-8-8h-16c0,13.232,10.768,24,24,24v16h16v-16c13.232,0,24-10.768,24-24 s-10.768-24-24-24h-16c-4.416,0-8-3.584-8-8S395.584,64,400,64z">
                                                                            </path>
                                                                            <rect x="288" y="80" width="16"
                                                                                height="16"></rect>
                                                                            <path
                                                                                d="M448,312c0,13.232-10.768,24-24,24h-8v16h8c22.056,0,40-17.944,40-40V184h-16V312z">
                                                                            </path>
                                                                            <rect x="384" y="336" width="16"
                                                                                height="16"></rect>
                                                                        </g>
                                                                    </g>
                                                                </g>
                                                            </g>
                                                        </svg> </span>
                                                @elseif ($expense->payment_method == 'credit_card')
                                                    <span
                                                        class="ml-2 inline-flex items-center px-2.5 py-1.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                stroke-linejoin="round"></g>
                                                            <g id="SVGRepo_iconCarrier">
                                                                <path
                                                                    d="M22 12C22 8.22876 22 6.34315 20.8284 5.17157C19.6569 4 17.7712 4 14 4H10C6.22876 4 4.34315 4 3.17157 5.17157C2 6.34315 2 8.22876 2 12C2 15.7712 2 17.6569 3.17157 18.8284C4.34315 20 6.22876 20 10 20H14C17.7712 20 19.6569 20 20.8284 18.8284C21.4816 18.1752 21.7706 17.3001 21.8985 16"
                                                                    stroke="#1C274C" stroke-width="1.5"
                                                                    stroke-linecap="round"></path>
                                                                <path d="M10 16H6" stroke="#1C274C" stroke-width="1.5"
                                                                    stroke-linecap="round"></path>
                                                                <path d="M14 16H12.5" stroke="#1C274C" stroke-width="1.5"
                                                                    stroke-linecap="round"></path>
                                                                <path d="M2 10L7 10M22 10L11 10" stroke="#1C274C"
                                                                    stroke-width="1.5" stroke-linecap="round"></path>
                                                            </g>
                                                        </svg>
                                                    </span>
                                                @elseif ($expense->payment_method == 'debit_card')
                                                    <span
                                                        class="ml-2 inline-flex items-center px-2.5 py-1.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                stroke-linejoin="round"></g>
                                                            <g id="SVGRepo_iconCarrier">
                                                                <path
                                                                    d="M22 12C22 8.22876 22 6.34315 20.8284 5.17157C19.6569 4 17.7712 4 14 4H10C6.22876 4 4.34315 4 3.17157 5.17157C2 6.34315 2 8.22876 2 12C2 15.7712 2 17.6569 3.17157 18.8284C4.34315 20 6.22876 20 10 20H14C17.7712 20 19.6569 20 20.8284 18.8284C21.4816 18.1752 21.7706 17.3001 21.8985 16"
                                                                    stroke="#1C274C" stroke-width="1.5"
                                                                    stroke-linecap="round"></path>
                                                                <path d="M10 16H6" stroke="#1C274C" stroke-width="1.5"
                                                                    stroke-linecap="round"></path>
                                                                <path d="M14 16H12.5" stroke="#1C274C" stroke-width="1.5"
                                                                    stroke-linecap="round"></path>
                                                                <path d="M2 10L7 10M22 10L11 10" stroke="#1C274C"
                                                                    stroke-width="1.5" stroke-linecap="round"></path>
                                                            </g>
                                                        </svg>
                                                    </span>
                                                @elseif ($expense->payment_method == 'check')
                                                    <span
                                                        class="ml-2 inline-flex items-center px-2.5 py-1.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <svg class="w-4 h-4" viewBox="0 -9 58 58" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                stroke-linejoin="round"></g>
                                                            <g id="SVGRepo_iconCarrier">
                                                                <g clip-path="url(#clip0_1503_2376)">
                                                                    <g clip-path="url(#clip1_1503_2376)">
                                                                        <path
                                                                            d="M3.5614 0.5H54.4386C56.1256 0.5 57.5 1.87958 57.5 3.58974V36.4103C57.5 38.1204 56.1256 39.5 54.4386 39.5H3.5614C1.87437 39.5 0.5 38.1204 0.5 36.4103V3.58974C0.5 1.87958 1.87437 0.5 3.5614 0.5Z"
                                                                            stroke="#F3F3F3"></path>
                                                                        <path d="M49.5 8.5H8.5V31.5H49.5V8.5Z"
                                                                            fill="#fcfcfc"></path>
                                                                        <path d="M46 16H36V19H46V16Z" fill="white">
                                                                        </path>
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M32 16H12V17H32V16ZM24 27H12V28H24V27ZM12 24H20V25H12V24ZM28 19H12V20H28V19Z"
                                                                            fill="#001018"></path>
                                                                        <path d="M50 8H8V12H50V8Z" fill="white"></path>
                                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                                            d="M36.7051 24.5019C36.7041 24.5028 36.7032 24.5037 36.7022 24.5047C36.6753 24.5317 36.6403 24.5774 36.6049 24.6407C36.5702 24.7027 36.5419 24.7697 36.523 24.8304C36.5137 24.8603 36.5076 24.8859 36.5039 24.9061C36.5021 24.9161 36.5011 24.9239 36.5006 24.9296C36.5 24.9348 36.5 24.9374 36.5 24.9374V24.9373C36.5 25.2135 36.2761 25.4373 36 25.4373C35.7239 25.4373 35.5 25.2135 35.5 24.9373C35.5 24.6778 35.6043 24.3812 35.7321 24.1527C35.7998 24.0316 35.8869 23.9061 35.9941 23.7986C36.0979 23.6945 36.2475 23.5803 36.4425 23.5316C37.1017 23.3668 37.5808 23.8553 37.8258 24.176C38.0855 24.516 38.2812 24.9256 38.3881 25.1494C38.3944 25.1625 38.4003 25.1749 38.406 25.1867C38.4856 25.3527 38.5367 25.5599 38.5756 25.7292C38.5901 25.7925 38.6036 25.8534 38.6167 25.913C38.6434 26.0343 38.6689 26.15 38.7001 26.2688C38.7458 26.4431 38.7924 26.5761 38.8422 26.6679C38.8718 26.7225 38.8925 26.7438 38.8995 26.7501C38.9607 26.7725 38.9804 26.7621 38.9881 26.7581C38.9886 26.7578 38.989 26.7576 38.9894 26.7574C39.0157 26.7445 39.0705 26.7027 39.138 26.5943C39.2755 26.3734 39.3637 26.0409 39.3935 25.8055C39.3982 25.7687 39.3956 25.7 39.3777 25.5681C39.3742 25.5425 39.37 25.5131 39.3653 25.4812C39.3505 25.3794 39.3319 25.2516 39.3237 25.1368C39.3129 24.9843 39.3117 24.7717 39.3933 24.5653C39.4865 24.3293 39.6705 24.1447 39.9364 24.0516C40.6483 23.8025 41.1902 24.2474 41.5063 24.5797C41.6474 24.7281 41.7816 24.8944 41.8957 25.036C41.9167 25.0621 41.9371 25.0873 41.9567 25.1115C42.0952 25.2821 42.1935 25.3942 42.2743 25.46C42.3875 25.5523 42.4453 25.5537 42.4639 25.5535C42.4964 25.553 42.5564 25.5401 42.6687 25.4724C42.7553 25.4202 42.8349 25.3607 42.9359 25.2852C42.9765 25.2549 43.0206 25.2219 43.07 25.1858C43.2227 25.0742 43.4204 24.938 43.6501 24.8519C43.9092 24.7548 44.3436 24.6412 44.7726 24.6191C44.9875 24.608 45.2274 24.6182 45.4531 24.6814C45.6803 24.7449 45.9306 24.8736 46.0985 25.1255C46.2517 25.3552 46.1896 25.6657 45.9598 25.8189C45.7305 25.9717 45.4208 25.9102 45.2674 25.6815C45.2671 25.6811 45.2668 25.6806 45.2664 25.6802C45.2669 25.6808 45.2673 25.6813 45.2676 25.6817C45.2675 25.6817 45.2674 25.6816 45.2674 25.6815C45.2627 25.6771 45.2421 25.6607 45.1837 25.6444C45.0998 25.6209 44.9782 25.6098 44.8242 25.6177C44.5153 25.6337 44.1823 25.7203 44.0012 25.7882C43.9076 25.8233 43.8026 25.889 43.6601 25.9932C43.6322 26.0135 43.6014 26.0366 43.5685 26.0612C43.4559 26.1455 43.318 26.2487 43.1848 26.3289C43.0035 26.4381 42.7642 26.5493 42.4783 26.5534C42.1786 26.5577 41.899 26.4441 41.6426 26.2353C41.47 26.0946 41.3108 25.9025 41.1803 25.7417C41.1566 25.7126 41.1335 25.6839 41.1108 25.6558C40.9962 25.5139 40.8921 25.385 40.7817 25.2689C40.5452 25.0203 40.4135 24.9721 40.3188 24.9831C40.3196 24.9516 40.3226 24.9368 40.3233 24.9332C40.3236 24.9322 40.3236 24.9321 40.3233 24.9328C40.32 24.9412 40.3108 24.9588 40.2928 24.9761C40.2802 24.9882 40.2699 24.9937 40.2667 24.9953C40.2661 24.9957 40.2657 24.9958 40.2657 24.9959C40.2657 24.9959 40.266 24.9957 40.2668 24.9955C40.283 24.9898 40.3002 24.9853 40.3188 24.9831C40.3183 25.0034 40.3187 25.0305 40.3212 25.066C40.3265 25.1411 40.337 25.213 40.35 25.3023C40.3558 25.3421 40.3621 25.3854 40.3687 25.4339C40.3866 25.5665 40.4084 25.7515 40.3856 25.9312C40.3459 26.2441 40.2292 26.7336 39.9869 27.1228C39.8644 27.3196 39.6856 27.5298 39.4295 27.6554C39.154 27.7904 38.8423 27.8009 38.529 27.6791C38.2435 27.5681 38.0698 27.341 37.9634 27.145C37.8556 26.9465 37.7849 26.7212 37.7328 26.5226C37.6966 26.3843 37.6637 26.2352 37.6348 26.1044C37.6228 26.0501 37.6115 25.999 37.601 25.9531C37.5598 25.7736 37.5291 25.6709 37.5043 25.619C37.5009 25.612 37.4975 25.605 37.4941 25.5978C37.3822 25.3643 37.2259 25.038 37.0311 24.783C36.8523 24.549 36.7465 24.5078 36.7051 24.5019Z"
                                                                            fill="#001018"></path>
                                                                        <path d="M49.5 8.5H8.5V31.5H49.5V8.5Z"
                                                                            stroke="#EFF1F3"></path>
                                                                    </g>
                                                                </g>
                                                                <defs>
                                                                    <clipPath id="clip0_1503_2376">
                                                                        <rect width="58" height="40"
                                                                            fill="white">
                                                                        </rect>
                                                                    </clipPath>
                                                                    <clipPath id="clip1_1503_2376">
                                                                        <rect width="58" height="40"
                                                                            fill="white">
                                                                        </rect>
                                                                    </clipPath>
                                                                </defs>
                                                            </g>
                                                        </svg>
                                                    </span>
                                                @elseif ($expense->payment_method == 'digital_wallet')
                                                    <span
                                                        class="ml-2 inline-flex items-center px-2.5 py-1.5 rounded text-xs font-medium bg-pink-100 text-pink-800">
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" id="Layer_1"
                                                            data-name="Layer 1" xmlns="http://www.w3.org/2000/svg"
                                                            fill="#000000">
                                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                stroke-linejoin="round"></g>
                                                            <g id="SVGRepo_iconCarrier">
                                                                <defs>
                                                                    <style>
                                                                        .cls-1 {
                                                                            fill: none;
                                                                            stroke: #020202;
                                                                            stroke-miterlimit: 10;
                                                                            stroke-width: 1.91px;
                                                                        }
                                                                    </style>
                                                                </defs>
                                                                <rect class="cls-1" x="4.36" y="1.5" width="15.27"
                                                                    height="21" rx="2.04"></rect>
                                                                <path class="cls-1"
                                                                    d="M13.91,2.45H10.09a.94.94,0,0,1-.95-1h5.72A.94.94,0,0,1,13.91,2.45Z">
                                                                </path>
                                                                <path class="cls-1"
                                                                    d="M9.14,14.86h3.34a1.43,1.43,0,0,0,1.43-1.43h0A1.43,1.43,0,0,0,12.48,12h-1a1.43,1.43,0,0,1-1.43-1.43h0a1.43,1.43,0,0,1,1.43-1.43h3.34">
                                                                </path>
                                                                <line class="cls-1" x1="12" y1="7.23"
                                                                    x2="12" y2="9.14"></line>
                                                                <line class="cls-1" x1="12" y1="14.86"
                                                                    x2="12" y2="16.77"></line>
                                                            </g>
                                                        </svg>
                                                    </span>
                                                @else

                                                @endif
                                            @endif

                                        </td>


                                        <td class="py-2.5 px-4 space-x-2 text-right">
                                            @if (user_can('Update Expense'))
                                                <x-secondary-button-table
                                                    wire:click="showEditExpense({{ $expense->id }})"
                                                    wire:key="edit-expense-button-{{ $expense->id }}">
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
                                            @endif

                                            @if (user_can('Delete Expense'))
                                                <x-danger-button-table
                                                    wire:click="showDeleteMenuexpense({{ $expense->id }})"
                                                    wire:key="delete-expense-button-{{ $expense->id }}">
                                                    {{-- {{$expense->id }} --}}

                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd"
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>

                                                </x-danger-button-table>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="py-2.5 px-4 text-gray-500 dark:text-gray-400" colspan="10">
                                        @lang('messages.noExpensesAdded')
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div wire:key='customer-table-paginate-{{ microtime() }}'
        class="sticky bottom-0 right-0 items-center w-full py-2.5 px-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center mb-4 sm:mb-0 w-full">
            {{ $expenses->links() }}
        </div>
    </div>

    <x-right-modal wire:model.live="showExpenseDetailsModal">
        <x-slot name="title">
            {{ __('modules.expenses.expenseDetails') }}
        </x-slot>

        <x-slot name="content">
            @if ($viewExpenseDetails)
                @livewire('payments.expense-details', ['expenses' => $viewExpenseDetails], key(microtime()))
            @endif
        </x-slot>

        <x-slot name="footer">
            {{-- <x-secondary-button wire:click="$set('showEditExpense', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button> --}}
        </x-slot>
    </x-right-modal>

    <x-right-modal wire:model.live="showEditExpenseModal">
        <x-slot name="title">
            {{ __('modules.expenses.editExpense') }}
        </x-slot>

        <x-slot name="content">
            @if ($selectedExpenses)
                @livewire('forms.EditExpense', ['expenses' => $selectedExpenses], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            {{-- <x-secondary-button wire:click="$set('showEditExpense', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button> --}}
        </x-slot>
    </x-right-modal>


    <x-confirmation-modal wire:model.live="confirmDeleteExpense">
        <x-slot name="title">
            @lang('modules.expenses.deleteExpense')?
        </x-slot>

        <x-slot name="content">
            @lang('modules.expenses.deleteExpensesMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteExpense')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($deleteExpense)
                <x-danger-button class="ml-3" wire:click='deleteExpenseData({{ $deleteExpense }})'
                    wire:loading.attr="disabled">
                    {{ __('Delete') }}
                </x-danger-button>
            @endif
        </x-slot>
    </x-confirmation-modal>


</div>
