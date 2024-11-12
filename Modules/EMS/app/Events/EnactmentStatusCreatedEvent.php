<?php

namespace Modules\EMS\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\EMS\app\Models\EnactmentStatus;

class EnactmentStatusCreatedEvent
{
    use Dispatchable, SerializesModels;

    public EnactmentStatus $encStatus;

    public function __construct(EnactmentStatus $encStatus)
    {
        $this->encStatus = $encStatus;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
