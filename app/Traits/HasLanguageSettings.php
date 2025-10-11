<?php

namespace App\Traits;

use App\Models\Restaurant;
use App\Models\Table;
use App\Models\Order;
use App\Models\LanguageSetting;
use Illuminate\Support\Facades\App;

trait HasLanguageSettings
{
    protected function applyLanguageSettings(): void
    {
        $restaurant = $this->getRestaurantForLanguage();
        
        if ($restaurant && $restaurant->customer_site_language) {
            $this->setLanguageAndRTL($restaurant);
        }
    }
    
    private function getRestaurantForLanguage(): ?Restaurant
    {
        // First try to get restaurant from query parameter (for table routes)
        if (request()->filled('hash')) {
            $restaurant = Restaurant::where('hash', request('hash'))->first();
            if ($restaurant) return $restaurant;
        }
        
        // Try route parameter
        $hash = request()->route('hash');
        if ($hash) {
            $restaurant = Restaurant::where('hash', $hash)->first();
            if ($restaurant) return $restaurant;
        }
        
        // Try to get from table hash (for tableOrder method)
        if ($hash) {
            $table = Table::where('hash', $hash)->first();
            if ($table) return $table->branch->restaurant;
        }
        
        // Try to get from order UUID (for orderSuccess method)
        $uuid = request()->route('id');
        if ($uuid) {
            $order = Order::where('uuid', $uuid)->first();
            if ($order) return $order->branch->restaurant;
        }
        
        return null;
    }
    
    private function setLanguageAndRTL(Restaurant $restaurant): void
    {
        if (session()->has('customer_locale')) {
            $locale = session('customer_locale');
            // Get RTL from the selected language, not from session
            $language = LanguageSetting::where('language_code', $locale)->first();
            $rtl = $language?->is_rtl ?? false;
            // Update session with correct RTL
            session(['customer_is_rtl' => $rtl]);
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
                'customer_is_rtl' => $rtl,
            ]);
            session()->forget('isRtl'); // Clear admin session
        }
        
        App::setLocale($locale);
    }
} 