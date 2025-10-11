<?php

namespace App\Livewire\LandingSite;

use App\Models\FrontDetail;
use App\Models\FrontFeature;
use App\Models\LanguageSetting;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class FrontFeaturePageIcon extends Component
{
    use WithFileUploads, LivewireAlert, WithPagination;

    public $language;
    public $featureTitle;
    public $featureDescription;
    public $frontDetail;
    public $featureIconHeading;
    public $addFeatureModal = false;
    public $featureIdToDelete = null;
    public $editFeatureId = null;
    public $showEditFrontFeatureModal = false;
    public $confirmDeleteFrontFeature = false;
    public $activeTab = 'featuresIcon';
    public string $selectedIcon = '';
    public array $icons = [];
    public string $search = '';
    public bool $showDropdown = false;

    public function mount()
    {
        $allComponents = Blade::getClassComponentAliases();

        $this->icons = collect($allComponents)
            ->keys()
            ->filter(fn($name) => Str::startsWith($name, 'heroicon-o-'))
            ->map(fn($name) => Str::after($name, 'heroicon-o-'))
            ->sort()
            ->values()
            ->toArray();
    }

    public function selectIcon($icon)
    {
        $this->selectedIcon = $icon;

        $this->showDropdown = false;
    }

    public function saveFeature()
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
            'language' => 'required',
            'featureTitle' => 'required',
            'featureDescription' => 'required',
            'selectedIcon' => 'required',

        ]);

        FrontFeature::create([
            'language_setting_id' => $this->language,
            'title' => $this->featureTitle,
            'description' => $this->featureDescription,
            'type' => 'icon',
            'image' => $iconSvg,
            'icon' => $this->selectedIcon,
        ]);

        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        $this->reset(['featureTitle', 'featureDescription', 'language', 'selectedIcon']);
        $this->addFeatureModal = false;
        $this->dispatch('closeModal');
    }

    public function closeModal()
    {
        $this->addFeatureModal = false;
        $this->showEditFrontFeatureModal = false;

    }

    #[On('closeModal')]
    public function handleCloseModal()
    {
        $this->closeModal();
    }

    public function deleteFrontFeature($id)
    {
        $feature = FrontFeature::findOrFail($id);
        $feature->delete();
        $this->alert('success', __('messages.featureDeleted'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function editFeature($id)
    {
        $this->frontDetail = FrontFeature::findOrFail($id);
        $this->showEditFrontFeatureModal = true;
    }

    public function confirmDeleteFeature($id)
    {
        $this->featureIdToDelete = $id;
    }

    public function deleteFeature()
    {
        $feature = FrontFeature::find($this->featureIdToDelete);
        if ($feature) {
            $feature->delete();
            $this->alert('success', __('messages.featureDeleted'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        }
        $this->featureIdToDelete = null;
    }

    public function render()
    {
        $languageEnable = LanguageSetting::where('active', 1)->get();
        $currentLocale = auth()->user()?->locale;
        $languageSetting = LanguageSetting::where('language_code', $currentLocale)->where('active', 1)->first();
        $frontDetails = FrontFeature::where('type', 'icon');

        if ($languageSetting) {
            $frontDetails = $frontDetails->where('language_setting_id', $languageSetting->id);
        }

        $frontDetails = $frontDetails->paginate(10);
        return view('livewire.landing-site.front-feature-with-icon', [
            'languageEnable' => $languageEnable,
            'frontDetails' => $frontDetails
        ]);
    }

    public function updateFeatureIconHeading()
    {

        FrontDetail::updateOrCreate(
            [
            'feature_with_icon_heading' => $this->featureIconHeading,
            'header_title' => $this->featureIconHeading ? $this->featureIconHeading : 'Default Header Title'
            ]
        );

       $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
     }
}
