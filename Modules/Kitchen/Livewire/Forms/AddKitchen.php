<?php

namespace Modules\Kitchen\Livewire\Forms;

use App\Models\Printer;
use Livewire\Component;
use App\Models\KotPlace;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddKitchen extends Component
{

    use LivewireAlert;
    public $kitchenName;
    public $description;
    public $printer_name;
    public $branchID;

    public function mount()
    {
        $this->branchID = branch()->id;
        $this->printer_name = Printer::where('branch_id', branch()->id)->first()->id ?? null;
    }

    public function save()
    {
        $this->validate([
            'kitchenName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kot_places', 'name')->where(function ($query) {
                    return $query->where('branch_id', branch()->id);
                }),
            ],
            'description' => 'nullable|string',
            'printer_name' => 'required|exists:printers,id',
        ]);

        KotPlace::create([
            'name' => $this->kitchenName,
            'type' => $this->description,
            'printer_id' => $this->printer_name,
        ]);
        $this->dispatch('hideAddKitchen');

        $this->alert('success', __('kitchen::messages.kitchenPlacesAdded'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close'),
        ]);

        // Reset form fields
        $this->reset(['kitchenName', 'description']);
    }

    public function render()
    {
        $availablePrinters = Printer::where('branch_id', branch()->id)->get();
        return view('kitchen::livewire.forms.add-kitchen', compact('availablePrinters'));
    }
}
