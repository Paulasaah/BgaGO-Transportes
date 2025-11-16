<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewAlertCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $alert;

    /**
     * Create a new event instance.
     */
    public function __construct(array $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('dashboard');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'alert.created';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'alert' => $this->alert,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
