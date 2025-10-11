<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register("{{ asset('service-worker.js') }}")
                .then(registration => {
                    console.log('Service Worker registered:', registration);
                })
                .catch(error => {
                    console.log('Service Worker registration failed:', error);
                });
        });
    }
    @if ((restaurant() && restaurant()->is_pwa_install_alert_show == 1) || (!restaurant() && global_setting()->is_pwa_install_alert_show == 1))
        var isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
        var isAndroid = /Android/i.test(navigator.userAgent);
        var isInStandaloneMode = ('standalone' in window.navigator) && window.navigator.standalone;

        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            deferredPrompt = event;

            // Prevent repeated prompts in the same session
            if (!sessionStorage.getItem("pwaDismissed")) {
                setTimeout(() => {
                    if (isAndroid) {
                        showInstallPrompt();
                    }
                }, 1000);
            }
        });
        function showInstallPrompt() {
            if (deferredPrompt) {
                deferredPrompt.prompt();

                deferredPrompt.userChoice.then(({ outcome }) => {
                    console.log(`User ${outcome === 'accepted' ? 'accepted' : 'dismissed'} the PWA install`);

                    if (outcome === 'dismissed') {
                        sessionStorage.setItem("pwaDismissed", "true");
                    }

                    deferredPrompt = null;
                });
            }
        }

        if (isIOS && !isInStandaloneMode) {
            // Show instruction only once, on scroll or click
            const showInstructionsOnce = () => {
                showIOSInstallInstructions();
                window.removeEventListener('scroll', showInstructionsOnce);
                window.removeEventListener('click', showInstructionsOnce);
            };

            window.addEventListener('scroll', showInstructionsOnce, { once: true });
            window.addEventListener('click', showInstructionsOnce, { once: true });
        }
        // ---------------------- Function to Show iOS Instructions ----------------------

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

            document.getElementById('closeInstructions').addEventListener('click', function () {
                document.getElementById('iosInstallInstructions').remove();
            });
        }
    @endif
</script>
