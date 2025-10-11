@php
     $modules = collect(\Froiden\Envato\Functions\EnvatoUpdate::plugins())->sortByDesc('number_of_sales');
@endphp
@if($modules->count() > 0)
<div class="bg-gradient-to-b from-gray-50 to-white py-10">
    <!-- Section Separator -->
    <div class="container mx-auto px-2 max-w-6xl mb-8">
        <div class="flex items-center justify-center">
            <div class="flex-grow border-t border-gray-300"></div>
            <div class="mx-4 bg-white px-6 py-3 rounded-full shadow-sm border border-gray-200">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h6m-6 4h6m-6 4h6"/>
                    </svg>
                    <span class="text-sm font-semibold text-gray-700">Premium Add-ons Available</span>
                </div>
            </div>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>
        <p class="text-center text-xs text-gray-500 mt-3">
            Explore additional modules to extend your Tabletrack functionality
        </p>
    </div>

    <div class="container mx-auto px-2 max-w-6xl">
        <!-- Design 1: Modern Cards with Gradient -->
        <div class="mb-10">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-stretch">
                @foreach($modules as $module)
                    <a href="{{ $module['product_link'] }}" target="_blank" class="block">
                        <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 flex flex-col items-center p-4 relative group cursor-pointer">
                            <!-- Price Badge -->
                            <span class="absolute top-3 right-3 bg-blue-600 text-white text-xs font-semibold px-2 py-0.5 rounded-full shadow">
                                ${{ $module['price'] }}
                            </span>

                            <!-- Trending Badge -->
                            @if($module['trending'])
                                <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full shadow">
                                    Trending
                                </span>
                            @endif

                            <!-- Thumbnail -->
                            <div class="bg-gray-100 rounded-lg shadow p-1 mb-3 flex items-center justify-center">
                                <img src="{{ $module['product_thumbnail'] }}" alt="{{ $module['product_name'] }}"
                                     class="w-[80px] h-[80px] object-cover rounded-md" />
                            </div>

                            <!-- Content -->
                            <h3 class="text-base font-bold text-gray-900 text-center mb-1">{{ str_replace('for Tabletrack', '', $module['product_name']) }}</h3>

                            <!-- Version and Sales -->
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                    v{{ $module['version'] }}
                                </span>
                                <span class="text-xs text-gray-500 bg-green-100 px-2 py-0.5 rounded">
                                    {{ $module['number_of_sales'] }} sales
                                </span>
                            </div>

                            <!-- Rating -->
                            @if($module['rating_count'] > 0)
                                <div class="flex items-center mb-2">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3 h-3 {{ $i <= $module['rating'] ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-xs text-gray-500 ml-1">({{ $module['rating_count'] }})</span>
                                </div>
                            @else
                                <div class="flex items-center mb-2">
                                    <span class="text-xs text-gray-400">No ratings yet</span>
                                </div>
                            @endif

                            <!-- Summary -->
                            @if($module['summary'])
                                <p class="text-xs text-gray-600 text-center mb-3 line-clamp-3">{{ $module['summary'] }}</p>
                            @endif

                            <!-- Footer -->
                            <div class="flex items-center justify-center w-full mt-auto">
                                <span class="text-xs text-blue-600 font-medium">Click to learn more →</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endif