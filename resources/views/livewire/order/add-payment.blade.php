<div>
    <x-dialog-modal wire:model.live="showAddPaymentModal" maxWidth="4xl" class="w-full max-w-full">
        <x-slot name="title">
            @if($order)

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h2 class="text-xl sm:text-xl font-semibold">{{ __('modules.order.payment') }}</h2>
                </div>
                <div class="flex items-center gap-2 sm:gap-4 mt-2 sm:mt-0">
                    <span class="text-base sm:text-lg font-medium">

                            {{ $order->show_formatted_order_number }}

                    </span>
                    <span class="text-lg sm:text-xl font-bold text-skin-base">{{ currency_format($order->total, restaurant()->currency_id) }}</span>
                </div>
            </div>
            @endif
        </x-slot>

         <x-slot name="content">
            @if($order)

            <div x-data="{
                showSplitOptions: false,
                splitType: @entangle('splitType').live,
                numberOfSplits: 2,
                customSplits: [1, 2],
                splits: [{ id: 1, items: [] }],
                activeSplitId: 1
               }" class="relative min-h-[500px]">
                <!-- Regular Payment View -->
                <div x-show="!showSplitOptions" class="flex flex-col md:flex-row gap-4 flex-wrap w-full">
                    <!-- Left Side - Payment Options -->
                    <div class="w-full md:flex-1">
                        <!-- Payment Type Selector -->
                        <div class="flex gap-3 mb-4">
                            <button class="flex-1 py-2 px-3 text-center border rounded-lg bg-skin-base text-white text-sm" @click="showSplitOptions = false">
                                {{ __('modules.order.fullPayment') }}
                            </button>
                            <button class="flex-1 py-2 px-3 text-center border rounded-lg hover:bg-gray-50 text-sm" wire:click="showSplitOptions = true" @click="showSplitOptions = true">
                                {{ __('modules.order.splitBill') }}
                            </button>
                        </div>

                        <!-- Payment Methods -->
                        <div @class([
                            'grid gap-3',
                            'grid-cols-2 sm:grid-cols-4' => !$canAddTip,
                            'grid-cols-2 sm:grid-cols-5' => $canAddTip
                        ])>
                            <button wire:click="setPaymentMethod('cash')"
                                class="p-3 text-center border rounded-lg {{ $paymentMethod === 'cash' ? 'bg-skin-base/5 border-skin-base' : 'hover:bg-gray-50' }}">
                                <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2m2 4h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2m7-5a2 2 0 1 1-4 0 2 2 0 0 1 4 0"/></svg>
                                <span class="text-sm">@lang('modules.order.cash')</span>
                            </button>

                            <button wire:click="setPaymentMethod('card')"
                                class="p-3 text-center border rounded-lg {{ $paymentMethod === 'card' ? 'bg-skin-base/5 border-skin-base' : 'hover:bg-gray-50' }}">
                                <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3"/></svg>
                                <span class="text-sm">@lang('modules.order.card')</span>
                            </button>

                            <button wire:click="setPaymentMethod('upi')"
                                class="p-3 text-center border rounded-lg {{ $paymentMethod === 'upi' ? 'bg-skin-base/5 border-skin-base' : 'hover:bg-gray-50' }}">

                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-qr-code-scan w-6 h-6 mx-auto mb-1" viewBox="0 0 16 16"><path d="M0 .5A.5.5 0 0 1 .5 0h3a.5.5 0 0 1 0 1H1v2.5a.5.5 0 0 1-1 0zm12 0a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0V1h-2.5a.5.5 0 0 1-.5-.5M.5 12a.5.5 0 0 1 .5.5V15h2.5a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H15v-2.5a.5.5 0 0 1 .5-.5M4 4h1v1H4z"/><path d="M7 2H2v5h5zM3 3h3v3H3zm2 8H4v1h1z"/><path d="M7 9H2v5h5zm-4 1h3v3H3zm8-6h1v1h-1z"/><path d="M9 2h5v5H9zm1 1v3h3V3zM8 8v2h1v1H8v1h2v-2h1v2h1v-1h2v-1h-3V8zm2 2H9V9h1zm4 2h-1v1h-2v1h3zm-4 2v-1H8v1z"/><path d="M12 9h2V8h-2z"/></svg>
                                <span class="text-sm">@lang('modules.order.upi')</span>
                            </button>

                            <button wire:click="setPaymentMethod('bank_transfer')"
                                class="p-3 text-center border rounded-lg {{ $paymentMethod === 'bank_transfer' ? 'bg-skin-base/5 border-skin-base' : 'hover:bg-gray-50' }}">
                                <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <span class="text-sm">@lang('modules.order.bank_transfer')</span>
                            </button>

                            <button wire:click="setPaymentMethod('due')"
                                class="p-3 text-center border rounded-lg {{ $paymentMethod === 'due' ? 'bg-skin-base/5 border-skin-base' : 'hover:bg-gray-50' }}">
                                <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm">@lang('modules.order.due')</span>
                            </button>
                            @if($canAddTip)
                            <button wire:click="addTipModal"
                                class="p-3 text-center border rounded-lg transition-all duration-200 {{ $order && $order->tip_amount > 0
                                    ? 'bg-green-50 dark:bg-green-900/50 border-green-200 dark:border-green-800 hover:bg-green-100 dark:hover:bg-green-900/70'
                                    : 'border-2 border-dashed border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-skin-base dark:hover:border-skin-base'
                                }}">

                                <svg class="w-6 h-6 mx-auto mb-1 {{ $order && $order->tip_amount > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0"/>
                                </svg>

                                @if($order && $order->tip_amount > 0)
                                    <div class="text-sm">
                                        <span class="font-medium text-green-600 dark:text-green-400">@lang('modules.order.tipAdded')</span>
                                        <span class="block text-green-500 dark:text-green-300">{{ currency_format($order->tip_amount, restaurant()->currency_id) }}</span>
                                    </div>
                                @else
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                        @lang('modules.order.addTip')
                                    </span>
                                @endif
                            </button>
                            @endif
                        </div>

                        <!-- Amount Input and Summary -->
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm mb-1">{{ __('modules.order.amount') }}</label>
                                <input type="text" wire:model.live="paymentAmount"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                    class="w-full text-2xl p-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200">

                            </div>

                            <div class="hidden">
                                <label class="block text-sm mb-1">{{ __('modules.order.returnAmount') }}</label>
                                <div class="w-full text-2xl p-3 rounded-lg border-gray-300 bg-gray-100 ">
                                    {{ currency_format($returnAmount, restaurant()->currency_id) }}
                                </div>
                            </div>

                            @if ($order)
                            <!-- Payment Summary -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 my-2 text-sm sm:text-base">
                                <div class="space-y-2">
                                    @if($order->tip_amount > 0)
                                        <div x-data="{ showTipBreakdown: false }" class="space-y-2">
                                            <!-- Expandable Tip Breakdown - Above when opened -->
                                            <div x-show="showTipBreakdown"  x-cloak
                                                    x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                                    x-transition:enter-end="opacity-100 translate-y-0"
                                                    x-transition:leave="transition ease-in duration-150"
                                                    x-transition:leave-start="opacity-100 translate-y-0"
                                                    x-transition:leave-end="opacity-0 -translate-y-2">
                                                <div class="flex justify-between text-gray-600 dark:text-gray-300">
                                                    <span>{{ __('modules.order.total') }}</span>
                                                    <span>{{ currency_format($order->total - $order->tip_amount, restaurant()->currency_id) }}</span>
                                                </div>
                                                <div class="flex justify-between text-green-600 dark:text-green-400">
                                                    <span>{{ __('modules.order.tipAmount') }}</span>
                                                    <span>+ {{ currency_format($order->tip_amount, restaurant()->currency_id) }}</span>
                                                </div>
                                            </div>

                                            <!-- Grand Total - Clickable -->
                                            <div class="flex justify-between items-center cursor-pointer text-gray-800 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded"
                                                @click="showTipBreakdown = !showTipBreakdown">
                                                <span class="flex items-center gap-1">
                                                    {{ __('modules.order.grandTotal') }}
                                                    <svg class="w-3 h-3 text-current transition-transform"
                                                        :class="showTipBreakdown ? 'rotate-180' : ''"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </span>
                                                <span class="font-medium">{{ currency_format($order->total, restaurant()->currency_id) }}</span>
                                            </div>
                                        </div>
                                        @else
                                        <div class="flex justify-between">
                                            <span>{{ __('modules.order.total') }}</span>
                                            <span class="font-medium">{{ currency_format($order->total, restaurant()->currency_id) }}</span>
                                        </div>
                                        @endif
                                    @if($paidAmount > 0)
                                    <div class="flex justify-between text-green-500 text-sm">
                                        <span>{{ __('modules.order.amountPaid') }}</span>
                                        <span class="font-medium">- {{ currency_format($paidAmount, restaurant()->currency_id) }}</span>
                                    </div>
                                    @endif
                                    <div class="flex justify-between text-sm font-semibold text-skin-base">
                                        <span>{{ __('modules.order.payableAmount') }}</span>
                                        <span class="font-bold">{{ currency_format(($order->total - $paidAmount), restaurant()->currency_id) }}</span>
                                    </div>
                                    @if($returnAmount > 0)
                                    <div class="flex justify-between text-green-600 pt-2 border-t">
                                        <span>{{ __('modules.order.returnAmount') }}</span>
                                        <span class="font-medium">{{ currency_format($returnAmount, restaurant()->currency_id) }}</span>
                                    </div>
                                    @else
                                    <div class="flex justify-between text-red-600 pt-2 border-t">
                                        <span>{{ __('modules.order.dueAmount') }}</span>
                                        <span class="font-medium">{{ currency_format($balanceAmount, restaurant()->currency_id) }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right Side - Numpad -->
                    <div class="w-full md:w-72">
                        <!-- Quick Amount Buttons -->
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            @foreach($predefinedAmounts as $amount)
                            <button wire:click="quickAmount({{ $amount }})" class="p-2 text-center border rounded-lg hover:bg-gray-50 w-full text-sm sm:text-base">
                                <span class="font-medium">{{ currency_format($amount, restaurant()->currency_id) }}</span>
                            </button>
                            @endforeach
                        </div>

                        <!-- Numpad -->
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(range(1, 9) as $number)
                            <button wire:click="appendNumber('{{ $number }}')" class="p-4 text-center border rounded-lg hover:bg-gray-50 w-full text-lg sm:text-xl">
                                <span class="text-xl">{{ $number }}</span>
                            </button>
                            @endforeach
                            <button wire:click="appendNumber('.')" class="p-4 text-center border rounded-lg hover:bg-gray-50 w-full text-lg sm:text-xl">
                                <span class="text-xl">.</span>
                            </button>
                            <button wire:click="appendNumber('0')" class="p-4 text-center border rounded-lg hover:bg-gray-50 w-full text-lg sm:text-xl">
                                <span class="text-xl">0</span>
                            </button>
                            <button wire:click="clearAmount" class="p-4 text-center border rounded-lg hover:bg-gray-50 w-full text-lg sm:text-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Split Bill Options -->
                <div x-show="showSplitOptions"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    class="absolute inset-0 bg-white dark:bg-gray-800 rounded-lg flex flex-col max-h-[90vh] overflow-y-auto w-full">

                    <!-- Header - Fixed -->
                    <div class="p-4 border-b bg-white dark:bg-gray-800">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-semibold">{{ __('modules.order.splitBill') }}</h3>
                                <template x-if="splitType">
                                    <button @click="splitType = null" class="text-sm text-gray-500 hover:text-gray-700">
                                        ({{ __('modules.order.changeMethod') }})
                                    </button>
                                </template>
                            </div>
                            <button @click="showSplitOptions = false; splitType = null" wire:click="showSplitOptions = false;" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <!-- Content - Scrollable -->
                    <div class="flex-1 overflow-y-auto my-4">
                        <!-- Split Methods Selection -->
                        <div x-show="!splitType" class="h-full grid grid-cols-1 sm:grid-cols-3 gap-4 place-content-center">
                            <button @click="splitType = 'equal'" class="p-4 text-center border rounded-lg hover:bg-gray-50">
                                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="block font-medium">{{ __('modules.order.equalSplit') }}</span>
                                <span class="text-sm text-gray-500">{{ __('modules.order.splitEqually') }}</span>
                            </button>
                            <button @click="splitType = 'custom'" class="p-4 text-center border rounded-lg hover:bg-gray-50">
                                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span class="block font-medium">{{ __('modules.order.customSplit') }}</span>
                                <span class="text-sm text-gray-500">{{ __('modules.order.splitByAmount') }}</span>
                            </button>
                            <button @click="splitType = 'items'" class="p-4 text-center border rounded-lg hover:bg-gray-50">
                                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span class="block font-medium">{{ __('modules.order.splitByItems') }}</span>
                                <span class="text-sm text-gray-500">{{ __('modules.order.splitByDishes') }}</span>
                            </button>
                        </div>

                        <!-- Equal Split Options -->
                        <div x-show="splitType === 'equal'" class="h-full flex flex-col">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 border-b pb-4">
                                <div class="flex flex-wrap gap-2 flex-1">
                                    @for($i = 1; $i <= $numberOfSplits; $i++)
                                        <div class="flex items-center gap-1">
                                            <div class="px-4 py-2 rounded-lg text-sm border">
                                                <span>Split {{ $i }}</span>
                                            </div>
                                            @if($numberOfSplits > 2)
                                                <button wire:click="removeSplit({{ $i }})"
                                                    class="p-1 text-gray-400 hover:text-red-500 rounded-full hover:bg-gray-100">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                                <button wire:click="addNewSplit"
                                    class="px-3 py-2 text-sm border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 shrink-0">
                                    + {{ __('modules.order.newSplit') }}
                                </button>
                            </div>
                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4 content-start mt-2">
                                @for($i = 1; $i <= $numberOfSplits; $i++)
                                    <div class="flex items-center gap-4 p-3 bg-gray-50 dark:bg-gray-600 rounded-lg">
                                        <div class="flex-1 flex justify-between items-center gap-3">
                                            <span>Split {{ $i }}</span>
                                            <input type="number"  readonly
                                                wire:model.live="splits.{{ $i }}.amount"
                                                class="w-32 rounded-lg border-gray-300 text-right"
                                                placeholder="{{ number_format($order->total / $numberOfSplits, 2) }}">
                                        </div>

                                        <select class="rounded-lg border-gray-300 w-28"
                                            wire:model.live="splits.{{ $i }}.paymentMethod">
                                            <option value="cash">{{ __('modules.order.cash') }}</option>
                                            <option value="card">{{ __('modules.order.card') }}</option>
                                            <option value="upi">{{ __('modules.order.upi') }}</option>
                                            <option value="bank_transfer">{{ __('modules.order.bank_transfer') }}</option>
                                            <option value="due">{{ __('modules.order.due') }}</option>
                                        </select>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Custom Split Options -->
                        <div x-show="splitType === 'custom'" class="h-full flex flex-col">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 border-b pb-4">
                                <div class="flex flex-wrap gap-2 flex-1">
                                    @foreach($customSplits as $splitNumber)
                                        <div class="flex items-center gap-1">
                                            <div class="px-4 py-2 rounded-lg text-sm border">
                                                <span>Split {{ $splitNumber }}</span>
                                            </div>
                                            @if(count($customSplits) > 2)
                                                <button wire:click="removeCustomSplit({{ $splitNumber }})"
                                                    class="p-1 text-gray-400 hover:text-red-500 rounded-full hover:bg-gray-100">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button wire:click="addNewCustomSplit"
                                    class="px-3 py-2 text-sm border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 shrink-0">
                                    + {{ __('modules.order.newSplit') }}
                                </button>
                            </div>
                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4 content-start mt-2">
                                @foreach($customSplits as $splitNumber)
                                    <div class="flex items-center gap-4 p-3 bg-gray-50 dark:bg-gray-600 rounded-lg">
                                        <div class="flex-1 flex justify-between items-center gap-3">
                                            <span>{{ __('modules.order.split') }} {{ $splitNumber }}</span>
                                            <input type="number" min="0" step="0.01"
                                                wire:model.live="splits.{{ $splitNumber }}.amount" wire:keyup="updateBalanceAmount"
                                                class="w-32 rounded-lg border-gray-300 text-right"
                                                placeholder="0.00"
                                                oninput="if(this.value < 0) this.value = 0;">
                                        </div>
                                        <select class="rounded-lg border-gray-300 w-28"
                                            wire:model.live="splits.{{ $splitNumber }}.paymentMethod">
                                            <option value="cash">{{ __('modules.order.cash') }}</option>
                                            <option value="card">{{ __('modules.order.card') }}</option>
                                            <option value="upi">{{ __('modules.order.upi') }}</option>
                                            <option value="bank_transfer">{{ __('modules.order.bank_transfer') }}</option>
                                            <option value="due">{{ __('modules.order.due') }}</option>
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Items Split Options -->
                        <div x-show="splitType === 'items'" class="space-y-6">
                            <!-- Splits Navigation -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 border-b pb-4">
                                <div class="flex flex-wrap gap-2 flex-1">
                                    @foreach($splits as $splitId => $split)
                                        <div class="flex items-center gap-1">
                                            <button wire:click="$set('activeSplitId', {{ $splitId }})"
                                                class="px-4 py-2 rounded-lg text-sm font-medium
                                                {{ $activeSplitId === $splitId ? 'bg-skin-base text-white' : 'border hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                                <span>{{ __('modules.order.split') }} {{ $splitId }}</span>
                                            </button>
                                            @if(count($splits) > 1 && $splitId !== 1)
                                                <button wire:click="removeItemSplit({{ $splitId }})"
                                                    class="p-1 text-gray-400 hover:text-red-500 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button wire:click="addNewItemSplit"
                                    class="px-3 py-2 text-sm border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 shrink-0">
                                    + {{ __('modules.order.newSplit') }}
                                </button>
                            </div>

                            <!-- Split Content - Side by Side Layout -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Left Side - Available Items -->
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <h4 class="font-medium text-sm text-gray-500 dark:text-gray-300">{{ __('modules.order.availableItems') }}</h4>
                                        <span class="text-sm text-gray-500 dark:text-gray-300">{{ __('modules.order.clickToAdd') }}</span>
                                    </div>
                                    <div class="space-y-2 max-h-[300px] sm:max-h-[400px] overflow-y-auto">
                                        @foreach($availableItems as $index => $item)
                                            @if($item['remaining'] > 0)
                                            <div class="p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 flex justify-between items-center">
                                                <div>
                                                    <span class="font-semibold text-base dark:text-gray-300">{{ $item['name'] }}</span>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        <span>{{  $item['remaining'] }}</span> x (&nbsp;
                                                        <span>{{ __('modules.order.base') }}: {{ currency_format($item['base_price'], restaurant()->currency_id) }}</span>
                                                        @if($item['discount'] > 0)
                                                            <span>- {{ __('modules.order.discount') }}: {{ currency_format($item['discount'], restaurant()->currency_id) }}</span>
                                                        @endif
                                                        @if($item['tax_amount'] > 0)
                                                            <span>+ {{ __('modules.order.tax') }}: {{ currency_format($item['tax_amount'], restaurant()->currency_id) }}</span>
                                                        @endif
                                                        @if($item['extra_charges'] > 0)
                                                            <span>+ {{ __('modules.order.extraCharges') }}: {{ currency_format($item['extra_charges'], restaurant()->currency_id) }}</span>
                                                        @endif
                                                        @if($item['tip'] > 0)
                                                            <span>+ {{ __('modules.order.tipAmount') }}: {{ currency_format($item['tip'], restaurant()->currency_id) }}</span>
                                                        @endif
                                                        )
                                                    </div>
                                                </div>

                                                <div class="text-right ml-4">
                                                    <span class="font-semibold text-base dark:text-gray-300">
                                                        {{ currency_format($item['price'] * $item['remaining'], restaurant()->currency_id) }}
                                                    </span>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <!-- Quantity controls -->
                                                        <button wire:click="addItemToSplit({{ $item['id'] }}, {{ $activeSplitId }}, 1)" class="px-2 py-1 border rounded hover:bg-gray-100" title="Add 1">
                                                            +
                                                        </button>
                                                        <span class="text-sm">{{ $item['remaining'] }}</span>
                                                        <button wire:click="addItemToSplit({{ $item['id'] }}, {{ $activeSplitId }}, {{ $item['remaining'] }})" class="px-2 py-1 border rounded hover:bg-gray-100" title="Add All">
                                                            @lang('modules.order.all')
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Right Side - Selected Items -->
                                <div class="space-y-3">
                                    <div class="flex items-center gap-4">
                                        <div class="flex-1 flex justify-between items-center text-gray-500 dark:text-gray-300">
                                            <h4 class="font-medium text-sm">{{ __('modules.order.itemsInSplit', ['split' => $activeSplitId]) }}</h4>
                                            <div class="text-sm">
                                                <span class="font-semibold">
                                                    {{ __('modules.order.total') }}: {{ currency_format(($splits[$activeSplitId]['total'] ?? 0), restaurant()->currency_id) }}
                                                </span>
                                                @if(restaurant()->tax_mode === 'item' && isset($splits[$activeSplitId]['tax_total']) && $splits[$activeSplitId]['tax_total'] > 0)
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                                        ({{ __('modules.order.tax') }}: {{ currency_format($splits[$activeSplitId]['tax_total'], restaurant()->currency_id) }})
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <select class="rounded-lg border-gray-300 dark:border-gray-600 w-28 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300"
                                            wire:change="updateSplitPaymentMethod({{ $activeSplitId }}, $event.target.value)"
                                            x-model="$wire.splits[{{ $activeSplitId }}].paymentMethod">
                                            <option value="cash">{{ __('modules.order.cash') }}</option>
                                            <option value="card">{{ __('modules.order.card') }}</option>
                                            <option value="upi">{{ __('modules.order.upi') }}</option>
                                            <option value="bank_transfer">{{ __('modules.order.bank_transfer') }}</option>
                                            <option value="due">{{ __('modules.order.due') }}</option>
                                        </select>
                                    </div>
                                    <div class="space-y-2 max-h-[300px] sm:max-h-[400px] overflow-y-auto">
                                        @if(empty($splits[$activeSplitId]['items']))
                                            <div class="text-center py-8 text-gray-500 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                {{ __('modules.order.noItemsInSplit') }}
                                            </div>
                                        @else
                                            @foreach($splits[$activeSplitId]['items'] as $index => $item)
                                            <div class="p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <div class="flex justify-between items-center font">
                                                    <span class="font-semibold text-base dark:text-gray-300">{{ $item['name'] }}</span>
                                                    <div class="text-right">
                                                        <span class="font-semibold text-base dark:text-gray-300">
                                                            {{ currency_format($item['price'] * $item['quantity'], restaurant()->currency_id) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex justify-between items-center gap-4">
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        <span>{{ $item['quantity'] }}</span> x (&nbsp;
                                                        <span>{{ __('modules.order.base') }}: {{ currency_format($item['base_price'], restaurant()->currency_id) }}</span>
                                                        @if($item['discount'] > 0)
                                                            <span>- {{ __('modules.order.discount') }}: {{ currency_format($item['discount'], restaurant()->currency_id) }}</span>
                                                        @endif
                                                        @if($item['tax_amount'] > 0)
                                                        <span>+ {{ __('modules.order.tax') }}: {{ currency_format($item['tax_amount'], restaurant()->currency_id) }}</span>
                                                        @endif
                                                        @if($item['extra_charges'] > 0)
                                                        <span>+ {{ __('modules.order.extraCharges') }}: {{ currency_format($item['extra_charges'], restaurant()->currency_id) }}</span>
                                                        @endif
                                                        @if($item['tip'] > 0)
                                                        <span>+ {{ __('modules.order.tipAmount') }}: {{ currency_format($item['tip'], restaurant()->currency_id) }}</span>
                                                        @endif
                                                        )
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <!-- Quantity controls -->
                                                        <button wire:click="decrementItemInSplit({{ $activeSplitId }}, {{ $index }})"
                                                                class="px-2 py-1 text-xs border rounded hover:bg-gray-100 dark:hover:bg-gray-600"
                                                                title="{{ __('modules.order.decreaseQuantity') }}">
                                                            -
                                                        </button>
                                                        <span class="text-sm font-medium">{{ $item['quantity'] }}</span>
                                                        <button wire:click="incrementItemInSplit({{ $activeSplitId }}, {{ $index }})"
                                                                class="px-2 py-1 text-xs border rounded hover:bg-gray-100 dark:hover:bg-gray-600"
                                                                title="{{ __('modules.order.increaseQuantity') }}">
                                                            +
                                                        </button>
                                                        <button wire:click="removeItemFromSplit({{ $activeSplitId }}, {{ $index }})"
                                                                class="text-sm text-red-500 hover:text-red-700 dark:hover:text-red-400 ml-2">
                                                            {{ __('modules.order.remove') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($order)
                    <!-- Footer - Fixed -->
                    <div class="border-t dark:border-gray-300 bg-white dark:bg-gray-800">
                        <!-- Common Payment Summary for All Split Types -->
                        <div x-show="splitType" class="mb-4">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 my-2 text-sm sm:text-base">
                                <div class="space-y-2">
                                    @if($order->tip_amount > 0)
                                        <div x-data="{ showTipBreakdown: false }" class="space-y-2">
                                            <!-- Expandable Tip Breakdown - Above when opened -->
                                            <div x-show="showTipBreakdown"  x-cloak
                                                    x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                                    x-transition:enter-end="opacity-100 translate-y-0"
                                                    x-transition:leave="transition ease-in duration-150"
                                                    x-transition:leave-start="opacity-100 translate-y-0"
                                                    x-transition:leave-end="opacity-0 -translate-y-2">
                                                <div class="flex justify-between text-gray-600 dark:text-gray-300">
                                                    <span>{{ __('modules.order.total') }}</span>
                                                    <span>{{ currency_format($order->total - $order->tip_amount, restaurant()->currency_id) }}</span>
                                                </div>
                                                <div class="flex justify-between text-green-600 dark:text-green-400">
                                                    <span>{{ __('modules.order.tipAmount') }}</span>
                                                    <span>+ {{ currency_format($order->tip_amount, restaurant()->currency_id) }}</span>
                                                </div>
                                            </div>

                                            <!-- Grand Total - Clickable -->
                                            <div class="flex justify-between items-center cursor-pointer text-gray-800 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded"
                                                @click="showTipBreakdown = !showTipBreakdown">
                                                <span class="flex items-center gap-1">
                                                    {{ __('modules.order.grandTotal') }}
                                                    <svg class="w-3 h-3 text-current transition-transform"
                                                        :class="showTipBreakdown ? 'rotate-180' : ''"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </span>
                                                <span class="font-medium">{{ currency_format($order->total, restaurant()->currency_id) }}</span>
                                            </div>
                                        </div>
                                        @else
                                        <div class="flex justify-between">
                                            <span>{{ __('modules.order.total') }}</span>
                                            <span class="font-medium">{{ currency_format($order->total, restaurant()->currency_id) }}</span>
                                        </div>
                                        @endif
                                    @if($paidAmount > 0)
                                    <div class="flex justify-between text-green-500 text-sm">
                                        <span>{{ __('modules.order.amountPaid') }}</span>
                                        <span class="font-medium">- {{ currency_format($paidAmount, restaurant()->currency_id) }}</span>
                                    </div>
                                    @endif
                                    <div class="flex justify-between text-sm font-semibold text-skin-base">
                                        <span>{{ __('modules.order.payableAmount') }}</span>
                                        <span class="font-bold">{{ currency_format(($order->total - $paidAmount), restaurant()->currency_id) }}</span>
                                    </div>

                                    <!-- Split-specific information -->
                                    <template x-if="splitType === 'equal'">
                                        <div class="flex justify-between text-blue-600 dark:text-blue-400 pt-2 border-t">
                                            <span>{{ __('modules.order.amountPerSplit') }}</span>
                                            <span class="font-medium">{{ currency_format(($order->total - $paidAmount) / $numberOfSplits, restaurant()->currency_id) }}</span>
                                        </div>
                                    </template>

                                    <template x-if="splitType === 'custom'">
                                        <div>
                                            <div class="flex justify-between text-blue-600 dark:text-blue-400 pt-2 border-t">
                                                <span>{{ __('modules.order.splitTotal') }}</span>
                                                <span class="font-medium">{{ currency_format(collect($splits)->sum(function($split) { return (float)($split['amount'] ?? 0); }), restaurant()->currency_id) }}</span>
                                            </div>
                                            @if(($balanceAmount > 0 || $returnAmount > 0))
                                                <div @class([
                                                    'flex justify-between',
                                                    'text-red-600' => $balanceAmount > 0,
                                                    'text-green-600' => $returnAmount > 0
                                                ])>
                                                    <span>{{ $balanceAmount > 0 ? __('modules.order.dueAmount') : __('modules.order.returnAmount') }}</span>
                                                    <span class="font-medium">{{ currency_format($balanceAmount > 0 ? $balanceAmount : $returnAmount, restaurant()->currency_id) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </template>

                                    <template x-if="splitType === 'items'">
                                        <div>
                                            <div class="flex justify-between text-blue-600 dark:text-blue-400 pt-2 border-t">
                                                <span>{{ __('modules.order.splitTotal') }}</span>
                                                <span class="font-medium">{{ currency_format(collect($splits)->sum('total'), restaurant()->currency_id) }}</span>
                                            </div>
                                            <div class="flex justify-between text-orange-600 dark:text-orange-400">
                                                <span>{{ __('modules.order.remainingAmount') }}</span>
                                                <span class="font-medium">{{ currency_format(($order->total - $paidAmount - collect($splits)->sum('total')), restaurant()->currency_id) }}</span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @endif
        </x-slot>

        <x-slot name="footer">
            @if($order)
            <div class="flex flex-row w-full gap-3">
                <x-button-cancel class="flex-1 px-6 py-3 text-base font-medium text-gray-700 hover:bg-gray-100 rounded-lg" wire:click="$toggle('showAddPaymentModal')">
                    {{ __('app.cancel') }}
                </x-button-cancel>
                <x-button wire:click="submitForm" class="flex-1 px-6 py-3 text-base font-medium bg-skin-base text-white rounded-lg hover:bg-skin-base-600">
                    {{ __('modules.order.completePayment') }}
                </x-button>
                </div>
            @endif
        </x-slot>

    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showTipModal" maxWidth="md">
        <x-slot name="title">
            <div class="flex items-center gap-2">
                <svg class="w-6 h-6 text-skin-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>@lang('modules.order.addTip')</span>
            </div>
        </x-slot>

        <x-slot name="content">
            @if ($order)
                <!-- Toggle Tip Mode -->
                <div class="flex justify-between items-center mb-2">
                    <x-label for="suggestedTip" class="" value="{{ __('modules.order.suggestedTip') }}" />
                    <div class="inline-flex justify-end p-0.5 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm">
                        <button wire:click="$set('tipMode', 'percentage')"
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition-all duration-200
                            {{ $tipMode === 'percentage' ? 'bg-skin-base text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                            %
                        </button>
                        <button wire:click="$set('tipMode', 'amount')"
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition-all duration-200
                            {{ $tipMode === 'amount' ? 'bg-skin-base text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                            {{ currency() }}
                        </button>
                    </div>
                </div>
                <!-- Quick Tip Options -->
                <div class="grid grid-cols-4 gap-2 mb-4">
                    @foreach([5, 10, 15, 20] as $value)
                        <button wire:click="setTip('{{ $tipMode }}', {{ $value }})"
                            class="relative group overflow-hidden">
                            <div class="px-3 py-3 text-sm rounded-lg border transition-all duration-200
                                {{ ($tipMode === 'percentage' && $tipPercentage === $value) || ($tipMode === 'amount' && $tipAmount === $value)
                                    ? 'bg-skin-base text-white border-skin-base'
                                    : 'hover:bg-gray-50 dark:hover:bg-gray-700 border-gray-300 dark:border-gray-600 dark:text-gray-200' }}">
                                <span class="block font-medium text-base">
                                    @if($tipMode === 'percentage')
                                        {{ $value }}%
                                    @else
                                        {{ currency_format($value, restaurant()->currency_id) }}
                                    @endif
                                </span>
                                <div class="absolute inset-x-0 -bottom-6 group-hover:bottom-0 transition-all duration-200 text-xs bg-gray-100 dark:bg-gray-700 dark:text-white py-1">
                                    @if($tipMode === 'percentage')
                                        {{ currency_format($order->total * $value / 100, restaurant()->currency_id) }}
                                    @else
                                        ≈ {{ number_format(($value / $order->total) * 100, 1) }}%
                                    @endif
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>

                <!-- Custom Tip Input -->
                <div class="mb-6">
                    <x-label for="tipAmount" value="{{ __('modules.order.customAmount') }}" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400">{{  currency() }}</span>
                        </div>
                        <x-input
                            id="tipAmount"
                            type="number"
                            step="0.01"
                            class="block w-full {{ strlen(currency()) > 2 ? 'pl-12' : 'pl-8' }} pr-12 rounded-xl"
                            wire:model.live="tipAmount"
                            placeholder="{{__('placeholders.enterCustomAmountPlaceholder')}}"

                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <button type="button"
                                wire:click="$set('tipAmount', 0)"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                title="Clear amount">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tip Note -->
                <div>
                    <label for="tipNote" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        @lang('modules.order.tipNote')
                        <span class="text-gray-400 text-xs">(@lang('app.optional'))</span>
                    </label>
                    <x-textarea
                        id="tipNote"
                        data-gramm="false"
                        class="block w-full rounded-xl"
                        wire:model="tipNote"
                        placeholder="{{__('placeholders.addNotePlaceholder')}}"
                    />
                </div>

                <!-- Current Order Summary -->
                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600 dark:text-gray-400">@lang('modules.order.currentTotal')</span>
                        <span class="font-medium">{{ currency_format($order->total - $order->tip_amount, restaurant()->currency_id) }}</span>
                    </div>
                    <div class="flex justify-between text-skin-base">
                        <span>@lang('modules.order.tipAmount')</span>
                        <span class="font-medium">+ {{ currency_format($tipAmount ?? 0, restaurant()->currency_id) }}</span>
                    </div>
                    <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-800">
                        <div class="flex justify-between">
                            <span class="font-medium">@lang('modules.order.newTotal')</span>
                            <span class="text-lg font-bold">
                                {{ currency_format(($order->total - $order->tip_amount + ($tipAmount ?: 0)), restaurant()->currency_id) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-3">
                <x-button-cancel wire:click="$toggle('showTipModal')" wire:loading.attr="disabled">
                    @lang('app.cancel')
                </x-button-cancel>
                <x-button wire:click="addTip" wire:loading.attr="disabled" class="min-w-[100px]">
                    <span wire:loading.remove wire:target="addTip">@lang('app.save')</span>
                    <span wire:loading wire:target="addTip">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </x-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
