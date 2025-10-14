<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('inventory::modules.reports.title') }}
            </h2>
            <x-inventory::reports.tabs />
        </div>
    </x-slot>

    <div >
        <div class="mx-auto sm:px-6 lg:px-8">
            @livewire('inventory::reports.profit-and-loss-report')
        </div>
    </div>
</x-app-layout> 