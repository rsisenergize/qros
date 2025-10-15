<?php

namespace App\Livewire\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class RestaurantDetail extends Component
{

    use LivewireAlert;

    public $restaurant;
    public $restaurantAdmin;
    public $showPasswordModal = false;
    public $password;
    public $hash;
    public $search;

    public $packageSmsCount;
    public $usedSmsCount;
    public $remainingSmsCount;
    public $isSmsLimitReached;
    public $showSmsTopupModal = false;
    public $smsTopupAmount;
    public $topupNote;

    public function mount()
    {
        $this->restaurant = Restaurant::with([
            'currency',
            'restaurantPayment',
            'restaurantPayment.package',
            'restaurantPayment.package.currency',
            'package',
            'country',
            'branches' => function ($query) {
                $query->withCount('orders');
            },
            'users' => function ($query) {
                $query->orderBy('id')->limit(1);
            }
        ])->where('hash', $this->hash)->firstOrFail();

        $this->restaurantAdmin = $this->restaurant->users->first();

        // Initialize SMS count info
        if (module_enabled('Sms')) {
            $this->getSmsCountInfo();
        }
    }

    public function getSmsCountInfo()
    {
        if ($this->restaurant && in_array('Sms', restaurant_modules())) {
            // Use restaurant's total_sms instead of package sms_count
            $this->packageSmsCount = $this->restaurant->total_sms ?? 0;
            $this->usedSmsCount = $this->restaurant->count_sms ?? 0;

            if ($this->packageSmsCount == -1) {
                $this->remainingSmsCount = -1;
                $this->isSmsLimitReached = false;
            } else {
                $this->remainingSmsCount = max(0, $this->packageSmsCount - $this->usedSmsCount);
                $this->isSmsLimitReached = $this->usedSmsCount >= $this->packageSmsCount;
            }
        } else {
            $this->packageSmsCount = 0;
            $this->usedSmsCount = 0;
            $this->remainingSmsCount = 0;
            $this->isSmsLimitReached = false;
        }
    }

    public function impersonate($restaurantId)
    {
        $admin = User::where('restaurant_id', $restaurantId)->first();

        if (!$admin) {
            $this->alert('error', 'No admin found this restaurant', [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);

            return true;
        }

        $user = user();
        session()->flush();
        session()->forget('user');

        Auth::logout();
        session(['impersonate_user_id' => $user->id]);
        session(['impersonate_restaurant_id' => $restaurantId]);
        session(['user' => $admin]);

        Auth::loginUsingId($admin->id);

        session(['user' => auth()->user()]);

        return $this->redirect(route('dashboard'));
    }

    public function submitForm()
    {
        $this->validate([
            'password' => 'required'
        ]);

        $this->restaurantAdmin->password = $this->password;
        $this->restaurantAdmin->save();

        $this->alert('success', __('messages.profileUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        $this->password = null;
        $this->showPasswordModal = false;
    }

    public function addSmsTopup()
    {
        if (!in_array('Sms', restaurant_modules())) {
            return;
        }

        $this->validate([
            'smsTopupAmount' => 'required|integer|min:1',
        ]);

        $this->restaurant->total_sms = ($this->restaurant->total_sms == -1) ? -1 : ($this->restaurant->total_sms ?? 0) + $this->smsTopupAmount;

        $this->restaurant->save();

        $this->getSmsCountInfo();

        $this->alert('success', __('sms::modules.messages.smsTopupSuccess'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
        ]);

        $this->smsTopupAmount = null;
        $this->showSmsTopupModal = false;
    }

    public function render()
    {
        return view('livewire.restaurant.restaurant-detail');
    }
}
