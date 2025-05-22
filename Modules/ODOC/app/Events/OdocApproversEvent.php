<?php

namespace Modules\ODOC\app\Events;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\ODOC\app\Http\Enums\TypeOfModelsEnum;
use Modules\ODOC\app\Http\Enums\TypeOfOdocDocumentsEnum;
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

        $document = Document::find($approver->document_id);
        if($document->model == TypeOfModelsEnum::BuildingDossier->value){
            $dossiserOdocService = new DossierService($approver->document_id, $document);
            $dossiserOdocService->checkApprovers();
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
