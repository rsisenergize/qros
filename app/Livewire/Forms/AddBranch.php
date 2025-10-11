<?php

namespace App\Livewire\Forms;

use App\Models\Branch;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddBranch extends Component
{

    use LivewireAlert;

    public $branchName;
    public $branchAddress;
    public $branchLat;
    public $branchLng;

    private function checkBranchLimit(): bool
    {
        if (!in_array('Change Branch', restaurant_modules(), true)) {
            return false;
        }

        $restaurant = restaurant();
        $branchLimit = $restaurant->package->branch_limit;

        if (is_null($branchLimit) || $branchLimit === -1) {
            return true;
        }

        if ($branchLimit === 0 || $restaurant->branches()->count() >= $branchLimit) {
            return false;
        }

        return true;
    }

    public function submitForm()
    {
        if (!$this->checkBranchLimit()) {
            $this->alert('error', __('messages.invalidRequest'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
            return;
        }

        $this->validate([
            'branchName' => 'required|unique:branches,name,null,id,restaurant_id,' . restaurant()->id,
            'branchAddress' => 'required',
            'branchLat' => 'required|numeric|between:-90,90',
            'branchLng' => 'required|numeric|between:-180,180',
        ]);

        Branch::create([
            'name' => $this->branchName,
            'restaurant_id' => restaurant()->id,
            'address' => $this->branchAddress,
            'lat' => $this->branchLat,
            'lng' => $this->branchLng,
        ]);

        // Reset the value
        $this->branchName = '';
        $this->branchAddress = '';
        $this->branchLat = '';
        $this->branchLng = '';

        $this->dispatch('hideAddBranch');

        session(['branches' => Branch::get()]);

        $this->alert('success', __('messages.branchAdded'), [
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
        return view('livewire.forms.add-branch', [
            'mapApiKey' => global_setting()->google_map_api_key ?? null,
        ]);
    }

}
