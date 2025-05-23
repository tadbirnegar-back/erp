<?php

namespace Modules\ODOC\app\Http\Services;

use Modules\BDM\app\Http\Traits\DossierTrait;
use Modules\ODOC\app\Http\Enums\OdocDocumentComponentsTypeEnum;
use Modules\ODOC\app\Http\Traits\OdocApproversTrait;
use Modules\ODOC\app\Http\Traits\OdocDocumentTrait;
use Modules\ODOC\app\Models\Approvers;
use Modules\ODOC\app\Models\Document;
use Modules\ODOC\app\Models\DocumentStatus;

class DossierService
{
    use OdocApproversTrait, OdocDocumentTrait, DossierTrait;

    public int $odocID;
    public Document $document;

    public function __construct($docID, $document)
    {
        $this->odocID = $docID;
        $this->document = $document;
    }

    public function checkApprovers()
    {
        $approvers = Approvers::where('document_id', $this->odocID)->get();
        if ($approvers->count() == 0) {
            $this->makeOdocApprove();
        } else {
            $this->checkPointOfApprovers();
        }

        switch ($this->document->component_to_render) {
            case OdocDocumentComponentsTypeEnum::BONYADE_MASKAN->value:
            case OdocDocumentComponentsTypeEnum::BUILDING_PLAN->value:
                $this->upgradeOneLevel($this->document->model_id);
                break;

            case OdocDocumentComponentsTypeEnum::MEMAR_OBLIGATION->value:
            case OdocDocumentComponentsTypeEnum::NAZER_OBLIGATION->value:
            case OdocDocumentComponentsTypeEnum::MOHASEB_OBLIGATION->value:
            case OdocDocumentComponentsTypeEnum::StartWorkingObligation->value:
                $isAllApproved = $this->checkAllTheObligationsAreApproved();
                if ($isAllApproved) {
                    $this->makeOdocApprove();
                    $this->upgradeOneLevel($this->document->model_id);
                } else {
                    $this->makeOdocApprove();
                }
                break;
        }


    }

    public function checkAllTheObligationsAreApproved()
    {
        $isAllApproved = true;
        $dossierID = $this->document->model_id;
        $pendingApproversStatus = $this->PendingApproversStatus()->id;
        $declinedApproversStatus = $this->DeclinedApproversStatus()->id;
        $documents = Document::where('model_id', $dossierID)
            ->whereIn('component_to_render', [OdocDocumentComponentsTypeEnum::MEMAR_OBLIGATION->value, OdocDocumentComponentsTypeEnum::NAZER_OBLIGATION->value, OdocDocumentComponentsTypeEnum::MOHASEB_OBLIGATION->value, OdocDocumentComponentsTypeEnum::StartWorkingObligation->value])
            ->get();
        $documents->map(function ($item) use ($pendingApproversStatus, $declinedApproversStatus, &$isAllApproved) {
            $approvers = Approvers::where('document_id', $item->id)->whereIn('status_id', [$pendingApproversStatus, $declinedApproversStatus])->first();
            if ($approvers) {
                $isAllApproved = false;
            }
        });

        return $isAllApproved;
    }

    public function makeOdocApprove()
    {
        $statusApproved = $this->CompletedOdocDocumentStatus()->id;
        DocumentStatus::create([
            'odoc_document_id' => $this->odocID,
            'status_id' => $statusApproved,
            'created_date' => now(),
            'creator_id' => $this->document->creator_id,
        ]);
    }

    public function makeOdocDecline()
    {
        $statusDeclined = $this->DeclinedOdocDocumentStatus()->id;
        DocumentStatus::create([
            'document_id' => $this->odocID,
            'status_id' => $statusDeclined,
            'created_date' => now(),
            'creator_id' => $this->document->creator_id,
        ]);
    }

    public function checkPointOfApprovers()
    {
        $pendingApproversStatus = $this->PendingApproversStatus()->id;
        $declinedApproversStatus = $this->DeclinedApproversStatus()->id;

        $PendingApprovers = Approvers::where('document_id', $this->odocID)->where('status_id', $pendingApproversStatus)->get();
        if ($PendingApprovers->count() == 0) {
            $declinedApprovers = Approvers::where('document_id', $this->odocID)->where('status_id', $declinedApproversStatus)->get();
            if ($declinedApprovers->count() == 0) {
                $this->makeOdocApprove();
            } else {
                $this->makeOdocDecline();
            }
        }

    }
}
