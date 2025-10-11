<div class="py-8 px-4 mx-auto lg:px-6">
    <div class="mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                @lang('menu.myAddresses')
            </h2>
            @if(!$showAddressForm && $addresses->isNotEmpty() && $addresses->count() < \App\Livewire\Shop\Addresses::MAX_ADDRESSES)
            <x-button wire:click="createNewAddress" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 inline-flex" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                @lang('app.addNew')
            </x-button>
            @endif
        </div>

        @if($showAddressForm)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ $editMode ? __('modules.delivery.editAddress') : __('modules.delivery.addNewAddress') }}
                </h3>

                <form wire:submit="saveAddress">
                    <div class="mb-4">
                        <x-label for="label" value="{{ __('modules.delivery.addressLabel') }}" />
                        <x-input id="label" wire:model="label" placeholder="{{ __('placeholders.addressLabelPlaceholder') }}" class="w-full" />
                        <x-input-error for="label" />
                    </div>

                    <!-- Search Box -->
                    <div id="place-autocomplete-card" class="mb-2" wire:ignore>
                        <p id="location-search"> </p>
                    </div>

                    <div class="mb-4">
                        <section id="address-map" class="h-96 rounded-lg shadow-md border border-gray-200 mb-2" wire:ignore></section>
                        <x-input-error for="lat" custom-message="{{ __('modules.delivery.pleaseSelectLocation') }}" />
                    </div>

                    <div class="mb-4">
                        <x-label for="address" value="{{ __('modules.delivery.fullAddress') }}" />
                        <x-textarea id="address" wire:model="address" rows="3" class="w-full" data-gramm="false" placeholder="{{ __('placeholders.addressPlaceholder') }}"></x-textarea>
                        <x-input-error for="address" />
                    </div>

                    <div class="flex justify-end space-x-2">
                        <x-secondary-button wire:click="cancelForm" type="button">
                            @lang('app.cancel')
                        </x-secondary-button>
                        <x-button type="submit">
                            {{ $editMode ? __('modules.delivery.updateAddress') : __('modules.delivery.saveAddress') }}
                        </x-button>
                    </div>
                </form>
            </div>
        @endif

        @if($addresses->isEmpty() && !$showAddressForm)
            <div class="flex flex-col items-center justify-center p-8 text-center bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="w-16 h-16 mb-4 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">@lang('modules.delivery.noAddressesFound')</h3>
                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">@lang('modules.delivery.addAddressDescription')</p>
                <x-button wire:click="createNewAddress" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 inline-flex" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    @lang('app.addNew')
                </x-button>
            </div>
        @elseif(!$showAddressForm)
            <div class="grid gap-4">
                @foreach($addresses as $address)
                    <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $address->label }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $address->address }}
                                </p>
                                <div class="mt-1 text-xs text-gray-400">
                                    Lat: {{ $address->lat }}, Lng: {{ $address->lng }}
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button wire:click="editAddress({{ $address->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-500 dark:hover:text-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="confirmDeleteAddress({{ $address->id }})" class="text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <x-confirmation-modal wire:model="confirmDeleteAddressModal">
        <x-slot name="title">
            @lang('modules.delivery.confirmDeleteAddress')
        </x-slot>

        <x-slot name="content">
            @lang('modules.delivery.confirmDeleteAddressDescription')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteAddressModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            @if ($confirmDeleteAddressId)
            <x-danger-button class="ml-3" wire:click='deleteAddress' wire:loading.attr="disabled">
                {{ __('app.delete') }}
            </x-danger-button>
            @endif
        </x-slot>
    </x-confirmation-modal>

    <script>
        const MAP_API_KEY = atob('{{ base64_encode($mapApiKey) }}');

        const STRINGS = {
            deliveryLocation: "@lang('modules.delivery.deliveryLocation')",
            shopLocation: "@lang('modules.delivery.shopLocation')",
            dragToAdjust: "@lang('modules.delivery.dragMarkerToAdjust')",
            showRange: "@lang('modules.delivery.showDeliveryRange')",
            hideRange: "@lang('modules.delivery.hideDeliveryRange')",
            useCurrentLocation: "@lang('modules.delivery.useCurrentLocation')",
            locationPermissionDenied: "@lang('modules.delivery.locationPermissionDenied')",
        };

        // Load Google Maps JS if not already loaded
        if (!window.google || !google.maps) {
            const script = document.createElement('script');
            script.src = MAP_API_KEY
                ? `https://maps.googleapis.com/maps/api/js?key=${MAP_API_KEY}&loading=async&libraries=places,geocoding,marker&callback=initAddressMap`
                : `https://maps.googleapis.com/maps/api/js?&loading=async&libraries=places,geocoding,marker&callback=initAddressMap`;
            script.async = true;
            document.head.appendChild(script);
        } else {
            initAddressMap();
        }

        let addressMap, addressMarker, addressAutocomplete, mapInitialized = false;

        function initAddressMap() {
            document.addEventListener('livewire:navigated', () => {
                Livewire.on('initAddressMap', (params) => {
                    setTimeout(() => setupAddressMap(params), 300);
                });
            });

            if (document.getElementById('address-map')) {
                setTimeout(() => setupAddressMap(), 300);
            }
        }

        function setupAddressMap(params = {}) {
            const el = document.getElementById('address-map');
            if (!el || (mapInitialized && !params)) return;
            mapInitialized = true;

            let { lat = 26.9125, lng = 75.7875 } = (params?.[0] || {});

            addressMap = new google.maps.Map(el, {
                center: { lat: lat, lng: lng },
                zoom: 15,
                gestureHandling: 'greedy',
                zoomControl: false,
                streetViewControl: false,
                mapId: 'DEMO_MAP_ID',
            });

            // Customer marker
            addressMarker = new google.maps.marker.AdvancedMarkerElement({
                position: { lat: lat, lng: lng },
                map: addressMap,
                gmpDraggable:true,
                title: STRINGS.deliveryLocation
            });

            google.maps.event.addListener(addressMarker, 'dragend', (e) => {
                updateLatLng(e.latLng.lat(), e.latLng.lng());
                reverseGeocode(e.latLng);
            });

            google.maps.event.addListener(addressMap, 'click', (e) => {
                updateLatLng(e.latLng.lat(), e.latLng.lng());
                reverseGeocode(e.latLng);
            });

            // Ensure proper map rendering
            setTimeout(() => {
                google.maps.event.trigger(addressMap, 'resize');
                addressMap.setCenter(new google.maps.LatLng(lat, lng));
            }, 100);

            addAutocomplete();

            addCurrentLocationButton();
        }

        function addCurrentLocationButton() {
            const button = document.createElement('button-element');
            button.className = 'bg-white p-2 rounded-lg shadow-md m-3';
            button.title = STRINGS.useCurrentLocation;

            const defaultSvg = `<svg class="w-5 h-5 text-current" width="20" height="20" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="3"/><path d="M13 4.069V2h-2v2.069A8.01 8.01 0 0 0 4.069 11H2v2h2.069A8.01 8.01 0 0 0 11 19.931V22h2v-2.069A8.01 8.01 0 0 0 19.931 13H22v-2h-2.069A8.01 8.01 0 0 0 13 4.069M12 18c-3.309 0-6-2.691-6-6s2.691-6 6-6 6 2.691 6 6-2.691 6-6 6"/></svg>`;
            const loadingSvg = `<svg class="animate-spin w-5 h-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4z"/></svg>`;

            button.innerHTML = defaultSvg;

            button.addEventListener('click', () => {
            if (!navigator.geolocation) return;

            button.innerHTML = loadingSvg;

            navigator.geolocation.getCurrentPosition(
                ({ coords: { latitude: lat, longitude: lng } }) => {
                const coords = { lat, lng };
                updateLatLng(coords.lat , coords.lng);
                reverseGeocode(coords);
                button.innerHTML = defaultSvg;
                },
                (error) => {
                console.error('Geolocation error:', error);
                button.innerHTML = defaultSvg;
                },
                { timeout: 10000, enableHighAccuracy: true }
            );
            });

            addressMap.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(button);
        }

        function updateLatLng(lat, lng) {
            if (lat && lng) {
                addressMarker.position = { lat, lng };
                addressMap.setCenter({ lat, lng });
                @this.set('lat', lat);
                @this.set('lng', lng);
            }
        }

        function addAutocomplete() {
            if (!window.placeAutocomplete) {
                const locationSearchInput = document.getElementById('location-search');

                window.placeAutocomplete = new google.maps.places.PlaceAutocompleteElement({
                    inputElement: locationSearchInput,
                });
            }

            const card = document.getElementById('place-autocomplete-card');
            card.appendChild(placeAutocomplete);


            placeAutocomplete.addEventListener('gmp-select', async ({ placePrediction }) => {
                const place = placePrediction.toPlace();
                await place.fetchFields({ fields: ['formattedAddress', 'location'] });

                const { location, formattedAddress } = place;

                @this.set('address', formattedAddress);

                if (location) {
                    updateLatLng(location.lat(), location.lng());
                }
            });
        }


        function reverseGeocode(latLng) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: latLng }, (results, status) => {
            if (status === google.maps.GeocoderStatus.OK && results[0]) {
                @this.set('address', results[0].formatted_address);
            } else {
                console.error("Geocoder failed due to: " + status);
            }
            });
        }
    </script>
</div>
