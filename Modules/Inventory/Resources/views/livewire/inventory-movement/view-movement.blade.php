<div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
    <!-- Header Section with improved visual hierarchy -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    {{ $movement->item->name }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    {{ $movement->item->category->name }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    wire:click="$dispatch('showEditMovementModal', { movementId: {{ $movement->id }} })"
                    class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    {{ __('app.update') }}
                </button>
                <span @class([
                    'px-4 py-2 text-sm font-semibold rounded-full inline-flex items-center gap-2',
                    'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' => $movement->transaction_type === 'in',
                    'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' => $movement->transaction_type === 'out',
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' => $movement->transaction_type === 'waste',
                    'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' => $movement->transaction_type === 'transfer',
                ])>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($movement->transaction_type === 'in')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                        @elseif($movement->transaction_type === 'out')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
                        @elseif($movement->transaction_type === 'waste')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        @endif
                    </svg>
                    {{ __('inventory::modules.movements.types.' . $movement->transaction_type) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Details Section with improved layout -->
    <div class="px-6 py-4">
        <dl class="grid gap-4">
            <div class="grid grid-cols-3 items-center py-3 border-b border-gray-100 dark:border-gray-700">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                    {{ __('inventory::modules.movements.fields.quantity') }}
                </dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white col-span-2">
                    {{ $movement->quantity }} {{ $movement->item->unit->symbol }}
                </dd>
            </div>

            <div class="grid grid-cols-3 items-center py-3 border-b border-gray-100 dark:border-gray-700">  
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('inventory::modules.stock.unitPurchasePrice') }}
                </dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white col-span-2">
                    {{ $movement->unit_purchase_price }} {{ restaurant()->currency->currency_code }} / {{ $movement->item->unit->symbol }}
                </dd>
            </div>

            <div class="grid grid-cols-3 items-center py-3 border-b border-gray-100 dark:border-gray-700">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ __('inventory::modules.movements.fields.date_time') }}
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                    {{ $movement->created_at->timezone(timezone())->translatedFormat('M d, Y h:i A') }}
                </dd>
            </div>

            <div class="grid grid-cols-3 items-center py-3 border-b border-gray-100 dark:border-gray-700">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ __('inventory::modules.movements.fields.added_by') }}
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                    {{ $movement->addedBy->name ?? '--' }}
                </dd>
            </div>

            @if($movement->transaction_type === 'in')
                <div class="grid grid-cols-3 items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        {{ __('inventory::modules.movements.fields.supplier') }}
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                        {{ $movement->supplier->name ?? '--' }}
                    </dd>
                </div>
                <div class="grid grid-cols-3 items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ __('inventory::modules.stock.expirationDate') }}
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                        {{ $movement->expiration_date ? $movement->expiration_date->translatedFormat('M d, Y') : '--' }}
                    </dd>
                </div>
            @endif

            @if($movement->transaction_type === 'waste')
                <div class="grid grid-cols-3 items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        {{ __('inventory::modules.movements.fields.waste_reason') }}
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                        {{ __('inventory::modules.movements.waste_reasons.' . $movement->waste_reason) }}
                    </dd>
                </div>
            @endif

            <div class="grid grid-cols-3 items-center py-3 border-b border-gray-100 dark:border-gray-700">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ __('inventory::modules.movements.fields.source_branch') }}
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                    {{ $movement->sourceBranch->name }}
                </dd>
            </div>

            @if($movement->transaction_type === 'transfer')
                <div class="grid grid-cols-3 items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        {{ __('inventory::modules.movements.fields.transfer_branch') }}
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white col-span-2">
                        {{ $movement->transferBranch->name }}
                    </dd>
                </div>
            @endif
        </dl>
    </div>

</div> 