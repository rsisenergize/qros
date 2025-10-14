<div>

     <div class="px-4 mb-4">
        <x-button type='button' wire:click="showAddRole" >@lang('app.manage') @lang('app.role')</x-button>
    </div>
    <div class="overflow-hidden shadow mb-8">
        <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600" wire:key="role-settings-table">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th scope="col"
                    class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                        <div class="text-xs font-light">@lang('app.user')</div>
                        <div class="text-sm font-medium">@lang('app.permission')</div>
                    </th>

                    @foreach ($roles as $item)
                        <th scope="col"
                        class="py-2.5 px-4 text-xs font-medium ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400">
                            <div class="text-xs font-light">@lang('app.role')</div>
                            <div class="text-sm font-medium">{{ ($item->display_name) }}</div>
                        </th>           
                    @endforeach
                </tr>     

            </thead>

            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                @foreach ($permissions as $module)
                    <tr>
                        <td class="bg-gray-50 p-4 text-sm font-semibold ltr:text-left rtl:text-right text-gray-500 uppercase dark:text-gray-400 dark:bg-gray-900" colspan="{{ (count($roles) + 1) }}">
                            {{ __('permissions.modules.'.$module->name) }}
                        </td>
                    </tr>

                    @foreach ($module->permissions as $item)
                    
                    @php
                        $permissionRoles = $item->roles->pluck('name')->toArray();
                    @endphp

                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='perms-item-{{ $item->id . microtime() }}'>
                        <td class="flex items-center px-4 py-2 mr-12 space-x-6 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('permissions.permissions.'.$item->name) }}
                        </td>
        
                        @foreach ($roles as $role)
                        <td class="px-4 py-2 mr-12 space-x-6">
                            @if (!in_array($role->name, $permissionRoles))
                                <x-secondary-button-table wire:click='setPermission({{ $role->id }}, {{ $item->id }})'>
                                    &plus;
                                </x-secondary-button-table>
                            @else
                                <x-danger-button-table wire:click='removePermission({{ $role->id }}, {{ $item->id }})'>
                                    &minus;
                                </x-danger-button-table>
                            @endif
                        </td>                
                        @endforeach
        
                    </tr>
                    @endforeach
                @endforeach

            </tbody>
        
        </table>
    </div>

    <x-dialog-modal wire:model.live="showAddRoleModal">
        <x-slot name="title">
            {{ __("app.manage") }} {{ __("app.role") }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">
                <!-- Existing Roles List -->
                <div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        @lang('app.role')
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        @lang('app.action')
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($roles as $index => $role)
                                    @php
                                        $protectedRoles = ['Admin', 'Super Admin', 'Branch Head', 'Waiter', 'Chef'];
                                        $isProtected = in_array($role->display_name, $protectedRoles);
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($inlineEditingRoleId === $role->id)
                                                <div class="flex items-center space-x-2">
                                                    <x-input 
                                                        class="flex-1" 
                                                        type="text" 
                                                        wire:model="inlineEditingRoleName" 
                                                        wire:keydown.enter="saveInlineEdit"
                                                        wire:keydown.escape="cancelInlineEdit"
                                                        autofocus
                                                    />
                                                    <x-secondary-button size="sm" wire:click="saveInlineEdit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </x-secondary-button>
                                                    <x-secondary-button size="sm" wire:click="cancelInlineEdit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </x-secondary-button>
                                                </div>
                                                @error('inlineEditingRoleName')
                                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                                @enderror
                                            @else
                                                <div class="flex items-center space-x-2">
                                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white {{ $isProtected ? '' : 'cursor-pointer hover:text-blue-600 dark:hover:text-blue-400' }}" 
                                                         @if(!$isProtected) wire:click="startInlineEdit({{ $role->id }}, '{{ $role->display_name }}')" @endif>
                                                        {{ $role->display_name }}
                                                    </h3>
                                                    @if(!$isProtected)
                                                        <svg class="w-4 h-4 text-gray-400 cursor-pointer hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:click="startInlineEdit({{ $role->id }}, '{{ $role->display_name }}')">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                            <div class="flex items-center justify-end ">
                                                @if($isProtected)
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        @lang('messages.protectedRole')
                                                    </span>
                                                @else
                                                    <x-danger-button size="sm" wire:click="deleteRole({{ $role->id }})">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </x-danger-button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add New Role Form -->
                <div class="">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ __("app.addNew") }} {{ __("app.role") }}
                    </h3>
                    <form wire:submit.prevent="createRole">
                        <!-- Display Name -->
                        <div class="mb-4">
                            <x-label for="newRoleDisplayName" value="{{ __('app.displayName') }}" required />
                            <x-input id="newRoleDisplayName" class="block mt-1 w-full" type="text" wire:model='newRoleDisplayName' autocomplete="off" placeholder="{{ __('app.enter') }} {{ __('app.displayName') }}" />
                            <x-input-error for="newRoleDisplayName" class="mt-2" />
                        </div>

                        <!-- Copy Permissions From Role -->
                        <div class="mb-4">
                            <x-label for="copyFromRole" value="{{ __('messages.copyPermissionsFrom') }}" />
                            <x-select class="mt-1 block w-full" wire:model="copyFromRole">
                                <option value="">@lang('app.dontCopyPermissions')</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ __($role->display_name) }}</option>
                                @endforeach
                            </x-select>
                        </div>

                        <!-- Modal Buttons -->
                        <div class="flex justify-end space-x-2">
                            <x-secondary-button wire:click="$set('showAddRoleModal', false)" wire:loading.attr="disabled">
                                {{ __('app.cancel') }}
                            </x-secondary-button>
                            <x-button type="submit" wire:loading.attr="disabled">
                                {{ __('app.create') }} {{ __('app.role') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </x-slot>
    </x-dialog-modal>

    <!-- Role Reassignment Modal for Delete -->
    <x-dialog-modal wire:model.live="showReassignModal">
        <x-slot name="title">
            {{ __("app.delete") }} {{ __("app.role") }}: {{ $deletingRoleName }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <!-- Warning Message -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                {{ __("app.warning") }}
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>{{ __("app.roleDeleteWarning") }}</p>
                                <p class="mt-1">{{ __("app.selectNewRoleForUsers") }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reassign to role -->
                <div>
                    <x-label for="reassignRoleId" value="{{ __('app.reassignUsersTo') }}" required />
                    <x-select class="mt-1 block w-full" wire:model="reassignRoleId">
                        <option value="">{{ __("app.selectRole") }}</option>
                        @foreach ($roles as $role)
                            @if($role->id != $deletingRoleId)
                                <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                            @endif
                        @endforeach
                    </x-select>
                    <x-input-error for="reassignRoleId" class="mt-2" />
                </div>

                <!-- Modal Buttons -->
                <div class="flex justify-end space-x-2">
                    <x-secondary-button wire:click="$set('showReassignModal', false)" wire:loading.attr="disabled">
                        {{ __('app.cancel') }}
                    </x-secondary-button>
                    <x-danger-button wire:click="confirmDeleteRole" wire:loading.attr="disabled">
                        {{ __('app.delete') }} {{ __('app.role') }}
                    </x-danger-button>
                </div>
            </div>
        </x-slot>
        
    </x-dialog-modal>

</div>
