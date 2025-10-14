<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Inventory', restaurant_modules()), 403);
        abort_if(!user_can('Show Unit'), 403);

        return view('inventory::units.index');
    }

   
}
