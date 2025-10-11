@props(['id', 'note' => ''])

<div x-data="{
    showNoteInput: false,
    showNotePreview: false,
    noteText: '{{ $note }}',
    showNoteIcon: {{ strlen($note) > 0 ? 'true' : 'false' }},
    saveNote() {
        $wire.updateItemNote('{{ $id }}', this.noteText);
        this.showNoteInput = false;
        this.showNotePreview = false;
        this.showNoteIcon = this.noteText.length > 0;
    },
    showInput() {
        this.showNoteInput = true;
        this.showNotePreview = false;
        this.$nextTick(() => this.$refs.noteInput.focus());
    },
    showPreview() {
        this.showNotePreview = true;
        this.showNoteInput = false;
    }
}" class="inline-flex items-center relative group">

    <!-- Note icon and text when note exists -->
    <div x-show="showNoteIcon && !showNoteInput && !showNotePreview" x-cloak
        class="flex items-center gap-2 cursor-pointer text-skin-base text-xs hover:text-skin-base/80 transition-all duration-200"
        @click="showPreview()"
        title="@lang('modules.order.specialInstructions')">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" stroke="currentColor" fill="none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8-4-4H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-3z"/></svg>
        <span class="truncate max-w-[70px] md:max-w-64 lg:max-w-[70px]">{{ $note }}</span>
    </div>

    <!-- Add note button when no note exists -->
    <button
        x-show="!showNoteIcon && !showNoteInput && !showNotePreview"
        @click="showInput()"
        class="inline-flex items-center gap-1 text-xs pt-1 text-gray-500 hover:text-skin-base transition-colors duration-200"
        title="@lang('modules.order.addNote')">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        @lang('modules.order.addNote')
    </button>

    <!-- Note Preview -->
    <div
        x-show="showNotePreview" x-cloak
        @click.away="showNotePreview = false"
        class="absolute top-0 left-0 z-10">
        <div class="bg-white dark:bg-gray-700 rounded-md shadow-md border border-gray-300 dark:border-gray-600 p-3 w-64 md:w-96">
            <div class="text-sm dark:text-white mb-2 break-all">{{ $note }}</div>
            <div class="flex justify-end gap-2 dark:text-white">
                <button
                    @click="showInput()"
                    class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-500 rounded transition-colors duration-200">
                    <span class="flex items-center gap-x-1">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" stroke="currentColor" fill="none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        @lang('app.update')
                    </span>
                </button>

                <button
                    @click="noteText = ''; saveNote()"
                    class="text-xs px-2 py-1 bg-red-50 hover:bg-red-100 dark:bg-red-700 dark:hover:bg-red-600 text-red-500 dark:text-red-300 rounded transition-colors duration-200"
                    title="@lang('app.delete')">
                    <span class="flex items-center gap-x-1">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" stroke="currentColor" fill="none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @lang('app.delete')
                    </span>
                </button>
                
                <button
                    @click="showNotePreview = false"
                    class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-500 rounded transition-colors duration-200">
                    @lang('app.close')
                </button>
            </div>
        </div>
    </div>

    <!-- Note input -->
    <div
        x-show="showNoteInput" x-cloak
        @click.away="showNoteInput = false"
        class="absolute top-0 left-0 z-10">
        <div class="relative">
            <div class="flex items-center bg-white dark:bg-gray-700 rounded-md shadow-md border border-gray-300 dark:border-gray-600 overflow-hidden">
                <input
                    x-ref="noteInput"
                    x-model="noteText"
                    type="text"
                    class="w-64 md:w-96 p-2 border-none text-base focus:ring-0 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400"
                    placeholder="{{ __('placeholders.addItemNotesPlaceholder') }}"
                    @keydown.enter="saveNote()"
                    autofocus
                />
                <div class="flex items-center gap-1 pr-2">
                    <button
                        @click="saveNote()"
                        class="p-1.5 text-white rounded-md bg-skin-base hover:bg-skin-base/90 transition-colors duration-200"
                        title="@lang('app.save')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/></svg>
                    </button>
                    <button
                        @click="showNoteInput = false"
                        class="p-1.5 text-gray-500 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
                        title="@lang('app.cancel')">
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
