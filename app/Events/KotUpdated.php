<?php

namespace App\Events;

use App\Models\Kot;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class KotUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $kot;

    public function __construct(Kot $kot)
    {
        $this->kot = $kot;
    }

    public function broadcastOn()
    {
        return new Channel('kots');
    }

    public function broadcastAs()
    {
        return 'kot.updated';
    }

    public function broadcastWith()
    {
        return [
            'kot_id' => $this->kot->id,
        ];
    }
}
