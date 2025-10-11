<div class="card">
    <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg shadow-lg p-8 border dark:border-gray-700 w-full">
        <div class="space-y-4">
            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">@lang('modules.expenses.expenseTitle')</strong>
                <span>{{ $expense_title }}</span>
            </p>
            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">@lang('modules.expenses.expenseCategory')</strong>
                <span>{{ $expense_category }}</span>
            </p>
            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">@lang('modules.expenses.expenseAmount')</strong>
                <span>${{ number_format($amount, 2) }}</span>
            </p>
            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">@lang('modules.expenses.expenseTitle')</strong>
                <span>{{ $expense_date }}</span>
            </p>
            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">@lang('modules.expenses.paymentStatus'):</strong>
                <span
                    class="px-4 py-1 text-sm font-semibold rounded-full
                    {{ $payment_status == 'paid' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                    {{ ucfirst($payment_status) }}
                </span>
            </p>
            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">@lang('modules.expenses.paymentDate'):</strong>
                <span>{{ $payment_date ?? 'N/A' }}</span>
            </p>
            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">@lang('modules.expenses.paymentDueDate'):</strong>
                <span>{{ $payment_due_date ?? 'N/A' }}</span>
            </p>

            @if ($payment_method)
                <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                    <strong class="text-gray-600 dark:text-gray-400">@lang('modules.expenses.paymentMethod'):</strong>
                    <span>@lang('modules.expenses.methods.' . $payment_method)</span>
                </p>
            @endif
            <p class="border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">@lang('modules.expenses.description'):</strong> <br>
                <span class="block mt-1 text-gray-700 dark:text-gray-300">{{ $description ?? 'No description provided' }}</span>
            </p>

            <!-- Receipt Preview -->
            @if ($existingReceiptUrl)
                <div class="mt-4">
                    <strong class="text-gray-600 dark:text-gray-400">@lang('modules.expenses.receiptPreview'):</strong>
                    <div class="mt-2">
                        @if (Str::endsWith($existingReceiptUrl, ['.jpg', '.jpeg', '.png', '.gif']))
                            <img src="{{ $existingReceiptUrl }}" alt="Expense Receipt"
                                class="rounded-lg shadow-md w-32 h-auto border dark:border-gray-700">
                        @endif
                        @if (Str::endsWith($existingReceiptUrl, ['.pdf']))
                            <img src="{{ asset('/img/receipt icon.jpg') }}" alt="Expense Receipt"
                                class="rounded-lg shadow-md w-32 h-auto border dark:border-gray-700">
                        @endif
                    </div>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 mt-4">@lang('modules.expenses.noReceiptAvailable')</p>
            @endif
        </div>
    </div>
</div>
