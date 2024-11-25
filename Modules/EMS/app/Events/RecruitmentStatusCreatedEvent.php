<?php

namespace Modules\EMS\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HRMS\app\Models\RecruitmentScriptStatus;

class RecruitmentStatusCreatedEvent
{
    use SerializesModels, Dispatchable;

    /**
     * Create a new event instance.
     */
    public RecruitmentScriptStatus $recStatus;

    public function __construct(RecruitmentScriptStatus $recStatus)
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
