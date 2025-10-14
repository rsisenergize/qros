<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('modules.profile.profileInfo') }}
    </x-slot>

    <x-slot name="description">
        {{ __('modules.profile.updateProfileInfo') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" id="photo" class="hidden"
                            wire:model.live="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-label for="photo" value="{{ __('modules.profile.photo') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_path?asset_url_local_s3($this->user->profile_photo_path):$this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('modules.profile.selectNewPhoto') }}
                </x-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('modules.profile.removePhoto') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('modules.profile.name') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('app.email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2 dark:text-white">
                    {{ __('Your email address is unverified.') }}

                    <button type="button" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>

        <!-- Phone Number -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="phone_number" value="{{ __('modules.settings.phoneNumber') }}" />
            <div class="flex gap-2 mt-1">
                <!-- Phone Code Dropdown -->
                <div x-data="{ isOpen: false, search: '', 
                    selectedCode: '{{ $this->state['phone_code'] ?? '' }}',
                    phoneCodes: @js(\App\Models\Country::pluck('phonecode')->unique()->filter()->values()->toArray()),
                    get filteredCodes() {
                        if (!this.search) return this.phoneCodes;
                        return this.phoneCodes.filter(code => code.includes(this.search));
                    },
                    selectCode(code) {
                        this.selectedCode = code;
                        this.isOpen = false;
                        this.search = '';
                        $wire.set('state.phone_code', code);
                    }
                }" 
                x-init="
                    // Watch for changes in Livewire state and update selectedCode
                    $watch('$wire.state.phone_code', value => {
                        if (value) {
                            selectedCode = value;
                        }
                    });
                "
                @click.away="isOpen = false" class="relative w-32">
                    <div @click="isOpen = !isOpen"
                        class="p-2 bg-gray-100 border rounded cursor-pointer dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                        <div class="flex items-center justify-between">
                            <span class="text-sm">
                                <span x-show="selectedCode">+<span x-text="selectedCode"></span></span>
                                <span x-show="!selectedCode">{{ __('modules.settings.select') }}</span>
                            </span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Search Input and Options -->
                    <ul x-show="isOpen" x-transition class="absolute z-10 w-full mt-1 overflow-auto bg-white rounded-lg shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                        <li class="sticky top-0 px-3 py-2 bg-white dark:bg-gray-900 z-10">
                            <input type="text" 
                                x-model="search"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                placeholder="{{ __('placeholders.search') }}" />
                        </li>
                        <template x-for="phonecode in filteredCodes" :key="phonecode">
                            <li @click="selectCode(phonecode)"
                                class="relative py-2 pl-3 text-gray-900 transition-colors duration-150 cursor-pointer select-none pr-9 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600"
                                :class="{ 'bg-gray-100 dark:bg-gray-800': phonecode === selectedCode }" role="option">
                                <div class="flex items-center">
                                    <span class="block ml-3 text-sm whitespace-nowrap" x-text="'+' + phonecode"></span>
                                </div>
                            </li>
                        </template>
                        <li x-show="filteredCodes.length === 0" class="relative py-2 pl-3 text-gray-500 cursor-default select-none pr-9 dark:text-gray-400">
                            {{ __('modules.settings.noPhoneCodesFound') }}
                        </li>
                    </ul>
                </div>
                <!-- Phone Number Input -->
                <x-input id="phone_number" class="block w-full" type="tel"
                    wire:model="state.phone_number" placeholder="1234567890" />
            </div>

            <x-input-error for="phone_code" class="mt-2" />
            <x-input-error for="phone_number" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('app.saved') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('app.save') }}
        </x-button>
    </x-slot>
</x-form-section>
