@php
    // Set locale for authenticated users
    if (session('locale')) {
        \Illuminate\Support\Facades\App::setLocale(session('locale'));
    } else {
        $user = auth()->user();
        if (isset($user)) {
            \Illuminate\Support\Facades\App::setLocale($user?->locale ?? 'en');
        } else {
            try {
                \Illuminate\Support\Facades\App::setLocale(session('locale') ?? global_setting()?->locale ?? 'en');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\App::setLocale('en');
            }
        }
    }
@endphp
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
            @livewire('inventory::reports.forecasting-report')
        </div>
    </div>
</x-app-layout> 