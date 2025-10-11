<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Restaurant;
use App\Models\LanguageSetting;

class CustomerSiteMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hash = $request->route('hash');
        
        if ($hash) {
            $restaurant = Restaurant::where('hash', $hash)->first();
    
            if ($restaurant && $restaurant->customer_site_language) {
                // If session has locale (from language switcher), use it
                if (session()->has('locale')) {
                    $locale = session('locale');
                    // Get RTL from the selected language, not from session
                    $language = LanguageSetting::where('language_code', $locale)->first();
                    $rtl = $language?->is_rtl ?? false;
                    // Update session with correct RTL
                    session(['is_rtl' => $rtl]);
                    session()->forget('isRtl'); // Clear admin session
                } else {
                    // First visit - use restaurant's customer_site_language directly
                    $locale = $restaurant->customer_site_language;
                    
                    // Get is_rtl from language settings
                    $language = LanguageSetting::where('language_code', $locale)->first();
                    $rtl = $language?->is_rtl ?? false;
                    
                    // Set session for consistency
                    session([
                        'customer_site_language' => $locale,
                        'is_rtl' => $rtl,
                    ]);
                    session()->forget('isRtl'); // Clear admin session
                }
                
                App::setLocale($locale);
            }
        }
    
        return $next($request);
    }
}