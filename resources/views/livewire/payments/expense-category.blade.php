<div>

    <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('modules.expenses.expensesCategory')</h1>
            </div>
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <form class="sm:pr-3" action="#" method="GET">
                        <label for="products-search" class="sr-only">Search</label>
                        <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                            {{-- <x-input id="expense" class="block mt-1 w-full" type="text" placeholder="{{ __('placeholders.expense') }}" wire:model.live.debounce.500ms="search"  /> --}}
                            <x-input id="expense" class="block mt-1 w-full" type="text"
                                placeholder="{{ __('placeholders.expensesCategory') }}"
                                wire:model.live.debounce.500ms="search" />
                        </div>
                    </form>
                </div>

                @if (user_can('Create Expense'))
                    <x-button type='button' wire:click="showAddExpenseCategory">
                        @lang('modules.expenses.addExpense')
                    </x-button>
                @endif

            </div>
        </div>
    </div>
    <div>


        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow">
                        <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="py-2.5 px-4 text-xs font-medium text-start text-gray-500 uppercase dark:text-gray-400">
                                        @lang('modules.expenses.category')
                                    </th>
                                    <th
                                        class="py-2.5 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">
                                        @lang('modules.expenses.description')
                                    </th>
                                    <th
                                        class="py-2.5 px-4 text-xs font-medium text-end text-gray-500 uppercase dark:text-gray-400">
                                        @lang('app.action')
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @if ($expenseCategories->count() > 0)
                                    @foreach ($expenseCategories as $expenseCategory)
                                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">

                                            <td class="py-2.5 px-4 text-gray-900 text-start dark:text-white text-center">
                                                {{ $expenseCategory->name }}</td>
                                            <td class="py-2.5 px-4 text-gray-900 dark:text-white text-center">
                                                {{ $expenseCategory->description }}</td>
                                            <td class="py-2.5 px-4 space-x-2 text-end">
                                                {{-- Action buttons --}}
                                                @if (user_can('Update Expense Category'))
                                                    <x-secondary-button-table
                                                        wire:click="showEditExpenseCategory({{ $expenseCategory->id }})"
                                                        wire:key="edit-expenseCategory-button-{{ $expenseCategory->id }}">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
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
                                                @if (user_can('Delete Expense Category'))
                                                    <x-danger-button-table
                                                        wire:click="showDeleteExpenseCategory({{ $expenseCategory->id }})"
                                                        wire:key="delete-expenseCategory-button-{{ $expenseCategory->id }}">
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
                {{ $expenseCategories->links() }}
            </div>
        </div>


        <x-confirmation-modal wire:model.live="confirmDeleteExpenseCategory">
            <x-slot name="title">
                @lang('modules.expenses.deleteExpenseCategory')?
            </x-slot>

            <x-slot name="content">
                @lang('modules.expenses.deleteExpensesCategoryMessage')
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmDeleteExpenseCategory')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-secondary-button>

                @if ($deleteExpenseCategory)
                    <x-danger-button class="ml-3" wire:click='deleteExpenseData({{ $deleteExpense }})'
                        wire:loading.attr="disabled">
                        {{ __('Delete') }}
                    </x-danger-button>
                @endif
            </x-slot>
        </x-confirmation-modal>


    </div>

    <x-right-modal wire:model.live="showEditExpenseCategoryModal">
        <x-slot name="title">
            {{ __('modules.expenses.editExpenseCategory') }}
        </x-slot>

        <x-slot name="content">
            @if ($selectedExpenseCategory)
                @livewire('forms.edit-expense-category', ['expenseCategory' => $selectedExpenseCategory], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">

        </x-slot>
    </x-right-modal>
    <!-- Product Drawer -->
    <x-right-modal wire:model.live="showAddExpenseCategoryModal">
        <x-slot name="title">
            {{ __('modules.expenses.addExpenseCategory') }}
        </x-slot>

        <x-slot name="content">
            @livewire('forms.add-expense-category')
        </x-slot>
    </x-right-modal>

</div>
