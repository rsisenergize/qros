<div>

    <div>
        <form wire:submit.prevent="update" class="space-y-4">
            <!-- Kitchen Name -->
            <div>
                <label for="kitchenName" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    @lang('kitchen::modules.menu.kitchenName') <span class="text-red-500">*</span>
                </label>
                <div class="mt-1">
                    <input type="text" wire:model="kitchenName" id="kitchenName"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                        placeholder="{{ __('kitchen::placeholders.enterKitchenName') }}">
                </div>
                @error('kitchenName')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    @lang('kitchen::modules.menu.description')
                </label>
                <div class="mt-1">
                    <textarea wire:model="description" id="description" rows="3"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                        placeholder="{{ __('kitchen::placeholders.enterKitchenDescription') }}"></textarea>
                </div>
                @error('description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Printer Name -->
            <div>
                <label for="printer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    @lang('kitchen::modules.menu.selectPrinter') <span class="text-red-500">*</span>
                </label>
                <div class="mt-1">
                    <select wire:model="printer_name" id="printer_name"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                        <option value="">{{ __('kitchen::modules.menu.selectPrinter') }}</option>
                        @foreach ($availablePrinters as $printer)
                            <option value="{{ $printer->id }}">{{ $printer->name }} {{ $printer->type == 'windows' ? '('.$printer->share_name.')' : '' }} - {{ $printer->printing_choice }}</option>
                        @endforeach
                    </select>
                </div>
                @error('printer_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Is Active -->


            <!-- Submit Button -->
            <div class="mt-4 flex justify-end space-x-2">
                <x-button type="submit" wire:loading.attr="disabled">
                    @lang('kitchen::modules.menu.save')
                </x-button>
            </div>
        </form>
    </div>


</div>
