<?php

namespace Modules\EMS\app\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\EMS\app\Models\EnactmentMeeting;

class EnactmentMeetingEvent
{
    use Dispatchable, SerializesModels;

    public EnactmentMeeting $encMeeting;

    public function __construct(EnactmentMeeting $encMeeting)
    {
        $this->encMeeting = $encMeeting;
        // \Log::info($this->encMeeting);
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
