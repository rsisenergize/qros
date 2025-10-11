<!DOCTYPE html>
<html lang="{{ session('customer_locale') ?? global_setting()->locale }}" dir="{{ session('customer_is_rtl') ? 'rtl' : 'ltr' }}">

<head>
    <link rel="manifest" href="{{ asset('manifest.json') }}" crossorigin="use-credentials">

    <meta name="theme-color" content="#ffffff">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ restaurantOrGlobalSetting()->upload_fav_icon_apple_touch_icon_url }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ restaurantOrGlobalSetting()->upload_fav_icon_android_chrome_192_url }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ restaurantOrGlobalSetting()->upload_fav_icon_android_chrome_512_url }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ restaurantOrGlobalSetting()->upload_favicon_16_url }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ restaurantOrGlobalSetting()->upload_favicon_32_url }}">
    <link rel="shortcut icon" href="{{ restaurantOrGlobalSetting()->favicon_url }}">

    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ restaurantOrGlobalSetting()->upload_fav_icon_apple_touch_icon_url }}">

    <title>{{ global_setting()->name }}</title>

    <meta name="keyword" content="{{ global_setting()->meta_keyword ?? '' }}">
    <meta name="description" content="{{ global_setting()->meta_description ?? global_setting()->name }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    @include('sections.theme_style', [
        'baseColor' => $globalSetting->theme_rgb,
        'baseColorHex' => $globalSetting->theme_hex,
    ])

    <script>
        if (localStorage.getItem('color-theme') === 'dark') {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    {{-- Include file for widgets if exist --}}
    @includeIf('sections.custom_script_admin')
</head>

<body>
    <div class="font-sans text-gray-900 dark:text-gray-100 antialiased">
        <button id="theme-toggle" data-tooltip-target="tooltip-toggle" type="button"
            class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 absolute top-2 right-2">
            <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
            <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                    fill-rule="evenodd" clip-rule="evenodd"></path>
            </svg>
        </button>
        <div id="tooltip-toggle" role="tooltip"
            class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
            Toggle dark mode
            <div class="tooltip-arrow" data-popper-arrow></div>
        </div>

        <div
            class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div class="flex flex-col justify-center items-center space-y-4">
                <a class="flex gap-2 items-center text-xl font-medium dark:text-white app-logo">
                    <img src="{{ $appTheme->logoUrl }}" class="h-8" alt="{{ global_setting()->name }} Logo" />
                    @if ($appTheme->show_logo_text)
                        {{ $appTheme->name }}
                    @endif
                </a>


            </div>
            {{ $slot }}
            @if (languages()->count() > 1)
    <div class="mt-4">
        @livewire('shop.languageSwitcher')
    </div>
    @endif
        </div>
    </div>

    @livewireScripts
    @include('layouts.update-uri')
</body>

</html>
@if (global_setting()->is_pwa_install_alert_show == 1)
    <script>
        (function () {
            let deferredPrompt = null;

            const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
            const isInStandaloneMode = ('standalone' in window.navigator) && window.navigator.standalone;

            // Handle Android PWA Install Prompt
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                // Prevent showing again if user has dismissed in this tab
                if (!sessionStorage.getItem("pwaDismissed")) {
                    ['scroll', 'click'].forEach(evt => {
                        window.addEventListener(evt, showInstallPrompt, { once: true });
                    });
                }
            });

            function showInstallPrompt() {
                if (deferredPrompt) {
                    deferredPrompt.prompt(); // Show the install prompt

                    deferredPrompt.userChoice.then(({ outcome }) => {
                        console.log(`User ${outcome === 'accepted' ? 'accepted' : 'dismissed'} the PWA install`);

                        if (outcome === 'dismissed') {
                            sessionStorage.setItem("pwaDismissed", "true"); // Prevent showing again in this session
                        }

                        deferredPrompt = null;
                    });
                }
            }

            // Show install prompt on first user interaction
            ['scroll', 'click'].forEach(event => {
                window.addEventListener(event, showInstallPrompt, { once: true });
            });

            // Handle iOS PWA Install Instruction
            if ((isIOS && !isInStandaloneMode) || deferredPrompt) {
                const lastPrompt = localStorage.getItem('iosPromptLastShown');
                const now = new Date().getTime();

                if (!lastPrompt || (now - parseInt(lastPrompt)) > 24 * 60 * 60 * 1000) {
                    ['scroll', 'click'].forEach(event => {
                        window.addEventListener(event, showIOSInstallInstructions, { once: true });
                    });
                }
            }

            function showIOSInstallInstructions() {
                if (document.getElementById('iosInstallInstructions')) return;
                localStorage.setItem('iosPromptLastShown', new Date().getTime());

                const instructions = document.createElement('div');
                instructions.id = 'iosInstallInstructions';
                instructions.innerHTML = `
                    <div style="position: fixed; bottom: 10px; left: 10px; right: 10px; background: #fff; padding: 10px; border: 1px solid #ccc; border-radius: 5px; text-align: center; z-index: 1000;">
                        <p class="flex relative">@lang('messages.installAppInstruction')
                            <img class="absolute right-0 left-auto mr-5" src="{{ asset('img/share-ios.svg') }}" alt="Share Icon" style="width: 20px; vertical-align: middle;">

                        </p>
                        @lang('messages.addToHomeScreen').
                        <button id="closeInstructions" class="block text-center mx-auto" style="margin-top: 10px; padding: 5px 10px;">@lang('app.close')</button>
                    </div>
                `;

                document.body.appendChild(instructions);

                // Close button functionality
                document.getElementById('closeInstructions').addEventListener('click', () => {
                    instructions.remove();
                });
            }
        })
    </script>
@endif


