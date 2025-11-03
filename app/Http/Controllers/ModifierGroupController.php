<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModifierGroupController extends Controller
{
    public function index()
    {
        return view('modifier_groups.index');
    }

    public function create()
    {
        return view('modifier_groups.create');
    }

    public function edit($id)
    {
        
        return view('modifier_groups.edit', compact('id'));
    }
}
