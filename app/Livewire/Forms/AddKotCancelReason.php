<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\KotCancelReason;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddKotCancelReason extends Component
{
    use LivewireAlert;
    public $reason;
    public $cancel_order = false;
    public $cancel_kot = false;

     public function submitForm()
    {
        $this->validate([
            'reason' => 'required',
            'cancel_order' => 'boolean',
            'cancel_kot' => 'boolean',
        ]);

        // Ensure at least one type is selected
        if (!$this->cancel_order && !$this->cancel_kot) {
            $this->addError('cancel_order', 'Please select at least one cancellation type.');
            return;
        }

        $kotReason = new KotCancelReason();
        $kotReason->reason = $this->reason;
        $kotReason->cancel_order = $this->cancel_order;
        $kotReason->cancel_kot = $this->cancel_kot;
        $kotReason->save();

        $this->dispatch('hideAddKotReason');
         $this->alert('success', __('messages.reasonAdded'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);

        // Reset form
        $this->reason = '';
        $this->cancel_order = false;
        $this->cancel_kot = false;
    }

    public function render()
    {
        return view('livewire.forms.add-kot-cancel-reason');
    }
}
