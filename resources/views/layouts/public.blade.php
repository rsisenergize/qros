<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ isRtl() ? 'rtl' : 'ltr' }}">

<head>
   @php
        $lastSegment = last(request()->segments());
    @endphp
    @if (user()->restaurant_id)
        <link rel="manifest" href="{{ asset('manifest.json') }}@if($lastSegment)?url={{ $lastSegment }}&hash={{ user()->restaurant->hash }}@endif" crossorigin="use-credentials">
    @else
        <link rel="manifest" href="{{ asset('manifest.json') }}@if($lastSegment)?url={{ $lastSegment }}@endif" crossorigin="use-credentials">
    @endif
    <meta name="theme-color" content="#ffffff">
    <meta name="description" content="{{ global_setting()->name }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css" />


    <link rel="apple-touch-icon" sizes="180x180" href="{{ restaurantOrGlobalSetting()->upload_fav_icon_apple_touch_icon_url }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ restaurantOrGlobalSetting()->upload_fav_icon_android_chrome_192_url }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ restaurantOrGlobalSetting()->upload_fav_icon_android_chrome_512_url }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ restaurantOrGlobalSetting()->upload_favicon_16_url }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ restaurantOrGlobalSetting()->upload_favicon_32_url }}">
    <link rel="shortcut icon" href="{{ restaurantOrGlobalSetting()->favicon_url }}">


    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ global_setting()->logoUrl }}">

    <title>{{ global_setting()->name }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    @stack('styles')

    @include('sections.theme_style', [
        'baseColor' => restaurantOrGlobalSetting()->theme_rgb,
        'baseColorHex' => restaurantOrGlobalSetting()->theme_hex,
    ])


    @if (File::exists(public_path() . '/css/app-custom.css'))
        <link href="{{ asset('css/app-custom.css') }}" rel="stylesheet">
    @endif


    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>


    {{-- Include file for widgets if exist --}}
    @includeIf('sections.custom_script_admin')
</head>


<body class="font-sans antialiased dark:bg-gray-900" id="main-body">


    <div class="flex rtl:flex-row-reverse  overflow-hidden bg-gray-50 dark:bg-gray-900 h-screen">

        <div id="main-content"
            class="relative w-full h-full overflow-y-auto bg-gray-50  dark:bg-gray-900">
            <main>
                @yield('content')
                {{ $slot ?? '' }}
            </main>


        </div>


    </div>

    @stack('modals')


    @livewireScripts

    @include('layouts.update-uri')


    <script src="{{ asset('vendor/livewire-alert/livewire-alert.js') }}" defer data-navigate-track></script>
    <x-livewire-alert::flash />


    <script>
        var elem = document.getElementById("main-body");

        function openFullscreen() {
            if (!document.fullscreenElement) {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    /* Safari */
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    /* IE11 */
                    elem.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    /* Safari */
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    /* IE11 */
                    document.msExitFullscreen();
                }
            }
        }
    </script>

    @include('layouts.service-worker-js')
    @include('sections.pusher-script')
    @stack('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>

</body>

</html>
