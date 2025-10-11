<?php

namespace App\Livewire\LandingSite;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;

class EditFrontWithIconFeature extends Component
{

    use WithFileUploads, LivewireAlert, WithPagination;

    public $languageSettingid;
    public $featureTitle;
    public $featureDescription;
    public $frontDetail;
    public $languageEnable;
    public $selectedIcon;
    public $icons = [];
    public $search = '';

    public function mount()
    {
        $this->languageSettingid = $this->frontDetail->language_setting_id;
        $this->featureTitle = $this->frontDetail->title;
        $this->featureDescription = $this->frontDetail->description;
        $this->selectedIcon = $this->frontDetail->icon; // Pre-fill the selected icon
        $this->icons = collect(Blade::getClassComponentAliases())
            ->keys()
            ->filter(fn($name) => Str::startsWith($name, 'heroicon-o-'))
            ->map(fn($name) => Str::after($name, 'heroicon-o-'))
            ->sort()
            ->values()
            ->toArray();
    }

    public function updatedFeatureDescription()
    {
        $this->dispatch('description-updated', $this->featureDescription);
    }

    public function selectIcon($icon)
    {
        $this->selectedIcon = $icon;
    }

    public function editFrontFeature()
    {
        $svgPath = base_path("vendor/blade-ui-kit/blade-heroicons/resources/svg/c-{$this->selectedIcon}.svg");

        $iconSize = 30;

        if (File::exists($svgPath)) {
            $iconSvg = File::get($svgPath);
            $iconSvg = preg_replace('/(width|height)="\d+(\.\d+)?"/', '', $iconSvg); // Remove existing
            $iconSvg = preg_replace(
            '/<svg\b/',
            "<svg width=\"{$iconSize}\" height=\"{$iconSize}\" class=\"svg icon bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6\"",
            $iconSvg,
            1
            );
        } else {
            $iconSvg = null;
        }

        $this->validate([
            'languageSettingid' => 'required',
            'featureTitle' => 'required',
            'featureDescription' => 'required',
            'selectedIcon' => 'required',
        ]);

        $this->frontDetail->update([
            'language_setting_id' => $this->languageSettingid,
            'title' => $this->featureTitle,
            'description' => $this->featureDescription,
            'image' => $iconSvg,
            'icon' => $this->selectedIcon,
        ]);

         $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.landing-site.edit-front-with-icon-feature');
    }
}
