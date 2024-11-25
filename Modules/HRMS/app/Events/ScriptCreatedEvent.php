<?php

namespace Modules\HRMS\app\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScriptCreatedEvent
{
    use SerializesModels, Dispatchable;


    /**
     * Create a new event instance.
     */
    public object $rs; // or the appropriate type, e.g., RecruitmentScript

    public function __construct($recruitmentScript)
    {
        $this->rs = $recruitmentScript; // Ensure it's initialized properly


    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
