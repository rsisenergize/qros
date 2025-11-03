<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-300  shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150']) }}
    wire:loading.attr="disabled" wire:target="{{ $target ?? 'submitForm' }}" wire:loading.class.remove="bg-gray-50 dark:bg-gray-700"
    wire:loading.class="bg-gray-50 dark:bg-gray-700"
    >
    {{ $slot }}
</button>
