<?php

namespace Modules\ODOC\app\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\ODOC\app\Http\Enums\ApproversStatusEnum;
use Modules\ODOC\app\Models\Approvers;

trait OdocApproversTrait
{
    private function setApprovers($documentID, $approvers)
    {
        $data = $this->prepareApproverData($documentID, $approvers);
        Approvers::insert($data);
    }

    private function prepareApproverData($documentID, $approvers)
    {
        $data = [];

        foreach ($approvers as $approver) {
            $data[] = [
                'person_id' => $approver['person_id'],
                'status_id' => $approver['status_id'],
                'signed_date' => $approver['signed_date'],
                'token' => $approver['token'],
                'signature_id' => $approver['signature_id'],
                'document_id' => $documentID,
            ];
        }

        return $data;
    }

    public function DeclinedApproversStatus()
    {
        return Cache::rememberForever('declined_odoc_document_status', function () {
            return Approvers::GetAllStatuses()
                ->firstWhere('name', ApproversStatusEnum::DECLINED->value);
        });
    }


    public function AssignedApproversStatus()
    {
        return Cache::rememberForever('active_odoc_document_status', function () {
            return Approvers::GetAllStatuses()
                ->firstWhere('name', ApproversStatusEnum::ASSIGNED->value);
        });
    }

    public function PendingApproversStatus()
    {
        return Cache::rememberForever('declined_odoc_document_status', function () {
            return Approvers::GetAllStatuses()
                ->firstWhere('name', ApproversStatusEnum::PENDING->value);
        });
    }
}
