<div>
    <form wire:submit="save" class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                @lang('modules.expenses.category') <span class="text-red-500">*</span>
            </label>
            <div class="mt-1">
                <input type="text" wire:model="name" id="name"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                    placeholder="{{ __('placeholders.enterExpenseCategoryName') }}">
            </div>
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                @lang('modules.expenses.description')
            </label>
            <div class="mt-1">
                <textarea wire:model="description" id="description" rows="3"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                    placeholder="{{ __('placeholders.enterExpenseCategoryDescription') }}"></textarea>
            </div>
            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mt-4 space-x-2">
            <x-button type="submit" wire:loading.attr="disabled">
                @lang('app.save')
            </x-button>
        </div>
    </form>
</div>
