 {{-- New Section --}}
 <div>

    @if ($customMenu)
        <div id="{{ Str::slug($customMenu->menu_name) }}"> </div>

        <div>
            <div class="max-w-7xl px-4 lg:px-8 lg:py-5 mx-auto">
                <ul class="flex flex-col gap-4">
                </ul>

                <div id="{{ Str::slug($customMenu->menu_name) }}"
                    class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
                    <!-- Title -->
                    <div class="mx-auto mb-8 lg:mb-14 text-center">
                        <h2 class="text-3xl lg:text-4xl text-gray-800 font-bold dark:text-neutral-200">
                            {{ $customMenu->menu_name }}
                        </h2>
                    </div>
                    <!-- End Title -->

                    <!-- Content -->
                    <div class="text-gray-600 dark:text-neutral-400">
                        {!! $customMenu->menu_content !!}
                    </div>
                    <!-- End Content -->
                </div>
            </div>
        </div>
    @endif
 </div>



    {{-- End New Section --}}
