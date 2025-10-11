<div>
    <div>

        <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
            <div class="w-full mb-1">
                <div class="mb-4">
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('menu.customers')</h1>
                </div>
                <div class="items-center justify-between block sm:flex ">
                    <div class="flex items-center mb-4 sm:mb-0">
                        <form class="sm:pr-3" action="#" method="GET">
                            <label for="products-search" class="sr-only">Search</label>
                            <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                                <x-input id="menu_name" class="block mt-1 w-full" type="text"
                                    placeholder="{{ __('placeholders.searchCustomers') }}"
                                    wire:model.live.debounce.500ms="search" />
                            </div>
                        </form>
                    </div>
                    <div class="flex items-center space-x-2">
                        {{-- <x-button type='button' wire:click="$set('showImportCustomer', true)">@lang('app.import')</x-button> --}}
                        <a wire:click="$set('showImportCustomer', true)"
                            class="inline-flex items-center justify-center cursor-pointer px-3 py-2 text-sm font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M20 14V8l-6-6H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-4h-7v3l-5-4 5-4v3h7zM13 4l5 5h-5V4z">
                                </path>
                            </svg> @lang('app.import')
                        </a>



                        <a wire:click="exportCustomerList"
                            class="inline-flex items-center justify-center cursor-pointer px-3 py-2 text-sm font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            @lang('app.export')
                        </a>

                        @if (user_can('Create Customer'))
                            <x-button type='button'
                                wire:click="$set('showAddCustomer', true)">@lang('modules.customer.addCustomer')</x-button>
                        @endif
                    </div>

                </div>
            </div>

        </div>

        <livewire:customer.customer-table :search='$search' key='customer-table-{{ microtime() }}' />


    </div>
    <!-- Product Drawer -->
    <x-right-modal wire:model.live="showAddCustomer">
        <x-slot name="title">
            {{ __('modules.customer.addCustomer') }}
        </x-slot>

        <x-slot name="content">
            @livewire('forms.add-customer-form')
        </x-slot>
    </x-right-modal>


    @props(['id' => null, 'maxWidth' => null])

    <x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }} wire:model="showImportCustomer" wire:close="closeImportCustomer">
    <div class="px-6 py-4">
        <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('modules.customer.importCustomer') }}
        </div>

      <a href="{{ asset('sample-files/customers.xlsx') }}" download
    class="inline-flex items-center justify-center cursor-pointer px-3 py-2 text-sm font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
    <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
            d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
            clip-rule="evenodd"></path>
    </svg>
    @lang('app.downloadSample')
</a>


        <form wire:submit.prevent="importCustomerList" class="flex items-center space-x-2">
            <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                <input type="file" wire:model="file" accept=".xlsx,.xls,.csv" id="file"
                    class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer focus:outline-none dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    x-data="{ resetFile() { this.value = ''; } }"
                    x-on:reset-file-input.window="resetFile()">
                @error('file') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success text-red-500 p-4 mb-4">{{ session('message') }}</div>
        @endif

        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 dark:bg-gray-800 text-end">
            <x-secondary-button wire:click="closeImportCustomer" class="mr-2">
                @lang('app.cancel')
            </x-secondary-button>
            <x-button type="submit"> @lang('app.import') </x-button>
        </div>
        </form>
    </x-modal>


</div>
