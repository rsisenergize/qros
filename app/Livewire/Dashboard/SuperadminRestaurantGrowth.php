<?php

namespace App\Livewire\Dashboard;

use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SuperadminRestaurantGrowth extends Component
{
    public $totalRestaurants;
    public $newThisMonth;
    public $percentChange;
    public $growthData;
    public $recentRestaurants;

    public function mount()
    {
        $this->calculateGrowthMetrics();
        $this->getRecentRestaurants();
    }

    private function calculateGrowthMetrics()
    {
        // Total restaurants
        $this->totalRestaurants = Restaurant::count();

        // New restaurants this month
        $this->newThisMonth = Restaurant::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // New restaurants last month
        $lastMonthCount = Restaurant::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        // Calculate percentage change
        $growthDifference = ($this->newThisMonth - $lastMonthCount);
        $this->percentChange = ($growthDifference / ($lastMonthCount == 0 ? 1 : $lastMonthCount)) * 100;

        // Get growth data for chart (last 12 months)
        $this->growthData = Restaurant::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as new_restaurants')
        )
            ->where('created_at', '>=', now()->subMonths(12)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
    }

    private function getRecentRestaurants()
    {
        $this->recentRestaurants = Restaurant::with(['package', 'country'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.superadmin-restaurant-growth');
    }
}
