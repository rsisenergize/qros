<div class="flex justify-center items-center">
    <x-secondary-link wire:click="printKotThermal"
        class="flex items-center px-9 py-6 rounded-lg transition duration-300
        bg-blue-500 text-white hover:bg-blue-600
        dark:bg-blue-700 dark:text-gray-200 dark:hover:bg-blue-800">
        <div wire:loading class="mr-2">
            <span class="loader"></span>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-7 text-gray-800 dark:text-gray-200" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
        </svg>
        <span class="ml-2 text-gray-800 dark:text-gray-200">Print</span>
    </x-secondary-link>
</div>
