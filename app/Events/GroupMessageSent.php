<?php

namespace App\Events;

use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel('group.' . $this->payload['group_id']);
    }

    public function broadcastAs(): string
    {
        return 'group.message';
    }

    public function broadcastWith(): array
    {
        return [
            'payload' => $this->payload,
        ];
    }
}