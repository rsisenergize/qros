<?php

namespace App\Livewire\Dashboard;

use App\Models\RestaurantPayment;
use App\Models\GlobalSubscription;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SuperadminRevenueChart extends Component
{
    public $monthlyRevenue;
    public $percentChange;
    public $revenueData;
    public $topRestaurants;
    public $subscriptionStats;

    public function mount()
    {
        $this->calculateRevenueMetrics();
        $this->getTopRestaurants();
        $this->getSubscriptionStats();
    }

    private function calculateRevenueMetrics()
    {
        $startOfMonth = now()->startOfMonth()->startOfDay()->toDateTimeString();
        $tillToday = now()->endOfDay()->toDateTimeString();

        $startOfLastMonth = now()->subMonth()->startOfMonth()->startOfDay()->toDateTimeString();
        $endOfLastMonth = now()->subMonth()->endOfMonth()->endOfDay()->toDateTimeString();

        // Current month revenue from restaurant payments
        $this->monthlyRevenue = RestaurantPayment::whereDate('payment_date_time', '>=', $startOfMonth)
            ->whereDate('payment_date_time', '<=', $tillToday)
            ->where('status', 'paid')
            ->sum('amount');

        // Previous month revenue
        $previousRevenue = RestaurantPayment::whereDate('payment_date_time', '>=', $startOfLastMonth)
            ->whereDate('payment_date_time', '<=', $endOfLastMonth)
            ->where('status', 'paid')
            ->sum('amount');

        // Calculate percentage change
        $revenueDifference = ($this->monthlyRevenue - $previousRevenue);
        $this->percentChange = ($revenueDifference / ($previousRevenue == 0 ? 1 : $previousRevenue)) * 100;

        // Get revenue data for chart (last 6 months)
        $this->revenueData = RestaurantPayment::select(
            DB::raw('DATE_FORMAT(payment_date_time, "%Y-%m") as month'),
            DB::raw('SUM(amount) as total_revenue')
        )
            ->where('status', 'paid')
            ->where('payment_date_time', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
    }

    private function getTopRestaurants()
    {
        $this->topRestaurants = RestaurantPayment::select(
            'restaurants.name',
            DB::raw('SUM(restaurant_payments.amount) as total_payments'),
            DB::raw('COUNT(restaurant_payments.id) as payment_count')
        )
            ->join('restaurants', 'restaurant_payments.restaurant_id', '=', 'restaurants.id')
            ->where('restaurant_payments.status', 'paid')
            ->where('restaurant_payments.payment_date_time', '>=', now()->startOfMonth())
            ->groupBy('restaurants.id', 'restaurants.name')
            ->orderByDesc('total_payments')
            ->limit(5)
            ->get();
    }

    private function getSubscriptionStats()
    {
        $this->subscriptionStats = [
            'total_subscriptions' => GlobalSubscription::count(),
            'active_subscriptions' => GlobalSubscription::where('subscription_status', 'active')->count(),
            'expired_subscriptions' => GlobalSubscription::where('subscription_status', 'inactive')->count(),
            'trial_subscriptions' => 0, // No trial status in current system
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.superadmin-revenue-chart');
    }
}
