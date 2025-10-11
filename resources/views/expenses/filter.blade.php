<div class="w-full p-4 flex gap-4">

    <div>
        <x-dropdown align="left">
            <x-slot name="trigger">
                <span class="inline-flex rounded-md">
                    <button type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        @lang('modules.expenses.filterExpenseCategory')
                        <div class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500  rounded-md  dark:border-gray-900 ml-1 {{ count($filterCategories) == 0 ? 'hidden' : '' }}">{{ count($filterCategories) }}</div>
                        <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </button>
                </span>
            </x-slot>

            <x-slot name="content">
                <!-- Account Management -->
                <div class="block px-4 py-2 text-sm font-medium text-gray-500">
                    <h6 class="text-sm font-medium text-gray-900 dark:text-white">
                        @lang('modules.expenses.expensesCategory')
                    </h6>
                </div>

                @foreach ($filterExpenseCategories as $key => $expense_category)
                    <x-dropdown-link wire:key='expense_category-cat-{{ $expense_category->id . microtime() }}'>
                        <input id="expense_category-cat-{{ $expense_category->id }}" type="checkbox" value="{{ $expense_category->id }}" wire:model.live='filterCategories' wire:key='expense_category-cat-input-{{ $expense_category->id . microtime() }}'
                        class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-gray-600 focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                        <label for="expense_category-cat-{{ $expense_category->id }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100" wire:key='expense_category-cat-label-{{ $expense_category->id . microtime() }}'>
                        {{ $expense_category->name }}
                        </label>
                    </x-dropdown-link>
                @endforeach

            </x-slot>
        </x-dropdown>
    </div>

    <div>
        <x-dropdown align="left">
            <x-slot name="trigger">
                <span class="inline-flex rounded-md">
                    <button type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        @lang('modules.expenses.filterPaymentMethod')
                        <div class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500  rounded-md  dark:border-gray-900 ml-1 {{ count($filterPaymentMethods) == 0 ? 'hidden' : '' }}">{{ count($filterPaymentMethods) }}</div>
                        <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </button>
                </span>
            </x-slot>

            <x-slot name="content">
                <!-- Account Management -->
                <div class="block px-4 py-2 text-sm font-medium text-gray-500">
                    <h6 class="text-sm font-medium text-gray-900 dark:text-white">
                        @lang('modules.expenses.expensesPaymentMethod')
                    </h6>
                </div>

                @php
                    $paymentMethods = [
                        'cash' => 'modules.expenses.methods.cash',
                        'bank_transfer' => 'modules.expenses.methods.bank_transfer',
                        'credit_card' => 'modules.expenses.methods.credit_card',
                        'debit_card' => 'modules.expenses.methods.debit_card',
                        'check' => 'modules.expenses.methods.check',
                        'digital_wallet' => 'modules.expenses.methods.digital_wallet'
                    ];
                @endphp

                @foreach ($paymentMethods as $key => $method)
                    <x-dropdown-link wire:key='payment_method-{{ $key }}'>
                        <input id="payment_method-{{ $key }}" type="checkbox" value="{{ $key }}" wire:model.live='filterPaymentMethods' wire:key='payment_method-input-{{ $key }}'
                        class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-gray-600 focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                        <label for="payment_method-{{ $key }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100" wire:key='payment_method-label-{{ $key }}'>
                        @lang($method)
                        </label>
                    </x-dropdown-link>
                @endforeach

            </x-slot>
        </x-dropdown>
    </div>


    <div>
        <x-dropdown align="left">
            <x-slot name="trigger">
                <span class="inline-flex rounded-md">
                    <button type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        @lang('modules.expenses.filterDateRange')
                        <div class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-md dark:border-gray-900 ml-1 {{ $filterDateRange ? '' : 'hidden' }}">1</div>
                        <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </button>
                </span>
            </x-slot>

            <x-slot name="content">
                <div class="block px-4 py-2 text-sm font-medium text-gray-500">
                    <h6 class="text-sm font-medium text-gray-900 dark:text-white">
                        @lang('modules.expenses.expensesDateRange')
                    </h6>
                </div>

                <div class="px-4 py-2" @click.stop>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">@lang('modules.expenses.startDate')</label>
                    <input type="date" id="start_date" wire:model.live="filterStartDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 sm:text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300">
                </div>

                <div class="px-4 py-2" @click.stop>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">@lang('modules.expenses.endDate')</label>
                    <input type="date" id="end_date" wire:model.live="filterEndDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 sm:text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300">
                </div>


            </x-slot>
        </x-dropdown>
    </div>


    @if ($clearFilterButton)
        <div wire:key='filter-btn-{{ microtime() }}'>
            <x-danger-button wire:click='clearFilters'>
                <svg aria-hidden="true" class="w-5 h-5 -ml-1 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
                @lang('app.clearFilter')
            </x-danger-button>
        </div>
    @endif

    <div>
        <x-secondary-button wire:click="$toggle('showFilters')">@lang('app.hideFilter')</x-secondary-button>
    </div>

</div>
