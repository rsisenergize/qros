<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-xl p-2">
    <div class="mb-2">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('modules.delivery.selectDeliveryLocation')</h2>
    </div>


    @if($inRange && $isDeliveryTimeAvailable)
        <x-alert type="success" class="mt-4">
            <div class="flex items-center">
                @if($isFreeDelivery)
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/></svg>
                    @lang('modules.delivery.freeDelivery')
                    @if(isset($freeDeliveryOverAmount) && $orderGrandTotal >= $freeDeliveryOverAmount)
                        (@lang('modules.delivery.orderQualifiesForFreeDelivery'))
                    @endif
                @else
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-14v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                    @lang('modules.delivery.deliveryFee'): <span class="font-semibold ml-1">{{ currency_format($deliveryFee, $currencyId) }}</span>
                    @if($distance)
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">
                            ({{ number_format($branch->deliverySetting->unit === 'miles' ? $distance / 1.60934 : $distance, 2) }} {{ $branch->deliverySetting->unit === 'miles' ? 'miles' : 'km' }})
                        </span>
                    @endif
                @endif
            </div>
        </x-alert>
    @elseif($deliveryMessage)
        <x-alert type="danger" class="mt-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3"/></svg>
                {{ $deliveryMessage ?? __('modules.delivery.locationOutOfRange') }}
            </div>
        </x-alert>
    @endif

    <!-- Saved/Manual Toggle -->
    <div class="flex justify-end mb-4">
        @if($customerAllAddresses?->isNotEmpty())
            <x-button
                wire:click="toggleManualLocation"
                @class([
                    'transition-colors duration-200',
                    'bg-blue-600' => !$showManualLocation,
                    'text-gray-700' => $showManualLocation,
                    'opacity-50' => !$isDeliveryTimeAvailable
                ]) :disabled="!$isDeliveryTimeAvailable">
                <svg class="w-5 h-5 mr-1 inline-flex" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($showManualLocation)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    @endif
                </svg>
                {{ $showManualLocation ? __('modules.delivery.useSavedAddress') : __('modules.delivery.useDifferentLocation') }}
            </x-button>
        @endif
    </div>

    <div class="space-y-6">
        <!-- Saved Addresses Section -->
        @if($customerAllAddresses?->isNotEmpty() && !$showManualLocation)
            <div class="grid gap-6 md:grid-cols-2">
                @foreach($customerAllAddresses as $address)
                    <div class="relative h-[120px]">
                        <div
                            wire:click="selectAddressFromSaved({{ $address->id }})"
                            class="h-full p-4 bg-white dark:bg-gray-700 rounded-xl border-2 cursor-pointer
                                hover:shadow-lg hover:border-blue-500 dark:hover:border-blue-400
                                transition-all duration-200 ease-in-out
                                {{ $selectedAddressId == $address->id ? 'border-blue-500 dark:border-blue-400 ring-4 ring-blue-500/20' : 'border-gray-200 dark:border-gray-600' }}"
                        >
                            <div class="flex h-full gap-3">
                                <div class="flex-shrink-0">
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center
                                        {{ $selectedAddressId == $address->id ? 'border-blue-500' : 'border-gray-300 dark:border-gray-500' }}">
                                        @if($selectedAddressId == $address->id)
                                            <div class="w-4 h-4 rounded-full bg-blue-500"></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1 flex flex-col space-y-3 min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $address->label }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
                                        {{ $address->address }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Manual Location Section -->
        @if(!$customerAllAddresses?->isNotEmpty() || $showManualLocation)
            <div class="space-y-6" wire:key='checkout-map-form'>
                <!-- Search Box -->
                <div id="place-autocomplete-card" wire:ignore>
                    <p id="location-search"> </p>
                </div>

                <!-- Map -->
                <div id="delivery-map" class="w-full h-[500px] rounded-xl shadow-md border border-gray-200 dark:border-gray-600" wire:ignore></div>

                <!-- Address Input -->
                <div class="space-y-1">
                    <x-textarea
                        id="delivery-address"
                        wire:model="selectedAddress"
                        rows="3"
                        class="w-full resize-none"
                        placeholder="{{__('modules.delivery.fullAddressPlaceholder')}}">
                    </x-textarea>
                    <x-input-error for="selectedAddress" />
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-4 pt-2">
                    @if($customerAllAddresses?->isNotEmpty())
                        <x-secondary-button wire:click="$set('showManualLocation', false)">
                            @lang('app.cancel')
                        </x-secondary-button>
                    @endif
                    <x-button
                        wire:click="selectDeliveryAddress"
                        wire:loading.attr="disabled"
                        :disabled="!$inRange || !$isDeliveryTimeAvailable"
                        @class([
                            'px-6',
                            'opacity-50' => !$inRange || !$isDeliveryTimeAvailable
                        ])>
                        @lang('modules.delivery.confirmLocation')
                    </x-button>
                </div>
            </div>
        @endif
        <!-- Action Buttons for saved address -->
        @if($selectedAddressId)
        <div class="flex justify-end gap-4 pt-4">
            <x-button wire:click="confirmSelectedAddress" wire:loading.attr="disabled"
                :disabled="!$inRange || !$isDeliveryTimeAvailable"
                class="px-6 bg-green-600 hover:bg-green-700"
                @class(['bg-green-600 hover:bg-green-700',
                        'opacity-50' => !$inRange || !$isDeliveryTimeAvailable
                ])>
                @lang('modules.delivery.confirmLocation')
            </x-button>
        </div>
        @endif
    </div>

    @push('scripts')
    @script
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
                ? `https://maps.googleapis.com/maps/api/js?key=${MAP_API_KEY}&loading=async&libraries=places,geocoding,marker&callback=initDeliveryMap`
                : `https://maps.googleapis.com/maps/api/js?&loading=async&libraries=places,geocoding,marker&callback=initDeliveryMap`;
            script.async = true;
            document.head.appendChild(script);
        } else {
            initDeliveryMap();
        }

        let deliveryMap, deliveryMarker, deliveryCircle, autocomplete;
        let mapInitialized = false;

        function initDeliveryMap() {
            Livewire.on('initDeliveryMap', (params) => {
                setTimeout(() => setupMap(params), 200);
            });
        }

        function setupMap(params = {}) {
            const el = document.getElementById('delivery-map');
            if (!el || (mapInitialized && !params)) return;
            mapInitialized = true;

            const {
                branchLat = @js($branchLat),
                branchLng = @js($branchLng),
                maxKm = @js($maxKm),
                defaultLat = @js($customerLat),
                defaultLng = @js($customerLng)
            } = params?.[0] || {};

            deliveryMap = new google.maps.Map(el, {
                center: { lat: defaultLat || branchLat, lng: defaultLng || branchLng },
                zoom: 15,
                gestureHandling: 'greedy',
                zoomControl: false,
                streetViewControl: false,
                mapId: 'DEMO_MAP_ID',
            });

            // Customer marker
            deliveryMarker = new google.maps.marker.AdvancedMarkerElement({
                position: { lat: defaultLat || branchLat, lng: defaultLng || branchLng },
                map: deliveryMap,
                gmpDraggable: true,
                title: STRINGS.deliveryLocation
            });

            // Branch marker
            const branchImg = document.createElement("div");
            branchImg.style.position = "relative";
            branchImg.style.width = "40px";
            branchImg.style.height = "50px";
            const markerSvg = `<svg viewBox="0 0 512 512" style="position:absolute;left:0;bottom:0">
                <path d="M256 0C150 0 64 86 64 192c0 133.1 174.9 307.7 181.6 314.4a16 16 0 0022.8 0C273.1 499.7 448 325.1 448 192 448 86 362 0 256 0z" fill="#f44336"/>
                <circle cx="256" cy="192" r="140" fill="#ffffff"/>
                <image href="{{ $restaurantLogo }}" x="136" y="72" width="240" height="240" clip-path="circle(120px at center)"/>
            </svg>`;

            branchImg.innerHTML = markerSvg;

            new google.maps.marker.AdvancedMarkerElement({
                position: { lat: branchLat, lng: branchLng },
                map: deliveryMap,
                content: branchImg,
                title: STRINGS.shopLocation
            });

            showInfoWindow(deliveryMarker);
            addAutocomplete();
            addMapEvents();
            drawDeliveryRange(branchLat, branchLng, maxKm);
            addCurrentLocationButton();
        }

        function showInfoWindow(marker) {
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-800">${STRINGS.deliveryLocation}</p>
                        <p class="text-xs text-gray-500 mt-1">${STRINGS.dragToAdjust}</p>
                    </div>`,
                disableAutoPan: true,
                pixelOffset: new google.maps.Size(0, -15),
            });

            // Remove close button when info window loads
            google.maps.event.addListener(infoWindow, "domready", () => {
                const closeButton = document.querySelector('.gm-ui-hover-effect');
                if (closeButton) {
                    closeButton.remove();
                }
            });

            // Show info window by default
            infoWindow.open(deliveryMap, marker);

            google.maps.event.addListener(marker, 'dragend', () => {
                infoWindow.open(deliveryMap, marker);
            });
        }

        function drawDeliveryRange(lat, lng, maxKm) {
            deliveryCircle = new google.maps.Circle({
                map: null,
                center: { lat, lng },
                radius: maxKm * 1000,
                strokeColor: '#4CAF50',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#4CAF50',
                fillOpacity: 0.1,
                clickable: false
            });

            const container = document.createElement('div');
            container.className = 'bg-white rounded-lg shadow-md p-1 m-2';

            const toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'text-sm px-2 py-1 font-medium transition text-gray-700 hover:text-blue-600';
            toggle.innerText = STRINGS.showRange;

            let visible = false;

            toggle.addEventListener('click', () => {
                visible = !visible;
                deliveryCircle.setMap(visible ? deliveryMap : null);
                toggle.innerText = visible ? STRINGS.hideRange : STRINGS.showRange;
            });

            container.appendChild(toggle);
            deliveryMap.controls[google.maps.ControlPosition.TOP_RIGHT].push(container);
        }

        function addCurrentLocationButton() {
            const button = document.createElement('button');
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
                updateLocation(coords);
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

            deliveryMap.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(button);
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

                @this.set('selectedAddress', formattedAddress);

                if (location) {
                    updateLocation(location);
                }
            });
        }

        function addMapEvents() {
            google.maps.event.addListener(deliveryMarker, 'dragend', (e) => {
                updateLocation(e.latLng);
                reverseGeocode(e.latLng);
            });

            google.maps.event.addListener(deliveryMap, 'click', (e) => {
                deliveryMarker.position = e.latLng;
                updateLocation(e.latLng);
                reverseGeocode(e.latLng);
            });
        }

        function updateLocation(latLng) {
            const lat = typeof latLng.lat === 'function' ? latLng.lat() : latLng.lat;
            const lng = typeof latLng.lng === 'function' ? latLng.lng() : latLng.lng;

            deliveryMarker.position = { lat, lng };
            deliveryMap.setCenter({ lat, lng });

            Livewire.dispatch('locationSelected', {
                lat,
                lng,
                address: @this.get('selectedAddress')
            });
        }

        function reverseGeocode(latLng) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    @this.set('selectedAddress', results[0].formatted_address);
                }
            });
        }
    </script>
    @endscript
    @endpush

</div>
