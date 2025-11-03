@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    @lang('modules.modifier.addModifierGroup')
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    @lang('modules.modifier.addModifierGroupDescription')
                </p>
            </div>

            @livewire('forms.create-modifier-group')
        </div>
    </div>
@endsection
