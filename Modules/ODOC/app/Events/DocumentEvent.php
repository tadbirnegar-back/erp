<?php

namespace Modules\ODOC\app\Events;

use Illuminate\Queue\SerializesModels;
use Modules\ODOC\app\Http\Traits\OdocDocumentTrait;
use Modules\ODOC\app\Models\Approvers;
use Modules\ODOC\app\Models\Document;
use Modules\ODOC\app\Models\DocumentStatus;

class DocumentEvent
{
    use SerializesModels , OdocDocumentTrait;


    public $document;

    public function __construct(Document $document)
    {
        $approver = Approvers::where('document_id' , $document->id)->first();
        if(!$approver)
        {
            DocumentStatus::create([
                'odoc_document_id' => $document->id,
                'status_id' => $this->CompletedOdocDocumentStatus()->id,
                'created_date' => now(),
                'creator_id' => $document->creator_id,
                'description' => null
            ]);
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
