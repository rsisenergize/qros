<x-auth-layout>
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">

        <div x-data="{ recovery: false }">
            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400" x-show="! recovery">
                {{ __('messages.accountRecoveryAuthMessage') }}
            </div>

            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400" x-cloak x-show="recovery">
                {{ __('messages.accountRecoveryCode') }}
            </div>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('two-factor.login') }}">
                @csrf

                <div class="mt-4" x-show="! recovery">
                    <x-label for="code" value="{{ __('modules.profile.code') }}" />
                    <x-input id="code" class="block mt-1 w-full" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code" />
                </div>

                <div class="mt-4" x-cloak x-show="recovery">
                    <x-label for="recovery_code" value="{{ __('modules.profile.recoveryCode') }}" />
                    <x-input id="recovery_code" class="block mt-1 w-full" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button type="button" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 underline cursor-pointer"
                                    x-show="! recovery"
                                    x-on:click="
                                        recovery = true;
                                        $nextTick(() => { $refs.recovery_code.focus() })
                                    ">
                        {{ __('modules.profile.useRecoveryCode') }}
                    </button>

                    <button type="button" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 underline cursor-pointer"
                                    x-cloak
                                    x-show="recovery"
                                    x-on:click="
                                        recovery = false;
                                        $nextTick(() => { $refs.code.focus() })
                                    ">
                        {{ __('modules.profile.useAuthCode') }}
                    </button>

                    <x-button class="ms-4">
                        {{ __('modules.profile.login') }}
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-auth-layout>
