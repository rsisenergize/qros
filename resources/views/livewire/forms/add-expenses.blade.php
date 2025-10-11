<div>
    <form wire:submit.prevent="save" class="space-y-4">
         <!-- Expense Title -  -->
        <div>
            <label for="expense_title" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
            @lang('modules.expenses.title') <span class="text-red-500">*</span>
            </label>
            <div class="mt-1">
            <input type="text" wire:model="expense_title" id="expense_title"
                placeholder="{{ __('placeholders.addTitle') }}"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
            </div>
            @error('expense_title')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Expense Category -  -->
        <div>
            <label for="expense_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                @lang('modules.expenses.category') <span class="text-red-500">*</span>
            </label>
            <div class="mt-1 flex">
                <select wire:model="expense_category_id" id="showExpenseCategoryModals"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                    <option value="">@lang('modules.expenses.selectCategory')</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}
                        </option>
                    @endforeach

                </select>
                <button type="button"
                    class="ml-2 inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                    wire:click="$toggle('showExpenseCategoryModal')">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M12 5l0 14"></path>
                        <path d="M5 12l14 0"></path>
                    </svg>
                </button>
            </div>


            @error('expense_category_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>


        <!-- Amount  -->
        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                @lang('modules.expenses.amount') <span class="text-red-500">*</span>
            </label>
            <div class="mt-1">
                <input type="number" step="0.01" wire:model="amount" id="amount"
                    placeholder="{{ __('placeholders.addAmount') }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
            </div>
            @error('amount')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- All Dates -->

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Expense Date -->
            <div>
                <label for="expense_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    @lang('modules.expenses.expenseDate') <span class="text-red-500">*</span>
                </label>
                <div class="mt-1">
                    <input type="date" wire:model="expense_date" id="expense_date"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                        onclick="this.showPicker()">
                </div>
                @error('expense_date')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Payment Date -->
            <div>
                <label for="payment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    @lang('modules.expenses.paymentDate')
                </label>
                <div class="mt-1">
                    <input type="date" wire:model="payment_date" id="payment_date"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                        onclick="this.showPicker()">
                </div>
                @error('payment_date')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Payment Due Date -->
            <div>
                <label for="payment_due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    @lang('modules.expenses.paymentDueDate')
                </label>
                <div class="mt-1">
                    <input type="date" wire:model="payment_due_date" id="payment_due_date"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                        onclick="this.showPicker()">
                </div>
                @error('payment_due_date')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Payment Method and Status in Two Columns -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <!-- Payment Status -->
            <div>
                <label for="payment_status" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    @lang('modules.expenses.paymentStatus') <span class="text-red-500">*</span>
                </label>
                <div class="mt-1">
                    <select wire:model.live="payment_status" id="payment_status"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                        <option value="pending">@lang('modules.expenses.status.pending')</option>
                        <option value="paid">@lang('modules.expenses.status.paid')</option>
                    </select>
                </div>
                @error('payment_status')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <!-- Payment Method -->
            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    @lang('modules.expenses.paymentMethod')
                    @if ($payment_status === 'paid')
                        <span class="text-red-500">*</span>
                    @endif
                </label>
                <div class="mt-1">
                    <select wire:model="payment_method" id="payment_method"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                        <option value="">@lang('app.select')</option>
                        @foreach ($paymentMethods as $key => $method)
                            <option value="{{ $key }}">@lang($method)</option>
                        @endforeach
                    </select>
                </div>
                @error('payment_method')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>


        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                @lang('modules.expenses.description')
            </label>
            <div class="mt-1">
                <textarea wire:model="description" id="description" rows="3"
                    placeholder="{{ __('placeholders.addDescription') }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"></textarea>
            </div>
            @error('description')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Receipt Upload -->

        <div x-data="{
            preview: null,
            isImage: false
        }">
            <label for="expense_receipt" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                @lang('modules.expenses.receipt')
            </label>

            <div class="mt-1">
                <input type="file" wire:model="expense_receipt" id="expense_receipt"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md
    file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700
    hover:file:bg-primary-100 dark:file:bg-gray-700 dark:file:text-gray-200"
                    x-ref="fileInput"
                    x-on:change="
    const file = $refs.fileInput.files[0];
    if (file) {
        const fileType = file.type;
        isImage = fileType.startsWith('image/');
        if (isImage) {
            const reader = new FileReader();
            reader.onload = (e) => { preview = e.target.result };
            reader.readAsDataURL(file);
        } else {
            preview = null;
        }
    }
    " />
            </div>

            @error('expense_receipt')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror

            <!-- Preview -->
            <div class="mt-3" x-show="preview && isImage">
                <p class="text-sm text-gray-500">@lang('modules.expenses.receiptPreview')</p>
                <img x-bind:src="preview"
                    class="mt-2 h-24 w-24 rounded-lg object-contain border dark:border-gray-700">
            </div>
        </div>



        <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel wire:click="$dispatch('hideAddExpenses')">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>

    <x-dialog-modal wire:model.live="showExpenseCategoryModal" maxWidth="xl">
        <x-slot name="title">
            @lang('modules.expenses.addCategory')
        </x-slot>

        <x-slot name="content">
            @livewire('forms.AddExpenseCategory')
        </x-slot>

        {{-- <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('showExpenseCategoryModal')"
                wire:loading.attr="disabled">@lang('app.cancel')</x-secondary-button>
        </x-slot> --}}
    </x-dialog-modal>

    <script>
        // This ensures clicking anywhere on the input field opens the date picker
        document.getElementById('expense_date').addEventListener('click', function() {
            this.showPicker();
        });

        document.getElementById('payment_date').addEventListener('click', function() {
            this.showPicker();
        });

        document.getElementById('payment_due_date').addEventListener('click', function() {
            this.showPicker();
        });
    </script>
</div>
