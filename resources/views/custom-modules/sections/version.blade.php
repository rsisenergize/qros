@php
    $version = \Illuminate\Support\Facades\File::get($module->getPath() . '/version.txt');
    $latestVersion = $plugins->where('envato_id', $envatoId)->pluck('version')->first();
@endphp
@if ($plugins->where('envato_id', $envatoId)->first())
    @if ($latestVersion > $version)

        <span class="bg-red-200 uppercase text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300 cursor-help"
              x-data="{ tooltip: false }"
              x-on:mouseenter="tooltip = true"
              x-on:mouseleave="tooltip = false"
              x-on:focus="tooltip = true"
              x-on:blur="tooltip = false">
            {{ $version }}

            <div x-show="tooltip"
                 class="absolute z-50 p-2 mt-2 text-sm text-white bg-gray-900 rounded-lg shadow-lg whitespace-normal min-w-[200px] max-w-[300px] break-words"
                 x-cloak
                 role="tooltip">
                @lang('app.moduleUpdateMessage', [
                    'name' => $module->getName(),
                    'version' => $latestVersion,
                ])
            </div>
        </span>
    @else
        <span class="bg-green-100 uppercase text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
            {{ $version }}
        </span>
    @endif
@else
    <span class="bg-green-100 uppercase text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
        {{ $version }}
    </span>
@endif
