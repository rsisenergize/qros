<!DOCTYPE html>
<html lang="{{ session('locale') ?? $restaurant->customer_site_language }}" dir="{{ session('is_rtl') ? 'rtl' : 'ltr' }}">

<head>
    <link rel="manifest" href="{{ url('manifest.json') }}?url={{ urlencode(ltrim(Request::getRequestUri(), '/')) }}&hash={{ $restaurant->hash }}" crossorigin="use-credentials">
    <meta name="theme-color" content="#ffffff">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="apple-touch-icon" sizes="180x180" href="{{ $restaurant->uploadFavIconAppleTouchIconUrl }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ $restaurant->uploadFavIconAndroidChrome192Url }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ $restaurant->uploadFavIconAndroidChrome512Url }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $restaurant->uploadFavIcon16Url }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $restaurant->uploadFavIcon32Url }}">
    <link rel="shortcut icon" href="{{ $restaurant->faviconUrl }}">

    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ $restaurant->logoUrl }}">


    <meta name="keyword" content="{{ $restaurant->meta_keyword ?? '' }}">
    <meta name="description" content="{{ $restaurant->meta_description ?? $restaurant->name }}">
    <title>{{ $restaurant->name }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <style>
        :root {
            /* --color-base: 219, 41, 41; */
            --color-base: {{ $restaurant->theme_rgb }};
            --livewire-progress-bar-color: {{ $restaurant->theme_hex }};
        }
    </style>
    <style>
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-slide-in { animation: slideIn 0.3s ease-out; }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
    </style>

    @if (File::exists(public_path() . '/css/app-custom.css'))
        <link href="{{ asset('css/app-custom.css') }}" rel="stylesheet">
    @endif

    {{-- Include file for widgets if exist --}}
    @includeIf('sections.custom_script_customer')

</head>
<body class="bg-white font-['Inter']">

    <livewire:kiosk::kiosk.order-confirmation :restaurant="$restaurant" :shopBranch="$shopBranch" :order="$order" />

    @livewireScripts

    <x-livewire-alert::flash />
    @include('sections.pusher-script')

    @stack('scripts')

</body>
</html>
