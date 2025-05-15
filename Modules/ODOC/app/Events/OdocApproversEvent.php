<?php

namespace Modules\ODOC\app\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ODOC\app\Models\Approvers;

class OdocApproversEvent
{
    use SerializesModels, Dispatchable;

    public Approvers $approver;
    /**
     * Create a new event instance.
     */
    public function __construct(Approvers $approver)
    {
        $approvers = Approvers::where('document_id', $approver->document_id)->select('status_id')->get();
        $statuses = $approvers->pluck('status_id')->toArray();

    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
