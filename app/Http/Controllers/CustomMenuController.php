<?php

namespace App\Http\Controllers;

use App\Models\CustomMenu;
use Illuminate\Http\Request;

class CustomMenuController extends Controller
{

    public function index(string $slug)
    {
        return view('landing.custom', ['slug' => $slug]);
    }

}
