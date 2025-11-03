@extends('layouts.app')

@section('content')

    <div class="bg-white dark:bg-gray-800 rounded-lg m-4">
        <header class="border-b border-gray-200 dark:border-gray-600 py-4">
            <div class="flex flex-col items-start gap-1 px-3 md:px-6">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">@lang('modules.menu.editMenuItem')</h1>
                <x-help-text>@lang('modules.menu.editMenuItemHelp')</x-help-text>
            </div>
        </header>

        <div class="p-4 sm:p-7">
            @livewire('forms.updateMenuItem', ['menuItemId' => $menuItemId])
        </div>
    </div>

@endsection
