<?php

namespace Modules\EMS\app\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\EMS\app\Models\Meeting;

class CreateMeetingEvent
{
    use SerializesModels, Dispatchable;

    public Meeting $meeting;

    /**
     * Create a new event instance.
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
