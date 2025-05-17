<?php

namespace Modules\ODOC\app\Http\Services;

use Modules\ODOC\app\Http\Traits\OdocApproversTrait;
use Modules\ODOC\app\Http\Traits\OdocDocumentTrait;
use Modules\ODOC\app\Models\Approvers;
use Modules\ODOC\app\Models\Document;

class DossierService
{
    use OdocApproversTrait, OdocDocumentTrait;

    public function __construct($docID, $statusID, $document)
    {
        $assignedApproversStatus = $this->AssignedApproversStatus()->id;
        if ($statusID == $assignedApproversStatus) {

            $notAssignedPersons = Approvers::where('document_id', $docID)->where('status_id', '!=', $assignedApproversStatus)->first();
            if (!$notAssignedPersons) {
                $statusApproved = $this->CompletedOdocDocumentStatus()->id;
                $document->update(['status_id', $statusApproved]);
            }
        } else {
            $statusDeclined = $this->DeclinedOdocDocumentStatus()->id;
            $document->update(['status_id', $statusDeclined]);
        }
    }
}
