<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ReservationGeneralSettings extends Component
{

    use LivewireAlert;

    public $enable_admin_reservation = true;
    public $enable_customer_reservation = true;
    public $minimum_party_size = 1;
    public $disableSlotMinutes = 0;
    public function mount()
    {
        $restaurant = restaurant();
        $this->enable_admin_reservation = $restaurant->enable_admin_reservation ?? true;
        $this->enable_customer_reservation = $restaurant->enable_customer_reservation ?? true;
        $this->minimum_party_size = $restaurant->minimum_party_size ?? 1;
        $this->disableSlotMinutes = $restaurant->disable_slot_minutes ;
    }

    public function saveSettings()
    {
        $this->validate([
            'minimum_party_size' => 'required|integer|min:1|max:50',
            'disableSlotMinutes' => 'required|integer|min:0|max:1440',
        ]);

        $restaurant = restaurant();
        $restaurant->update([
            'enable_admin_reservation' => $this->enable_admin_reservation,
            'enable_customer_reservation' => $this->enable_customer_reservation,
            'minimum_party_size' => $this->minimum_party_size,
            'disable_slot_minutes' => $this->disableSlotMinutes,
        ]);

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.settings.reservation-general-settings');
    }

}
