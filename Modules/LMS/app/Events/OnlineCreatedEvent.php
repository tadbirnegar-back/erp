<?php

namespace Modules\LMS\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\PayStream\app\Models\Online;

class OnlineCreatedEvent
{
    use Dispatchable, SerializesModels;

    public Online $online;

    /**
     * Create a new event instance.
     */
    public function _construct(Online $online)
    {
        $this->online = $online;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
