<div class="grid grid-cols-1 gap-6 p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">

<div>

    <div class="px-4 mb-4">
        <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.settings.cancelSettings')</h3>
        <x-button type='button' wire:click="showAddKotReason" >@lang('app.add')</x-button>
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
                                    @lang('modules.settings.reason')
                                </th>

                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.cancellationTypes')
                                </th>

                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                    @lang('app.action')
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='reason-list-{{ microtime() }}'>

                            @forelse ($reasons as $reason)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='reason-{{ $reason->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $reason->reason }}
                                </td>

                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    <div class="flex items-center gap-2">
                                        @if($reason->cancel_order)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-300">
                                                @lang('modules.order.order')
                                            </span>
                                        @endif
                                        @if($reason->cancel_kot)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                                @lang('menu.kot')
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right">
                                    @if(strtolower($reason->reason) === 'other')
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">
                                            @lang('modules.settings.default')
                                        </span>
                                    @else
                                        <x-secondary-button wire:click='editReason({{ $reason->id }})' wire:key='reason-edit-{{ $reason->id . microtime() }}'>
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
                                        </x-secondary-button>

                                        <x-danger-button-table wire:click="confirmDelete({{ $reason->id }})"  wire:key='reason-del-{{ $reason->id . microtime() }}'>
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </x-danger-button-table>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="py-2.5 px-4 space-x-6" colspan="3">
                                    @lang('app.noRecordsFound')
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

</div>


<!-- Add Modal -->
<x-right-modal wire:model.live="showAddCancelReasonModal">
<x-slot name="title">
{{ __("modules.settings.addKotCancelReason") }}
</x-slot>

<x-slot name="content">
@livewire('forms.AddKotCancelReason')
</x-slot>

<x-slot name="footer">
<x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
    {{ __('app.close') }}
</x-secondary-button>
</x-slot>
</x-right-modal>

<!-- Edit Modal -->
<x-right-modal wire:model.live="showEditCancelReasonModal">
<x-slot name="title">
{{ __("modules.settings.editKotCancelReason") }}
</x-slot>

<x-slot name="content">
@if($reasonToEdit)
    @livewire('forms.EditKotCancelReason', ['kotCancelReason' => $reasonToEdit], key($reasonToEdit->id))
@endif
</x-slot>

<x-slot name="footer">
<x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
    {{ __('app.close') }}
</x-secondary-button>
</x-slot>
</x-right-modal>



 <x-confirmation-modal wire:model="showDeleteModal">
        <x-slot name="title">
            @lang('modules.settings.deleteReason')?
        </x-slot>

        <x-slot name="content">
            @lang('modules.settings.deleteCancelReasonWarning')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('showDeleteModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>


            <x-danger-button class="ml-3" wire:click='deleteReason' wire:loading.attr="disabled">
                {{ __('app.delete') }}
            </x-danger-button>

         </x-slot>
    </x-confirmation-modal>

</div>
