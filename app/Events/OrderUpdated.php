<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $action;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $action = 'updated')
    {
        $this->order = $order;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('orders'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'action' => $this->action,
            'status' => $this->order->status,
            'order_type' => $this->order->order_type,
            'order_type_id' => $this->order->order_type_id,
            'waiter_id' => $this->order->waiter_id,
            'table_id' => $this->order->table_id,
            'customer_id' => $this->order->customer_id,
            'updated_at' => $this->order->updated_at,
            'timestamp' => now()->toISOString(),
            'test_message' => 'This is a test broadcast from Laravel',
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.updated';
    }
}
