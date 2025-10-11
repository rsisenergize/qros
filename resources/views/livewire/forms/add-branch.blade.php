<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">
            <div>
                <x-label for="branchName" value="{{ __('modules.settings.branchName') }}" />
                <x-input id="branchName" class="block mt-1 w-full" type="text"  wire:model='branchName' />
                <x-input-error for="branchName" class="mt-2" />
            </div>

            <div>
                <x-label for="branchAddress" value="{{ __('modules.settings.branchAddress') }}" />
                <x-textarea id="branchAddress" class="block mt-1 w-full" rows="3" wire:model='branchAddress' />
                <x-input-error for="branchAddress" class="mt-2" />
            </div>

            <!-- Search Box -->
            <div id="place-autocomplete-card" class="mb-2" wire:ignore>
                <p id="location-search"> </p>
            </div>

            <div class="mb-4">
                <section id="address-map" class="h-96 rounded-lg shadow-md border border-gray-200 mb-2" wire:ignore></section>
                <x-input-error for="lat" custom-message="{{ __('modules.delivery.pleaseSelectLocation') }}" />
            </div>


            @if($branchLat || $branchLng)
                <div class="flex flex-col md:flex-row items-center gap-4">
                    <div class="w-full md:w-1/2">
                        <x-label for="branchLat" value="{{ __('modules.delivery.branchLat') }}"/>
                        <div class="block w-full bg-gray-100 dark:bg-gray-800 p-2 rounded border border-gray-300 dark:border-gray-600 text-sm text-gray-900 dark:text-gray-200">
                            {{ $branchLat }}
                        </div>
                    </div>
                    <div class="w-full md:w-1/2">
                        <x-label for="branchLng" value="{{ __('modules.delivery.branchLng') }}"/>
                        <div class="block w-full bg-gray-100 dark:bg-gray-800 p-2 rounded border border-gray-300 dark:border-gray-600 text-sm text-gray-900 dark:text-gray-200">
                            {{ $branchLng }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideAddBranch')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
