<?php

namespace App\Http\Controllers;

use App\Models\Kot;
use App\Helper\Files;
use Illuminate\Support\Facades\Log;
use App\Models\KotPlace;
use App\Models\Printer;

class KotController extends Controller
{
    protected $connector;
    protected $printer;

    public function index()
    {
        abort_if(!in_array('KOT', restaurant_modules()), 303);
        abort_if((!user_can('Manage KOT')), 303);
        return view('kot.index');
    }

    public function printKot($id, $kotPlaceid = null, $width = 80, $thermal = false)
    {
        $kot = Kot::with('items', 'order.waiter', 'table')->find($id);
        $kotPlace = KotPlace::find($kotPlaceid);

        return view('pos.printKot', compact('kot', 'kotPlaceid', 'width', 'thermal', 'kotPlace'));
    }
}
