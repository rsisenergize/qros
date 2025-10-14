<form wire:submit="submitForm">
    @csrf
    <div class="space-y-4">
       
        <div>
            <x-label for="itemCategoryName" value="{{ __('inventory::modules.itemCategory.itemCategoryName') }}" />
            <x-input id="itemCategoryName" class="block mt-1 w-full" type="text" placeholder="{{ __('inventory::placeholders.itemCategoryNamePlaceholder') }}" name="itemCategoryName" wire:model='itemCategoryName' />
            <x-input-error for="itemCategoryName" class="mt-2" />
        </div>

    </div>
       
    <div class="flex w-full pb-4 space-x-4 mt-6">
        <x-button>@lang('app.save')</x-button>
        <x-secondary-button wire:click="$dispatch('hideEditItemCategory')" wire:loading.attr="disabled">@lang('app.cancel')</x-secondary-button>
    </div>
</form>
    