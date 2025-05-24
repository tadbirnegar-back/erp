<?php

namespace Modules\ODOC\app\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\AAA\app\Http\Traits\OtpTrait;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\ODOC\app\Http\Enums\DocumentStatusEnum;
use Modules\ODOC\app\Http\Enums\OdocDocumentComponentsTypeEnum;
use Modules\ODOC\app\Http\Enums\TypeOfOdocDocumentsEnum;
use Modules\ODOC\app\Models\Approvers;
use Modules\ODOC\app\Models\Document;
use Modules\ODOC\app\Models\DocumentStatus;
use Modules\PersonMS\app\Http\Traits\SignaturesTrait;
use Modules\PersonMS\app\Models\Person;

trait OdocDocumentTrait
{
    use OdocApproversTrait, OtpTrait, SignaturesTrait, OdocApproversTrait;

    public function storeOdocDocument($data, $userID)
    {
        $json = json_decode($data['json']);

        foreach ($json as $object) {
            $data['component_to_render'] = $object->component_to_render;
            $data['json'] = encrypt_json([$object]);
            $data['creator_id'] = $userID;
            $data['title'] = OdocDocumentComponentsTypeEnum::getName($data['component_to_render']);
            $doc = Document::where('model', $data['model'])
                ->where('model_id', $data['model_id'])
                ->where('component_to_render', $data['component_to_render'])
                ->where('version', $data['version'])->first();

            if ($doc) {
                $doc->update([
                    'data' => $data['json'],
                    'component_to_render' => $data['component_to_render'],
                    'model' => $data['model'],
                    'model_id' => $data['model_id'],
                    'serial_number' => $data['serial_number'],
                    'title' => $data['title'],
                    'created_date' => now(),
                    'creator_id' => $userID,
                    'version' => $data['version'],
                    'ounit_id' => $data['ounit_id'],
                ]);
            } else {
                $doc = Document::create([
                    'data' => $data['json'],
                    'component_to_render' => $data['component_to_render'],
                    'model' => $data['model'],
                    'model_id' => $data['model_id'],
                    'serial_number' => $data['serial_number'],
                    'title' => $data['title'],
                    'created_date' => now(),
                    'creator_id' => $userID,
                    'version' => $data['version'],
                    'ounit_id' => $data['ounit_id'],
                ]);
            }
            $this->setBdmOdocApprovers($doc->component_to_render, $doc->id);
            $this->setPendingApprovalForDocument($doc->id , $userID);
        }
    }

    public function setPendingApprovalForDocument($documentID , $userID)
    {
        DocumentStatus::create([
            'odoc_document_id' => $documentID,
            'status_id' => $this->PendingApproversStatus()->id,
            'created_date' => now(),
            'creator_id' => $userID,
            'description' => null
        ]);
    }

    public function fetchAllRelatedDocuments($data, $user, $perPage, $pageNum)
    {
        $query = Document::join('odoc_approvers', 'odoc_approvers.document_id', '=', 'odoc_documents.id')
            ->join('organization_units', 'organization_units.id', '=', 'odoc_documents.ounit_id')
            ->select([
                'odoc_documents.id as document_id',
                'odoc_documents.title as title',
                'odoc_documents.serial_number as serial_number',
                'organization_units.name as ounit_name',
                'odoc_documents.model as model',
                'odoc_documents.created_date as created_date',
            ])
            ->where('odoc_approvers.person_id', $user->person_id)
            ->where('odoc_approvers.status_id', $this->PendingApproversStatus()->id)
            ->when(isset($data['title']), function ($query) use ($data) {
                $query->where('odoc_documents.title', 'like', '%' . $data['title'] . '%');
            })
            ->distinct()
            ->paginate($perPage, ['*'], 'page', $pageNum);
        return $query;

    }

    public function CompletedOdocDocumentStatus()
    {
        return Cache::rememberForever('active_odoc_document_status', function () {
            return Document::GetAllStatuses()
                ->firstWhere('name', DocumentStatusEnum::COMPLETED->value);
        });
    }

    public function PendingOdocDocumentStatus()
    {
        return Cache::rememberForever('pending_odoc_document_status', function () {
            return Document::GetAllStatuses()
                ->firstWhere('name', DocumentStatusEnum::PENDING->value);
        });
    }

    public function DeclinedOdocDocumentStatus()
    {
        return Cache::rememberForever('declined_odoc_document_status', function () {
            return Document::GetAllStatuses()
                ->firstWhere('name', DocumentStatusEnum::DECLINED->value);
        });
    }

}
