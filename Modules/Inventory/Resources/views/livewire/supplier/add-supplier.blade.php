<div>
    <form wire:submit="submitForm"> 
        @csrf
        <div class="grid grid-cols-1 gap-4">
            <div>
                <x-label for="name" value="{{ __('inventory::modules.supplier.name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" autofocus wire:model='name' />
                <x-input-error for="name" class="mt-2" />
            </div>

            <div>
                <x-label for="email" value="{{ __('inventory::modules.supplier.email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" autofocus wire:model='email' />
                <x-input-error for="email" class="mt-2" />
            </div>
            
            <div>
                <x-label for="phone" value="{{ __('inventory::modules.supplier.phone') }}" />
                <x-input id="phone" class="block mt-1 w-full" type="text" autofocus wire:model='phone' />
                <x-input-error for="phone" class="mt-2" />
            </div>
 
            <div>
                <x-label for="address" value="{{ __('inventory::modules.supplier.address') }}" />
                <x-textarea id="address" class="block mt-1 w-full" autofocus wire:model='address' />
                <x-input-error for="address" class="mt-2" />
            </div>

        </div>

        <div class="flex w-full pb-4 space-x-4 mt-6">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideAddSupplier')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>


