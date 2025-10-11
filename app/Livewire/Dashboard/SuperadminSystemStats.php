<?php

namespace App\Livewire\Dashboard;

use App\Models\Restaurant;
use App\Models\User;
use App\Models\RestaurantPayment;
use App\Models\GlobalSubscription;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SuperadminSystemStats extends Component
{
    public $totalRestaurants;
    public $totalUsers;
    public $totalRevenue;
    public $activeRestaurants;
    public $inactiveRestaurants;
    public $totalSubscriptions;
    public $activeSubscriptions;
    public $trialSubscriptions;
    public $expiredSubscriptions;
    public $monthlyGrowth;

    public function mount()
    {
        $this->calculateSystemStats();
    }

    private function calculateSystemStats()
    {
        // Restaurant statistics
        $this->totalRestaurants = Restaurant::count();
        $this->activeRestaurants = Restaurant::where('is_active', true)->count();
        $this->inactiveRestaurants = Restaurant::where('is_active', false)->count();

        // User statistics
        $this->totalUsers = User::count();

        // Revenue statistics (from restaurant payments)
        $this->totalRevenue = RestaurantPayment::where('status', 'paid')->sum('amount');

        // Subscription statistics
        $this->totalSubscriptions = GlobalSubscription::count();
        $this->activeSubscriptions = GlobalSubscription::where('subscription_status', 'active')->count();
        $this->trialSubscriptions = 0; // No trial status in current system
        $this->expiredSubscriptions = GlobalSubscription::where('subscription_status', 'inactive')->count();

        // Monthly growth calculation (restaurant growth)
        $thisMonthRestaurants = Restaurant::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $lastMonthRestaurants = Restaurant::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $this->monthlyGrowth = $lastMonthRestaurants > 0
            ? (($thisMonthRestaurants - $lastMonthRestaurants) / $lastMonthRestaurants) * 100
            : 0;
    }

    public function render()
    {
        return view('livewire.dashboard.superadmin-system-stats');
    }
}
