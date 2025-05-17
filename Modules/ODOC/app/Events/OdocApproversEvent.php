<?php

namespace Modules\ODOC\app\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\ODOC\app\Http\Services\DossierService;
use Modules\ODOC\app\Models\Approvers;
use Modules\ODOC\app\Models\Document;

class OdocApproversEvent
{
    use SerializesModels, Dispatchable;

    public Approvers $approver;
    /**
     * Create a new event instance.
     */
    public function __construct(Approvers $approver)
    {
        $documnet = Document::find($approver->document_id);
        if($documnet->model == BuildingDossier::class){
            new DossierService($approver->document_id, $approver->status_id , $documnet);
        }
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
