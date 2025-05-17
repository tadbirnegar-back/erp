<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\HRMS\app\Http\Enums\EducationalRecordStatusEnum;
use Modules\HRMS\app\Http\Enums\IsarStatusEnum;
use Modules\HRMS\app\Models\EducationalRecord;
use Modules\HRMS\app\Models\Isar;

trait IsarTrait
{
//write CRUD for isars table

    public function isarStore(array $data, ?int $personID)
    {

        $isar = Isar::updateOrCreate([
            'person_id' => $personID,
        ], [
            'isar_status_id' => $data['isarStatusID'],
            'status_id' => $this->pendingApproveIsarStatus()->id
        ]);

        return $isar;
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
            return Isar::GetAllStatuses()->firstWhere('name', IsarStatusEnum::PENDING_APPROVE->value);
        });
    }

    public function approvedIsarStatus()
    {
        return Cache::rememberForever('isar_approved_status', function () {
            return Isar::GetAllStatuses()->firstWhere('name', IsarStatusEnum::APPROVED->value);
        });
    }
}
