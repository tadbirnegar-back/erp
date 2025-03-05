<?php

namespace Modules\EVAL\app\Events;

use Illuminate\Queue\SerializesModels;
use Modules\EVAL\app\Models\EvalCircular;

class CircularExpirationEvent
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(EvalCircular $circular)
    {
        $this -> circular  = $circular;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
