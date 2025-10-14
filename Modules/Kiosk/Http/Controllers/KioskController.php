<?php

namespace Modules\Kiosk\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Http\Controllers\ShopController;
use App\Models\Branch;
use App\Models\Order;

class KioskController extends ShopController
{
    /**
     * Display a listing of the resource.
     */
    public function index($hash)
    {
        $restaurant = Restaurant::where('hash', $hash)->firstOrFail();
        $shopBranch = $this->getShopBranch($restaurant);

        return view('kiosk::index', compact('restaurant', 'shopBranch'));
    }

    /**
     * Get the branch for the shop based on request or default to first branch
     */
    private function getShopBranch(Restaurant $restaurant): Branch
    {
        if (request()->filled('branch')) {
            $branchParam = request('branch');

            // Try to find by unique_hash first, then by ID
            $branch = Branch::withoutGlobalScopes()->where('unique_hash', $branchParam)->first();

            if (!$branch) {
                $branch = Branch::withoutGlobalScopes()->find($branchParam);
            }

            return $branch;
        }

        return $restaurant->branches->first();
    }

    /**
     * Get enabled package modules and features for the restaurant
     */
    private function getPackageModules(?Restaurant $restaurant): array
    {
        if (!$restaurant?->package) {
            return [];
        }

        $modules = $restaurant->package->modules->pluck('name')->toArray();
        $additionalFeatures = json_decode($restaurant->package->additional_features ?? '[]', true);

        return array_merge($modules, $additionalFeatures);
    }

    public function orderConfirmation($uuid)
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();
        $restaurant = $order->branch->restaurant;
        $shopBranch = $order->branch;
        return view('kiosk::order-confirmation', compact('restaurant', 'shopBranch', 'order'));
    }
}
