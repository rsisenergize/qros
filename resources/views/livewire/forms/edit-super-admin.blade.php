<div>
    <!-- Edit User Modal -->
    <x-right-modal wire:model.live="showEditModal">
        <x-slot name="title">
            {{ __("app.update") }} {{ __("app.user") }}
        </x-slot>

        <x-slot name="content">
            <form wire:submit="submitForm">
                @csrf
                <div class="space-y-4">

                    <div>
                        <x-label for="editUserName" value="{{ __('modules.staff.name') }}" />
                        <x-input id="editUserName" class="block mt-1 w-full" type="text" autofocus wire:model='userName' />
                        <x-input-error for="userName" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="editUserEmail" value="{{ __('modules.customer.email') }}" />
                        <x-input id="editUserEmail" class="block mt-1 w-full" type="email" autofocus wire:model='userEmail' />
                        <x-input-error for="userEmail" class="mt-2" />
                    </div>

                </div>

                <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
                    <x-button type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>@lang('app.update')</span>
                        <span wire:loading>@lang('app.updating')...</span>
                    </x-button>
                    <x-button-cancel wire:click="closeModal"
                        wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
                </div>
            </form>
        </x-slot>
    </x-right-modal>
</div>
