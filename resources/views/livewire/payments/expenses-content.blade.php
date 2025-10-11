<div>

    <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('modules.expenses.expenses')</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <form class="sm:pr-3" action="#" method="GET">
                        <label for="products-search" class="sr-only">Search</label>
                        <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                            {{-- <x-input id="expense" class="block mt-1 w-full" type="text" placeholder="{{ __('placeholders.expense') }}" wire:model.live.debounce.500ms="search"  /> --}}
                            <x-input id="expense" class="block mt-1 w-full" type="text"
                                placeholder="{{ __('placeholders.expense') }}"
                                wire:model.live.debounce.500ms="search" />
                        </div>
                    </form>

                    <x-secondary-button wire:click="$dispatch('showExpensesFilters')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-filter mr-1" viewBox="0 0 16 16">
                            <path
                                d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5" />
                        </svg> @lang('app.showFilter')
                    </x-secondary-button>
                </div>

                @if (user_can('Create Expense'))
                    <x-button type='button' wire:click="$set('showAddExpenses', true)">
                        @lang('modules.expenses.addExpense')
                    </x-button>
                @endif

            </div>
        </div>
    </div>

    <livewire:payments.expenses :search='$search' key='expenses-{{ microtime() }}' />


    <!-- Product Drawer -->
    <x-right-modal wire:model.live="showAddExpenses">
        <x-slot name="title">
            {{ __('modules.expenses.addExpense') }}
        </x-slot>

        <x-slot name="content">
            @livewire('forms.add-expenses')
        </x-slot>
    </x-right-modal>

</div>
