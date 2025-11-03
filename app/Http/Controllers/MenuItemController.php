<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuItemController extends Controller
{

    public function index()
    {
        abort_if(!in_array('Menu Item', restaurant_modules()), 403);
        abort_if((!user_can('Show Menu Item')), 403);
        return view('menu_items.index');
    }

    public function bulkImport()
    {
        abort_if(!in_array('Menu Item', restaurant_modules()), 403);
        abort_if((!user_can('Create Menu Item')), 403);
        return view('menu_items.bulk-import');
    }

    public function create()
    {
        return view('menu_items.create');
    }

    public function edit($menuItemId)
    {
        return view('menu_items.edit', compact('menuItemId'));
    }
}
