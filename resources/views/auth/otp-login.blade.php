<x-auth-layout>
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white text-center">
                {{ __('auth.loginViaOneTimePassword') }}
            </h2>
        </div>

        <x-validation-errors class="mb-4"/>

        @session('status')
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ $value }}
        </div>
        @endsession

        @if(!session('otp_sent'))
            <!-- Email Input Form -->
            <form method="POST" action="{{ route('otp.send') }}">
                @csrf
                <div>
                    <x-label for="email" value="{{ __('app.email') }}"/>
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                             autofocus autocomplete="username"/>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                        ← {{ __('auth.backToLogin') }}
                    </a>
                    <x-button type="submit" class="button">
                        <svg aria-hidden="true" class="hidden inline w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                        {{ __('auth.sendOtp') }}
                    </x-button>
                </div>
            </form>
        @else
            <!-- OTP Verification Form -->
            <div id="otp-success-message" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400 text-center" style="display: none;"></div>
            
            <form method="POST" action="{{ route('otp.verify') }}">
                @csrf
                <input type="hidden" name="email" value="{{ session('email') }}">
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                        {{ __('auth.weveSentA6DigitCodeTo') }}<br>
                        <span class="font-medium">{{ session('email') }}</span>
                    </p>
                </div>

                <div>
                    <x-label for="otp" value="{{ __('auth.enter6DigitCode') }}"/>
                    <div class="mt-1 flex justify-center space-x-2">
                        @for($i = 1; $i <= 6; $i++)
                            <input 
                                type="text" 
                                name="otp[]" 
                                maxlength="1" 
                                class="w-12 h-12 text-center text-lg font-semibold border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white otp-input"
                                data-index="{{ $i }}"
                                autocomplete="off"
                            >
                        @endfor
                    </div>
                    <input type="hidden" name="otp_combined" id="otp_combined">
                </div>

                <div class="flex items-center justify-between mt-6">
                    <button type="button" onclick="resendOtp()" class="text-sm text-blue-600 hover:text-blue-700" id="resendBtn" disabled>
                        {{ __('auth.resendOtp') }} (<span id="countdown">60</span>s)
                    </button>
                    <x-button type="submit" class="button">
                        <svg aria-hidden="true" class="hidden inline w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                        {{ __('auth.verifyOtp') }}
                    </x-button>
                </div>
            </form>
        @endif

        @if(!module_enabled('Subdomain') && !global_setting()->disable_landing_site)
        <div class="flex items-center justify-center mt-4">
            <a href="{{ route('home') }}" class="text-sm text-gray-500 underline underline-offset-1">
                @lang('auth.goHome')
            </a>
        </div>
        @endif
    </div>

    <script>
        // OTP input handling
        document.addEventListener('DOMContentLoaded', function() {
            const otpInputs = document.querySelectorAll('.otp-input');
            const otpCombined = document.getElementById('otp_combined');

            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;
                    
                    // Only allow numbers
                    if (!/^\d*$/.test(value)) {
                        e.target.value = '';
                        return;
                    }

                    // Auto-focus next input
                    if (value && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }

                    // Update combined OTP
                    updateCombinedOtp();
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });
            });

            function updateCombinedOtp() {
                const otp = Array.from(otpInputs).map(input => input.value).join('');
                otpCombined.value = otp;
            }

            // Start countdown for resend
            if (document.getElementById('resendBtn')) {
                startCountdown();
            }
        });

        function startCountdown() {
            let countdown = 60;
            const countdownElement = document.getElementById('countdown');
            const resendBtn = document.getElementById('resendBtn');

            const timer = setInterval(() => {
                countdown--;
                countdownElement.textContent = countdown;

                if (countdown <= 0) {
                    clearInterval(timer);
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = 'Resend OTP';
                }
            }, 1000);
        }

        function resendOtp() {
            const email = '{{ session("email") }}';
            const resendBtn = document.getElementById('resendBtn');
            
            // Disable button and show loading
            resendBtn.disabled = true;
            resendBtn.innerHTML = '{{ __("auth.sending") }}';
            
            fetch('{{ route("otp.resend") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const successMessage = document.getElementById('otp-success-message');
                    successMessage.textContent = '{{ __("auth.newOtpSentSuccessfully") }}';
                    successMessage.style.display = 'block';
                    
                    // Clear OTP inputs
                    const otpInputs = document.querySelectorAll('.otp-input');
                    otpInputs.forEach(input => input.value = '');
                    document.getElementById('otp_combined').value = '';
                    
                    // Focus on first OTP input
                    otpInputs[0].focus();
                    
                    // Start countdown again
                    startCountdown();
                    
                    // Hide success message after 3 seconds
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 3000);
                } else {
                    alert('{{ __("auth.failedToResendOtp") }}');
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = '{{ __("auth.resendOtp") }}';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __("auth.failedToResendOtp") }}');
                resendBtn.disabled = false;
                resendBtn.innerHTML = '{{ __("auth.resendOtp") }}';
            });
        }

        // Button loading state
        document.querySelector('.button').addEventListener('click', function() {
            const button = this;
            const inputs = document.querySelectorAll('input[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.checkValidity() || !input.value) {
                    isValid = false;
                }
            });

            if (isValid) {
                button.classList.add('opacity-50', 'cursor-not-allowed');
                button.innerHTML = `<svg aria-hidden="true" class="inline w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" xmlns="http://www.w3.org/2000/svg">
                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                </svg> Loading...`;
            }
        });
    </script>
</x-auth-layout> 