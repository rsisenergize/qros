<?php

namespace Modules\Kitchen\Livewire\Forms;

use App\Models\MultipleKot;
use App\Models\Printer;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditKitchen extends Component
{
    use LivewireAlert;
    public $kitchen;
    public $kitchenName;
    public $description;
    public $printer_name;
    public $is_active = true;

    public function mount()
    {
        $this->kitchenName = $this->kitchen->name;
        $this->description = $this->kitchen->type;
        $this->printer_name = $this->kitchen->printer_id;
    }

    public function update()
    {
        $rules = [
            'description' => 'nullable|string',
            'printer_name' => 'required|exists:printers,id',
            'is_active' => 'boolean',
        ];

        if ($this->kitchenName !== $this->kitchen->name) {
            $rules['kitchenName'] = 'required|unique:kot_places,name';
        } else {
            $rules['kitchenName'] = 'required';
        }

        $this->validate($rules);

        $this->kitchen->update([
            'name' => $this->kitchenName,
            'type' => $this->description,
            'printer_id' => $this->printer_name,
            'is_active' => $this->is_active
        ]);

        $this->dispatch('hideEditKitchen');

        $this->alert('success', __('kitchen::messages.KitchenPlacesUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        $availablePrinters = Printer::where('branch_id', branch()->id)->get();
        return view('kitchen::livewire.forms.edit-kitchen', compact('availablePrinters'));
    }
}
