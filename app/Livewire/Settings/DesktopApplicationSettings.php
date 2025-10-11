<?php

namespace App\Livewire\Settings;

use App\Models\DesktopApplication;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class DesktopApplicationSettings extends Component
{
    use LivewireAlert;
    public $windows_file_path = '';
    public $mac_file_path = '';
    public $linux_file_path = '';
    public $desktopApp;

    protected $rules = [
        'windows_file_path' => 'nullable|string|url',
        'mac_file_path' => 'nullable|string|url',
        'linux_file_path' => 'nullable|string|url',
    ];

    public function mount()
    {
        $this->desktopApp = DesktopApplication::first();
        $this->loadExistingData();
    }

    public function loadExistingData()
    {
        $this->desktopApp = DesktopApplication::first();

        if ($this->desktopApp) {
            $this->windows_file_path = $this->desktopApp->windows_file_path ?? DesktopApplication::WINDOWS_FILE_PATH;
            $this->mac_file_path = $this->desktopApp->mac_file_path ?? DesktopApplication::MAC_FILE_PATH;
            $this->linux_file_path = $this->desktopApp->linux_file_path ?? DesktopApplication::LINUX_FILE_PATH;
        }
    }


    public function saveAll()
    {
        $this->validate();
        $desktopApp = DesktopApplication::first();

        if (!$desktopApp) {
            $desktopApp = DesktopApplication::create([]);
        }

        $desktopApp->windows_file_path = $this->windows_file_path;
        $desktopApp->linux_file_path = $this->linux_file_path;
        $desktopApp->mac_file_path = $this->mac_file_path;

        $desktopApp->save();


        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
        $this->loadExistingData();
    }


    public function resetWindowsUrl()
    {
        $this->windows_file_path = DesktopApplication::WINDOWS_FILE_PATH;
    }

    public function resetMacUrl()
    {
        $this->mac_file_path = DesktopApplication::MAC_FILE_PATH;
    }

    public function resetLinuxUrl()
    {
        $this->linux_file_path = DesktopApplication::LINUX_FILE_PATH;
    }

    public function render()
    {
        $desktopApplication = DesktopApplication::first();
        return view('livewire.settings.desktop-application-settings', compact('desktopApplication'));
    }
}
