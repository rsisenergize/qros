<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\KotCancelReason;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class CancellationSettings extends Component
{
    use LivewireAlert;
    public $reasons;
    public $showAddCancelReasonModal = false;
    public $showEditCancelReasonModal = false;
    public $showDeleteModal = false;
    public $reasonToEdit = null;
    public $reasonToDelete = null;

    public function mount()
    {
        $this->loadReasons();
    }

    public function loadReasons()
    {
        $this->reasons = KotCancelReason::latest()->get();
    }

    public function showAddKotReason()
    {
        $this->showAddCancelReasonModal = true;
    }

    public function editReason($reasonId)
    {
        $this->reasonToEdit = KotCancelReason::find($reasonId);
        $this->showEditCancelReasonModal = true;
    }

    public function confirmDelete($reasonId)
    {
        $this->reasonToDelete = $reasonId;
        $this->showDeleteModal = true;
    }

    public function deleteReason()
    {

            $reason = KotCancelReason::find($this->reasonToDelete);
            if ($reason) {
                $reason->delete();
                $this->loadReasons();
                $this->alert('success', __('messages.reasonDeleted'), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
        ]);


        $this->showDeleteModal = false;
        $this->reasonToDelete = null;
    }
}

    public function closeModal()
    {
        $this->showAddCancelReasonModal = false;
        $this->showEditCancelReasonModal = false;
        $this->reasonToEdit = null;
    }

    #[On('hideAddKotReason')]
    public function hideAddKotReason()
    {
        $this->showAddCancelReasonModal = false;
        $this->loadReasons(); // Refresh the list after adding
    }

    #[On('hideEditKotReason')]
    public function hideEditKotReason()
    {
        $this->showEditCancelReasonModal = false;
        $this->reasonToEdit = null;
        $this->loadReasons(); // Refresh the list after editing
    }

    public function render()
    {
        return view('livewire.settings.cancellation-settings');
    }
}
