<?php

namespace App\Livewire\Settings;

use App\Models\PusherSetting;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class PushNotificationSettings extends Component
{

    use LivewireAlert;

    public $beamerStatus = false;
    public $pusherBroadcastStatus = false;
    public $instanceID;
    public $beamSecret;
    public $pusher_app_id;
    public $pusher_key;
    public $pusher_secret;
    public $pusher_cluster;
    public $pusherSettings;

    public function mount()
    {
        $this->pusherSettings = PusherSetting::first();
        $this->beamerStatus = (bool)$this->pusherSettings->beamer_status;
        $this->pusherBroadcastStatus = (bool)($this->pusherSettings->pusher_broadcast ?? false);
        $this->instanceID = $this->pusherSettings->instance_id;
        $this->beamSecret = $this->pusherSettings->beam_secret;
        $this->pusher_app_id = $this->pusherSettings->pusher_app_id;
        $this->pusher_key = $this->pusherSettings->pusher_key;
        $this->pusher_secret = $this->pusherSettings->pusher_secret;
        $this->pusher_cluster = $this->pusherSettings->pusher_cluster;
    }

    public function submitForm()
    {
        $this->validate([
            'instanceID' => 'required_if:beamerStatus,true',
            'beamSecret' => 'required_if:beamerStatus,true',
            'pusher_app_id' => 'required_if:pusherBroadcastStatus,true',
            'pusher_key' => 'required_if:pusherBroadcastStatus,true',
            'pusher_secret' => 'required_if:pusherBroadcastStatus,true',
            'pusher_cluster' => 'required_if:pusherBroadcastStatus,true',
        ]);

        $this->pusherSettings->update([
            'beamer_status' => $this->beamerStatus,
            'pusher_broadcast' => $this->pusherBroadcastStatus,
            'instance_id' => $this->instanceID,
            'beam_secret' => $this->beamSecret,
            'pusher_app_id' => $this->pusher_app_id,
            'pusher_key' => $this->pusher_key,
            'pusher_secret' => $this->pusher_secret,
            'pusher_cluster' => $this->pusher_cluster,
        ]);

        $this->pusherSettings->fresh();

        cache()->forget('pusherSettings');

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }
    public function render()
    {
        return view('livewire.settings.push-notification-settings');
    }
}
