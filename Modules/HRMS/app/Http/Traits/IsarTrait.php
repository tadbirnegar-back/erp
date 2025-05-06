<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\HRMS\app\Http\Enums\EducationalRecordStatusEnum;
use Modules\HRMS\app\Models\EducationalRecord;
use Modules\HRMS\app\Models\Isar;

trait IsarTrait
{
//write CRUD for isars table

    public function isarStore(array $data, ?int $personID)
    {
        $isar = new Isar();
        $isar->isar_status_id = $data['isarStatusID'] ?? null;
        $isar->relative_type_id = $data['relativeTypeID'] ?? null;
        $isar->length = $data['length'] ?? null;
        $isar->percentage = $data['percentage'] ?? null;
        $isar->person_id = $personID;
        $isar->status_id = $this->pendingApproveIsarStatus()->id;
        $isar->save();

        return $isar;
    }

    public function readIsar(int $id): ?Isar
    {
        return Isar::find($id);
    }

    public function isarUpdate(Isar $isar, array $data): ?Isar
    {
        $isar->isar_status_id = $data['isarStatusID'] ?? null;
        $isar->relative_type_id = $data['relativeTypeID'] ?? null;
        $isar->length = $data['length'] ?? null;
        $isar->percentage = $data['percentage'] ?? null;
        $isar->save();

        return $isar;
    }

    public function pendingApproveIsarStatus()
    {
        return Cache::rememberForever('isar_pending_approve_status', function () {
            return EducationalRecord::GetAllStatuses()->firstWhere('name', EducationalRecordStatusEnum::PENDING_APPROVE->value);
        });
    }

    public function approvedIsarStatus()
    {
        return Cache::rememberForever('isar_approved_status', function () {
            return EducationalRecord::GetAllStatuses()->firstWhere('name', EducationalRecordStatusEnum::APPROVED->value);
        });
    }
}
