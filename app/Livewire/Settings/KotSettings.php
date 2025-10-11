<?php

namespace App\Livewire\Settings;

use App\Models\KotSetting;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class KotSettings extends Component
{
    use LivewireAlert;

    public $kotSettings;
    public $enableItemLevelStatus;
    public $defaultKotStatus;

    public function mount()
    {
        $this->kotSettings = KotSetting::first();
        $this->enableItemLevelStatus = (bool) $this->kotSettings->enable_item_level_status;
        
        $this->defaultKotStatus = $this->kotSettings->default_status;
    }

    public function submitForm()
    {
        $this->kotSettings->update([
            'enable_item_level_status' => $this->enableItemLevelStatus,
            'default_status' => $this->defaultKotStatus
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
        return view('livewire.settings.kot-settings');
    }
}
