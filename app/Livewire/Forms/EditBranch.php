<?php

namespace App\Livewire\Forms;

use App\Models\Branch;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditBranch extends Component
{

    use LivewireAlert;

    public $branchName;
    public $branchAddress;
    public $branch;
    public $branchLat;
    public $branchLng;
    public $mapDivId;

    public function mount()
    {
        $this->dispatch('google-map-loaded');

        $this->branchName = $this->branch->name;
        $this->branchAddress = $this->branch->address;
        $this->branchLat = $this->branch->lat;
        $this->branchLng = $this->branch->lng;
        $this->mapDivId = 'edit-address-map-' . $this->branch->id;
    }

    public function submitForm()
    {
        $this->validate([
            'branchName' => 'required|unique:branches,name,'.$this->branch->id.',id,restaurant_id,' . restaurant()->id,
            'branchAddress' => 'required',
            'branchLat' => 'required|numeric|between:-90,90',
            'branchLng' => 'required|numeric|between:-180,180'
        ]);

        Branch::where('id', $this->branch->id)->update([
            'name' => $this->branchName,
            'restaurant_id' => restaurant()->id,
            'address' => $this->branchAddress,
            'lat' => $this->branchLat,
            'lng' => $this->branchLng,
        ]);

        $this->dispatch('hideEditBranch');

        session(['branches' => Branch::get()]);

        $this->alert('success', __('messages.branchUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    #[On('updateLivewireMapProperties')]
    public function updateLivewireMapProperties($lat, $lng)
    {
        $this->branchLat = $lat;
        $this->branchLng = $lng;
    }

    public function render()
    {

        return view('livewire.forms.edit-branch', [
            'mapApiKey' => global_setting()->google_map_api_key ?? null,
        ]);
    }

}
