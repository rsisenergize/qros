<?php

namespace App\Livewire\Forms;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditKotCancelReason extends Component
{
    use LivewireAlert;
    public $kotCancelReason;
    public $reason;
    public $cancel_order = false;
    public $cancel_kot = false;

    public function mount()
    {
        $this->reason = $this->kotCancelReason->reason;
        $this->cancel_order = $this->kotCancelReason->cancel_order;
        $this->cancel_kot = $this->kotCancelReason->cancel_kot;
    }

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

        $this->kotCancelReason->reason = $this->reason;
        $this->kotCancelReason->cancel_order = $this->cancel_order;
        $this->kotCancelReason->cancel_kot = $this->cancel_kot;
        $this->kotCancelReason->save();

        $this->dispatch('hideEditKotReason');
         $this->alert('success', __('messages.reasonUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.forms.edit-kot-cancel-reason');
    }
}
