<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\LanguageSetting;

class LanguageSwitcher extends Component
{

    public function setLanguage($locale)
    {
        session(['customer_locale' => $locale]);
        $language = LanguageSetting::where('language_code', $locale)->first();
        $isRtl = ($language->is_rtl == 1);
        session(['customer_is_rtl' => $isRtl]);

        $this->js('window.location.reload()');

    }

    public function render()
    {
        $locale = session('customer_locale') ?? global_setting()->locale;

        $activeLanguage = LanguageSetting::where('language_code', $locale)->first();

        return view('livewire.shop.language-switcher', [
            'activeLanguage' => $activeLanguage,
        ]);
    }

}
