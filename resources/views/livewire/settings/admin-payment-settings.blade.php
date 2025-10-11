<div>
    <div
        class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    <h2 class="mb-4 text-xl font-semibold">@lang('modules.settings.adminSettings')</h2>



        <div class="space-y-4">
            @foreach ($gateways as $gateway)
                <div class="flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-700 p-2 rounded-md">
                        <span class="flex items-center gap-3 font-semibold text-lg {{ !$settings[$gateway] ? 'text-gray-400 dark:text-gray-500' : 'text-gray-800 dark:text-gray-100' }}">
                        <span class="flex items-center justify-center w-8 h-8">
                            {!! $gatewayLogos[$gateway] ?? '' !!}
                        </span>
                        {{ ucfirst($gateway) }}
                    </span>
                    <label for="checkbox_{{ $gateway }}" class="relative flex items-center cursor-pointer">
                        <input type="checkbox"
                            id="checkbox_{{ $gateway }}"
                            wire:model.live="settings.{{ $gateway }}"
                    class="sr-only" />
                        <span
                    class="h-6 bg-gray-200 border border-gray-200 rounded-full w-11 toggle-bg dark:bg-gray-700 dark:border-gray-600"></span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>
