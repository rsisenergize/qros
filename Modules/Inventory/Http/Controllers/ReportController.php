<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Inventory\Entities\InventoryMovement;
use Modules\Inventory\Entities\InventoryItem;

class ReportController extends Controller
{
    public function usage(Request $request)
    {
        abort_if(!in_array('Inventory', restaurant_modules()), 403);
        abort_if(!user_can('Show Inventory Report'), 403);
        
        return view('inventory::reports.usage');
    }

    public function turnover()
    {
        abort_if(!in_array('Inventory', restaurant_modules()), 403);

        return view('inventory::reports.turnover');
    }

    public function forecasting()
    {
        abort_if(!in_array('Inventory', restaurant_modules()), 403);

        return view('inventory::reports.forecasting');
    }

    public function cogs()
    {
        abort_if(!in_array('Inventory', restaurant_modules()), 403);

        return view('inventory::reports.cogs');
    }

    public function profitAndLoss()
    {
        abort_if(!in_array('Inventory', restaurant_modules()), 403);

        return view('inventory::reports.profit-and-loss');
    }
} 