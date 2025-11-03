<div class="p-4 space-y-6 md:p-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('modules.settings.deliveryApps')</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.deliveryAppsDescription')</p>
        </div>
        <x-button wire:click="openAddForm" class="inline-flex items-center gap-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            @lang('modules.settings.addDeliveryApp')
        </x-button>
    </div>

    <!-- Delivery Platforms List -->
    @if(count($deliveryPlatforms) > 0)
        <div class="overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                @lang('app.logo')
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                @lang('modules.settings.name')
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                @lang('modules.settings.commission')
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                @lang('modules.settings.status')
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                @lang('app.action')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @foreach($deliveryPlatforms as $platform)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <!-- Logo -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex-shrink-0 w-10 h-10">
                                        @if($platform->logo)
                                            <img
                                                class="w-10 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-600"
                                                src="{{ $platform->logo_url ?? asset('images/default-logo.png') }}"
                                                alt="{{ $platform->name }}"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-600 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 16 4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Name -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $platform->name }}
                                    </div>
                                </td>

                                <!-- Commission -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $platform->commission_value }}{{ $platform->commission_type === 'percent' ? '%' : '' }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button wire:click="togglePlatformStatus('{{ $platform->id }}')"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-skin-base focus:ring-offset-2 {{ $platform->is_active ? 'bg-skin-base' : 'bg-gray-200 dark:bg-gray-600' }}">
                                        <span class="sr-only">@lang('modules.settings.toggleStatus')</span>
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $platform->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                </td>

                                <!-- Actions -->
                                <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right rtl:space-x-reverse">
                                    <x-secondary-button-table wire:click="editPlatform('{{ $platform->id }}')" wire:key="platform-edit-{{ $platform->id . microtime() }}">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 0 0-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 0 0 0-2.828"/><path fill-rule="evenodd" d="M2 6a2 2 0 0 1 2-2h4a1 1 0 0 1 0 2H4v10h10v-4a1 1 0 1 1 2 0v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2z" clip-rule="evenodd"/></svg>
                                        @lang('app.update')
                                    </x-secondary-button-table>
                                    <x-danger-button-table wire:click="confirmDelete('{{ $platform->id }}')" wire:key="platform-del-{{ $platform->id . microtime() }}">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1" clip-rule="evenodd"/></svg>
                                    </x-danger-button-table>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">@lang('modules.settings.noDeliveryApps')</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.noDeliveryAppsDescription')</p>
            <div class="mt-6">
                <x-button wire:click="openAddForm" class="inline-flex items-center gap-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    @lang('modules.settings.addDeliveryApp')
                </x-button>
            </div>
        </div>
    @endif

    <!-- Add/Edit Form Modal -->
    <x-right-modal wire:model.live="showAddForm">
        <x-slot name="title">
            {{ $editingIndex !== null ? __('modules.settings.editDeliveryApp') : __('modules.settings.addDeliveryApp') }}
        </x-slot>

        <x-slot name="content">
            <form wire:submit="savePlatform" class="space-y-6" x-data="{ logoPreview: null, logoName: null }">
                <!-- App Name -->
                <div>
                    <x-label for="name" :value="__('modules.settings.appName')" class="mb-1" />
                    <x-input id="name" type="text" wire:model.live="name" class="w-full" placeholder="{{__('placeholders.appNamePlaceHolder')}}" />
                    <x-input-error for="name" class="mt-1 text-xs" />
                </div>

                <!-- Upload Logo -->
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-base font-medium text-gray-900 dark:text-white">@lang('modules.settings.uploadLogo')</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.uploadPlatformLogoDescription')</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>

                    <div x-data="{ photoName: null, photoPreview: null, hasNewPhoto: @entangle('hasNewPhoto').live , clearFileInput() { this.photoName = ''; this.photoPreview = ''; this.hasNewPhoto = false; this.$refs.photo.value = ''; @this.set('logo', ''); } }" class="flex items-center space-x-4">
                        <!-- Profile Photo File Input -->
                        <input type="file" class="hidden"
                                    accept="image/png, image/gif, image/jpeg, image/webp"
                                    wire:model="logo"
                                    x-ref="photo"
                                    x-on:change="
                                            photoName = $refs.photo.files[0].name;
                                            const reader = new FileReader();
                                            reader.onload = (e) => {
                                                photoPreview = e.target.result;
                                            };
                                            reader.readAsDataURL($refs.photo.files[0]);
                                            hasNewPhoto = true;
                                    " />

                        <!-- Existing Logo Preview (when not uploading new one) -->
                        <div x-show="! photoPreview && !hasNewPhoto" class="h-24 w-24 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                            @if($logo)
                                <img src="{{ $logoUrl }}" class="h-24 w-24 object-contain">
                            @else
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 16 4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2"/></svg>
                            @endif
                        </div>

                        <!-- New Profile Photo Preview -->
                        <div class="mt-2" x-show="photoPreview && hasNewPhoto" style="display: none;">
                            <div class="relative h-24 w-24">
                                <span class="block h-24 w-24 rounded-lg bg-cover bg-center bg-no-repeat"
                                    x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                </span>
                                
                                <!-- Loading State -->
                                <div wire:loading wire:target="logo" class="absolute inset-0 bg-gray-900/60 rounded-lg flex items-center justify-center">
                                    <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12zm2 5.291A7.96 7.96 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938z"/></svg>
                                </div>
                            </div>
                        </div>

                        <x-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.photo.click()">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-8-4-4m0 0L8 8m4-4v12"/></svg>
                            @lang('app.upload')
                        </x-secondary-button>

                        <!-- Remove button for new photo -->
                        <x-secondary-button class="mt-2" type="button" x-on:click.prevent="clearFileInput()" x-show="hasNewPhoto" x-cloak>
                            @lang('modules.settings.removeLogo')
                        </x-secondary-button>

                        <!-- Remove button for existing logo -->
                        @if ($logo)
                            <x-secondary-button type="button" class="mt-2" wire:click="removeLogo" x-on:click.prevent="clearFileInput()" x-show="!hasNewPhoto" x-cloak>
                                @lang('modules.settings.removeLogo')
                            </x-secondary-button>
                        @endif

                        <x-input-error for="logo" class="mt-2" />
                    </div>
                </div>

                <!-- Commission Type & Value -->
                <div class="w-full">
                    <div>
                        <x-label for="commissionValue" :value="__('modules.settings.commissionValue')" class="mb-1" />
                        <div class="relative">
                            <x-input id="commissionValue" type="number" step="0.01" min="0" wire:model.live="commissionValue" class="w-full pr-10" />
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-800 dark:text-gray-300 text-sm">{{ $commissionType === 'percent' ? '%' : '' }}</span>
                            </div>
                        </div>
                        <x-input-error for="commissionValue" class="mt-1 text-xs" />
                    </div>
                </div>

                <!-- Status Toggle -->
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <x-label for="isActive" :value="__('modules.settings.activeStatus')" class="!mb-1" />
                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.activeStatusDescription')</p>
                    </div>
                    <button type="button" wire:click="$toggle('isActive')"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-skin-base focus:ring-offset-2 {{ $isActive ? 'bg-skin-base' : 'bg-gray-200 dark:bg-gray-600' }}">
                        <span class="sr-only">@lang('modules.settings.toggleStatus')</span>
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $isActive ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                <!-- Form Actions -->
                <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
                    <x-button type="submit" wire:loading.attr="disabled" wire:target="savePlatform">
                        <span wire:loading.remove wire:target="savePlatform">
                            {{ $editingIndex !== null ? __('app.update') : __('app.save') }}
                        </span>
                        <span wire:loading wire:target="savePlatform">
                            @lang('app.saving')
                        </span>
                    </x-button>
                    <x-button-cancel wire:click="hideAddForm" wire:loading.attr="disabled">
                        @lang('app.cancel')
                    </x-button-cancel>
                </div>
            </form>
        </x-slot>
    </x-right-modal>

    <!-- Delete Confirmation Modal -->
    <x-confirmation-modal wire:model.live="confirmDeleteModal">
        <x-slot name="title">
            @lang('modules.settings.deleteDeliveryApp')
        </x-slot>

        <x-slot name="content">
            @lang('modules.settings.deleteDeliveryAppConfirmation')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmDeleteModal', false)" wire:loading.attr="disabled">
                @lang('app.cancel')
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="deletePlatform" wire:loading.attr="disabled">
                @lang('app.delete')
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>