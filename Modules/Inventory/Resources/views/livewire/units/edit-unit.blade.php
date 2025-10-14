<form wire:submit="submitForm">
    @csrf
    <div class="space-y-4">
       
        <div>
            <x-label for="unitName" value="{{ __('inventory::modules.unit.unitName') }}" />
            <x-input id="unitName" class="block mt-1 w-full" type="text" placeholder="{{ __('inventory::placeholders.unitNamePlaceholder') }}" name="unitName" wire:model='unitName' />
            <x-input-error for="unitName" class="mt-2" />
        </div>

        <div>
            <x-label for="unitSymbol" value="{{ __('inventory::modules.unit.unitSymbol') }}" />
            <x-input id="unitSymbol" class="block mt-1 w-full" type="text" placeholder="{{ __('inventory::placeholders.unitSymbolPlaceholder') }}" name="unitSymbol" wire:model='unitSymbol' />
            <x-input-error for="unitSymbol" class="mt-2" />
        </div>
    </div>
       
    <div class="flex w-full pb-4 space-x-4 mt-6">
        <x-button>@lang('app.save')</x-button>
        <x-secondary-button wire:click="$dispatch('hideEditUnitModal')" wire:loading.attr="disabled">@lang('app.cancel')</x-secondary-button>
    </div>
</form>
