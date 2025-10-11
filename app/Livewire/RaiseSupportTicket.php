<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class RaiseSupportTicket extends Component
{
    public $showRaiseSupportTicketModal = false;

    #[On('showRaiseSupportTicket')]
    public function showRaiseSupportTicket()
    {
        $this->showRaiseSupportTicketModal = true;
    }
   
    public function render()
    {
        return view('livewire.raise-support-ticket');
    }
}
