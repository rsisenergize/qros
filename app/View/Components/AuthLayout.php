<?php

namespace App\View\Components;

use App\Models\GlobalSetting;
use App\Models\LanguageSetting;
use Illuminate\Support\Facades\App;
use Illuminate\View\Component;
use Illuminate\View\View;

class AuthLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {

        // SAAS
        if (module_enabled('Subdomain')) {
            $restaurant = getRestaurantBySubDomain();
            $globalSetting = $restaurant ?? GlobalSetting::first();
        } else {
            $globalSetting = global_setting();
        }

        $appTheme = $globalSetting;

        $locale = session('customer_locale') ?? $globalSetting->locale;
        App::setLocale($locale);
        
        // Handle RTL for auth layout
        $language = LanguageSetting::where('language_code', $locale)->first();
        $isRtl = $language?->is_rtl ?? false;
        session(['customer_is_rtl' => $isRtl]);

        return view('layouts.auth', [
            'globalSetting' => $globalSetting,
            'appTheme' => $appTheme,
        ]);
    }
}
