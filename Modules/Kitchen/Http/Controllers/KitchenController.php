<?php

namespace Modules\Kitchen\Http\Controllers;

use App\Models\KotPlace;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KitchenController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        abort_if(!in_array('Kitchen', restaurant_modules()), 403);

        return view('kitchen::kitchen_places.index');
    }

    public function showKot($id)
    {
        abort_if(!in_array('Kitchen', restaurant_modules()), 403);

        $kot = KotPlace::findOrFail($id);
        return view('kitchen::kitchen_places.kitchen-details', compact('kot'));
    }

    public function allKot()
    {
        abort_if(!in_array('Kitchen', restaurant_modules()), 403);

        return view('kitchen::all_kot.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(!in_array('Kitchen', restaurant_modules()), 403);

        // Redirect to kitchen places index since create view is handled by modal
        return redirect()->route('kitchen.kitchen-places.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(!in_array('Kitchen', restaurant_modules()), 403);

        // Redirect to kitchen places index
        return redirect()->route('kitchen.kitchen-places.index');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        abort_if(!in_array('Kitchen', restaurant_modules()), 403);

        // Redirect to kitchen places index since individual kitchen show view is not needed
        return redirect()->route('kitchen.kitchen-places.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort_if(!in_array('Kitchen', restaurant_modules()), 403);

        // Redirect to kitchen places index since edit view is handled by modal
        return redirect()->route('kitchen.kitchen-places.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort_if(!in_array('Kitchen', restaurant_modules()), 403);

        // Redirect to kitchen places index
        return redirect()->route('kitchen.kitchen-places.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_if(!in_array('Kitchen', restaurant_modules()), 403);

        // Redirect to kitchen places index
        return redirect()->route('kitchen.kitchen-places.index');
    }
}
