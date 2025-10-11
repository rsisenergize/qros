<?php

namespace App\Http\Controllers;

use App\Livewire\LandingSite\FooterSetting;
use App\Livewire\Settings\LanguageSettings;
use App\Models\CustomMenu;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use App\Models\LanguageSetting;

class LandingSiteController extends Controller
{

    public function index()
    {
        $settings = GlobalSetting::first();
        $customMenu = CustomMenu::all();
        return view('landing-sites.index', compact('settings', 'customMenu'));
    }

    public function showMenu()
    {
        $customMenu = CustomMenu::orderBy('sort_order')->get();
        $footerSetting = FooterSetting::where('language_id', request()->get('language_id'))->first();
        return view('layouts.landing', compact('customMenu', 'footerSetting'));
    }

}
