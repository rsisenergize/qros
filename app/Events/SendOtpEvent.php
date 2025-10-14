<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendOtpEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $otp;
    public $type;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, string $otp, string $type = 'login')
    {
        $this->user = $user;
        $this->otp = $otp;
        $this->type = $type;
    }
} 