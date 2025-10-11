<div>
    <form wire:submit="editFrontFeature">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="customerName" value="{{ __('modules.customer.name') }}" />
                <x-input id="customerName" class="block mt-1 w-full" type="text" autofocus wire:model='customerName' placeholder="{{ __('placeholders.customerName') }}" />
                <x-input-error for="customerName" class="mt-2" />
            </div>

            <div>
                <x-label for="customerEmail" value="{{ __('modules.customer.email') }}" />
                <x-input id="customerEmail" class="block mt-1 w-full" type="email" autofocus wire:model='customerEmail' placeholder="{{ __('placeholders.customerEmail') }}" />
                <x-input-error for="customerEmail" class="mt-2" />
            </div>

            <div>
                <x-label for="customerPhone" value="{{ __('modules.customer.phone') }}" />
                <x-input id="customerPhone" class="block mt-1 w-full" type="tel" autofocus wire:model='customerPhone' placeholder="{{ __('placeholders.customerPhone') }}" />
                <x-input-error for="customerPhone" class="mt-2" />
            </div>

            <div>
                <x-label for="customerAddress" value="{{ __('modules.customer.address') }}" />
                <x-input id="customerAddress" class="block mt-1 w-full" type="text" autofocus wire:model='customerAddress' placeholder="{{ __('placeholders.customerAddress') }}" />
                <x-input-error for="customerAddress" class="mt-2" />
            </div>

        </div>

        <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideEditCustomer')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
