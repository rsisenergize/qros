<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="userName" value="{{ __('modules.staff.name') }}" />
                <x-input id="userName" class="block mt-1 w-full" type="text" autofocus wire:model='userName' />
                <x-input-error for="userName" class="mt-2" />
            </div>

            <div>
                <x-label for="userEmail" value="{{ __('modules.customer.email') }}" />
                <x-input id="userEmail" class="block mt-1 w-full" type="email" autofocus wire:model='userEmail' />
                <x-input-error for="userEmail" class="mt-2" />
            </div>

        <div>
            <x-label for="userPassword" value="{{ __('modules.staff.password') }}" />
            <x-input id="userPassword" class="block mt-1 w-full" type="password" autofocus
                wire:model='userPassword' />
            <x-input-error for="userPassword" class="mt-2" />
        </div>


        </div>

        <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
            <x-button type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove>@lang('app.save')</span>
                <span wire:loading>@lang('app.saving')...</span>
            </x-button>
            <x-button-cancel wire:click="$dispatch('hideAddMember')"
                wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
