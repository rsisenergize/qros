<div>
    @if ($isEditing)
        <div class="px-4">
            <form wire:submit="saveBranch">
                @csrf
                <div class="space-y-4">
                    <div>
                        <x-label for="branchName" value="{{ __('modules.settings.branchName') }}" />
                        <x-input id="branchName" class="block mt-1 w-full" type="text" wire:model='branchName' />
                        <x-input-error for="branchName" class="mt-2" />
                    </div>

                    @if ($formMode === 'add')
                        <div>
                            <x-label for="cloneData" value="{{ __('modules.settings.getDatafrom') }}" />
                            <x-select id="cloneData" class="block mt-1 w-full" wire:model.live="cloneData">
                                <option value="">{{ __('app.select') }}</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </x-select>
                            <x-input-error for="cloneData" class="mt-2" />
                        </div>

                        @if ($cloneData)
                            <div
                                class="mt-4 p-4 border rounded-lg bg-gray-100 dark:bg-gray-800 shadow-sm flex flex-col md:flex-row gap-6">
                                <!-- Clone Options -->
                                <div class="flex-1">
                                    <x-label value="{{ __('modules.settings.cloneOptions') }}" class="mb-2" />
                                    <div class="space-y-2">
                                        <div>
                                            <x-checkbox id="clone_menu" wire:model="cloneMenu" />
                                            <label for="clone_menu"
                                                class="ml-2 text-sm text-gray-700 dark:text-gray-200">@lang('modules.settings.menu')</label>
                                        </div>
                                        <x-input-error for="cloneMenu" class="mt-2" />

                                        <div>
                                            <x-checkbox id="clone_categories" wire:model="clonecategories" />
                                            <label for="clone_categories"
                                                class="ml-2 text-sm text-gray-700 dark:text-gray-200">@lang('modules.settings.ItemCategories')</label>
                                        </div>
                                        <x-input-error for="clonecategories" class="mt-2" />
                                        <div>
                                            <x-checkbox id="clone_menu_items" wire:model="cloneMenuItems" wire:change="handleCloneMenuItemsChange" />
                                            <label for="clone_menu_items"
                                                class="ml-2 text-sm text-gray-700 dark:text-gray-200">@lang('modules.settings.menuItems')</label>
                                        </div>
                                        <x-input-error for="cloneMenuItems" class="mt-2" />

                                        <div>
                                            <x-checkbox id="clone_item_modifiers" wire:model="cloneItemModifires" wire:change="handleCloneItemModifiersChange" />
                                            <label for="clone_item_modifiers"
                                                class="ml-2 text-sm text-gray-700 dark:text-gray-200">@lang('modules.modifier.itemModifiers')</label>
                                        </div>
                                        <div>
                                            <x-checkbox id="clone_modifiers_groups" wire:model="cloneModifiersGroups" />
                                            <label for="clone_modifiers_groups"
                                                class="ml-2 text-sm text-gray-700 dark:text-gray-200">@lang('modules.modifier.modifierGroup')</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Clone Settings -->
                                <div class="flex-1">
                                    <x-label value="{{ __('modules.settings.cloneSettings') }}" class="mb-2" />
                                    <div class="space-y-2">

                                    <div>
                                        <x-checkbox id="clone_delivery_settings" wire:model="cloneDeliverySettings" />
                                        <label for="clone_delivery_settings"
                                            class="ml-2 text-sm text-gray-700 dark:text-gray-200">@lang('modules.settings.deliverySettings')</label>
                                    </div>

                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    <div>
                        <x-label for="branchAddress" value="{{ __('modules.settings.branchAddress') }}" />
                        <x-textarea id="branchAddress" class="block mt-1 w-full" rows="3"
                            wire:model='branchAddress' />
                        <x-input-error for="branchAddress" class="mt-2" />
                    </div>

                    <!-- Search Box -->
                    <div id="place-autocomplete-card" class="mb-2" wire:ignore>
                        <p id="location-search"> </p>
                    </div>

                    <div class="mb-4">
                        <section id="branch-address-map" class="h-96 rounded-lg shadow-md border border-gray-200 mb-2"
                            wire:ignore></section>
                        <x-input-error for="lat"
                            custom-message="{{ __('modules.delivery.pleaseSelectLocation') }}" />
                    </div>
                </div>

                <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
                    <x-button>@lang('app.save')</x-button>
                    <x-button-cancel wire:click="cancelEdit"
                        wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
                </div>
            </form>
        </div>
    @else
        <div class="px-4 mb-4">

            @if (!in_array('Change Branch', restaurant_modules()))
                <button wire:click="$dispatch('showUpgradeLicense')"
                    class="bg-white dark:bg-gray-800 border border-skin-base dark:border-gray-500 rounded-lg font-semibold text-skin-base dark:text-gray-300 text-sm px-5 py-2.5 text-center inline-flex items-center  w-fit gap-2 justify-between"
                    type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-geo-alt" viewBox="0 0 16 16">
                        <path
                            d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10" />
                        <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                    </svg>

                    @lang('modules.settings.addBranch')

                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-rocket-takeoff" viewBox="0 0 16 16">
                        <path
                            d="M9.752 6.193c.599.6 1.73.437 2.528-.362s.96-1.932.362-2.531c-.599-.6-1.73-.438-2.528.361-.798.8-.96 1.933-.362 2.532" />
                        <path
                            d="M15.811 3.312c-.363 1.534-1.334 3.626-3.64 6.218l-.24 2.408a2.56 2.56 0 0 1-.732 1.526L8.817 15.85a.51.51 0 0 1-.867-.434l.27-1.899c.04-.28-.013-.593-.131-.956a9 9 0 0 0-.249-.657l-.082-.202c-.815-.197-1.578-.662-2.191-1.277-.614-.615-1.079-1.379-1.275-2.195l-.203-.083a10 10 0 0 0-.655-.248c-.363-.119-.675-.172-.955-.132l-1.896.27A.51.51 0 0 1 .15 7.17l2.382-2.386c.41-.41.947-.67 1.524-.734h.006l2.4-.238C9.005 1.55 11.087.582 12.623.208c.89-.217 1.59-.232 2.08-.188.244.023.435.06.57.093q.1.026.16.045c.184.06.279.13.351.295l.029.073a3.5 3.5 0 0 1 .157.721c.055.485.051 1.178-.159 2.065m-4.828 7.475.04-.04-.107 1.081a1.54 1.54 0 0 1-.44.913l-1.298 1.3.054-.38c.072-.506-.034-.993-.172-1.418a9 9 0 0 0-.164-.45c.738-.065 1.462-.38 2.087-1.006M5.205 5c-.625.626-.94 1.351-1.004 2.09a9 9 0 0 0-.45-.164c-.424-.138-.91-.244-1.416-.172l-.38.054 1.3-1.3c.245-.246.566-.401.91-.44l1.08-.107zm9.406-3.961c-.38-.034-.967-.027-1.746.163-1.558.38-3.917 1.496-6.937 4.521-.62.62-.799 1.34-.687 2.051.107.676.483 1.362 1.048 1.928.564.565 1.25.941 1.924 1.049.71.112 1.429-.067 2.048-.688 3.079-3.083 4.192-5.444 4.556-6.987.183-.771.18-1.345.138-1.713a3 3 0 0 0-.045-.283 3 3 0 0 0-.3-.041Z" />
                        <path
                            d="M7.009 12.139a7.6 7.6 0 0 1-1.804-1.352A7.6 7.6 0 0 1 3.794 8.86c-1.102.992-1.965 5.054-1.839 5.18.125.126 3.936-.896 5.054-1.902Z" />
                    </svg>
                </button>
            @else
                <x-button type='button' wire:click="createMode">@lang('modules.settings.addBranch')</x-button>
            @endif
        </div>

        <div class="flex flex-col">

            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow">
                        <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        @lang('modules.settings.branchName')
                                    </th>

                                    <th scope="col"
                                        class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        @lang('modules.settings.branchAddress')
                                    </th>

                                    <th scope="col"
                                        class="py-2.5 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                        @lang('app.action')
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                                wire:key='member-list-{{ microtime() }}'>

                                @foreach (branches() as $item)
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700"
                                        wire:key='member-{{ $item->id . rand(1111, 9999) . microtime() }}'
                                        wire:loading.class.delay='opacity-10'>
                                        <td
                                            class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $item->name }}
                                        </td>

                                        <td
                                            class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $item->address }}
                                        </td>

                                        <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right">
                                            <x-secondary-button-table wire:click='showEditBranch({{ $item->id }})'
                                                wire:key='member-edit-{{ $item->id . microtime() }}'
                                                wire:key='editmenu-item-button-{{ $item->id }}'>
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                                    </path>
                                                    <path fill-rule="evenodd"
                                                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                @lang('app.update')
                                            </x-secondary-button-table>

                                            @if (branch()->id != $item->id)
                                                <x-danger-button-table
                                                    wire:click="showDeleteBranch({{ $item->id }})"
                                                    wire:key='branch-del-{{ $item->id . microtime() }}'>
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd"
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </x-danger-button-table>
                                            @else
                                                <br><span class="text-xs dark:text-gray-400">@lang('messages.cannotDeleteCurrentBranch')</span>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    @endif

    <x-confirmation-modal wire:model="confirmDeleteBranchModal">
        <x-slot name="title">
            @lang('modules.settings.deleteBranch')?
        </x-slot>

        <x-slot name="content">
            @lang('modules.settings.deleteBranchMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteBranchModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            @if ($activeBranch)
                <x-danger-button class="ml-3" wire:click='deleteBranch({{ $activeBranch->id }})'
                    wire:loading.attr="disabled">
                    {{ __('app.delete') }}
                </x-danger-button>
            @endif
        </x-slot>
    </x-confirmation-modal>


    @pushOnce('scripts')
        @script
            <script>
                const MAP_API_KEY = atob('{{ base64_encode($mapApiKey) }}');
                const STRINGS = {
                    shopLocation: "@lang('modules.delivery.shopLocation')",
                    dragToAdjust: "@lang('modules.delivery.dragMarkerToAdjust')",
                };

                // Load Google Maps JS if not already loaded
                if (!window.google || !google.maps) {
                    const script = document.createElement('script');
                    script.src = MAP_API_KEY ?
                        `https://maps.googleapis.com/maps/api/js?key=${MAP_API_KEY}&loading=async&libraries=places,geocoding,marker&callback=setupAddressMap` :
                        `https://maps.googleapis.com/maps/api/js?&loading=async&libraries=places,geocoding,marker&callback=setupAddressMap`;
                    document.head.appendChild(script);
                } else {
                    setupAddressMap();
                }

                document.addEventListener('livewire:navigated', () => {
                    Livewire.on('initAddressMap', (params) => {
                        setTimeout(() => setupAddressMap(params), 300);
                    });
                });

                if (document.getElementById('branch-address-map')) {
                    setTimeout(() => setupAddressMap(), 300);
                }

                let map, addressMarker;

                function setupAddressMap() {
                    const mapElement = document.getElementById('branch-address-map');

                    if (!mapElement) {
                        return;
                    }

                    const lat = parseFloat(@this.get('branchLat')) || 26.9125;
                    const lng = parseFloat(@this.get('branchLng')) || 75.7875;

                    map = new google.maps.Map(mapElement, {
                        center: {
                            lat,
                            lng
                        },
                        zoom: 15,
                        gestureHandling: 'greedy',
                        zoomControl: false,
                        streetViewControl: false,
                        mapId: 'DEMO_MAP_ID',
                    });

                    const container = document.createElement("div");
                    container.style.position = "relative";
                    container.style.width = "35px";
                    container.style.height = "45px";
                    const markerSvg = `<svg viewBox="0 0 512 512" style="position:absolute;left:0;bottom:0">
                <path d="M256 0C150 0 64 86 64 192c0 133.1 174.9 307.7 181.6 314.4a16 16 0 0022.8 0C273.1 499.7 448 325.1 448 192 448 86 362 0 256 0z" fill="#f44336"/>
                <circle cx="256" cy="192" r="140" fill="#ffffff"/>
                <image href="{{ restaurant()->logo_url }}" x="136" y="72" width="240" height="240" clip-path="circle(120px at center)"/>
            </svg>`;

                    container.innerHTML = markerSvg;

                    addressMarker = new google.maps.marker.AdvancedMarkerElement({
                        position: {
                            lat,
                            lng
                        },
                        map: map,
                        content: container,
                        gmpDraggable: true,
                        title: STRINGS.shopLocation
                    });

                    google.maps.event.addListener(addressMarker, 'dragend', (e) => {
                        updateLatLng(e.latLng.lat(), e.latLng.lng());
                    });

                    google.maps.event.addListener(map, 'click', (e) => {
                        updateLatLng(e.latLng.lat(), e.latLng.lng());
                    });

                    // Ensure proper map rendering
                    setTimeout(() => {
                        google.maps.event.trigger(map, 'resize');
                        map.setCenter(new google.maps.LatLng(lat, lng));
                    }, 100);

                    addAutocomplete();

                    addCurrentLocationButton();
                }

                function addCurrentLocationButton() {
                    const button = document.createElement("button-current-location");
                    button.className = "bg-white p-2 rounded-lg shadow-md m-3";
                    button.title = "Use Current Location";
                    button.setAttribute("type", "button-current-location");

                    const defaultSvg =
                        `<svg class="w-5 h-5 text-current" width="20" height="20" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="3"/><path d="M13 4.069V2h-2v2.069A8.01 8.01 0 0 0 4.069 11H2v2h2.069A8.01 8.01 0 0 0 11 19.931V22h2v-2.069A8.01 8.01 0 0 0 19.931 13H22v-2h-2.069A8.01 8.01 0 0 0 13 4.069M12 18c-3.309 0-6-2.691-6-6s2.691-6 6-6 6 2.691 6 6-2.691 6-6 6"/></svg>`;
                    const loadingSvg =
                        `<svg class="animate-spin w-5 h-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4z"/></svg>`;

                    button.innerHTML = defaultSvg;

                    button.addEventListener("click", () => {
                        if (!navigator.geolocation) return;

                        button.innerHTML = loadingSvg;

                        navigator.geolocation.getCurrentPosition(
                            ({
                                coords: {
                                    latitude: lat,
                                    longitude: lng
                                }
                            }) => {
                                const position = {
                                    lat,
                                    lng
                                };
                                updateLatLng(lat, lng);
                                button.innerHTML = defaultSvg;
                            },
                            (error) => {
                                console.error("Geolocation error:", error);
                                button.innerHTML = defaultSvg;
                            }, {
                                timeout: 10000,
                                enableHighAccuracy: true
                            }
                        );
                    });

                    map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(button);
                }

                function updateLatLng(lat, lng) {
                    if (lat && lng) {
                        addressMarker.position = {
                            lat,
                            lng
                        };
                        map.setCenter({
                            lat,
                            lng
                        });
                        @this.set('branchLat', lat);
                        @this.set('branchLng', lng);
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


                    placeAutocomplete.addEventListener('gmp-select', async ({
                        placePrediction
                    }) => {
                        const place = placePrediction.toPlace();
                        await place.fetchFields({
                            fields: ['location']
                        });
                        const {
                            location
                        } = place;

                        if (location) {
                            updateLatLng(location.lat(), location.lng());
                        }
                    });
                }
            </script>
        @endscript
    @endpushOnce



</div>
