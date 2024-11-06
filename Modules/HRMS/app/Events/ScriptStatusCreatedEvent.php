<?php

namespace Modules\HRMS\app\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HRMS\app\Models\recruitmentScriptStatus;

class ScriptStatusCreatedEvent
{
    use Dispatchable, SerializesModels;

    public recruitmentScriptStatus $recStatus;

    public function __construct(recruitmentScriptStatus $recStatus)
    {
        $this->recStatus = $recStatus;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
