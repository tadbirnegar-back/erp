<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\MilitaryService;

trait MilitaryServiceTrait
{

    //write crud for military service here
    public function militaryServiceStore(array $data, ?int $workForceID)
    {
        $militaryService = new MilitaryService();
        $militaryService->military_service_status_id = $data['militaryServiceStatusID'] ?? null;
        $militaryService->exemption_type_id = $data['exemptionTypeID'] ?? null;
        $militaryService->issue_date = $data['issueDate'] ?? null;
        $militaryService->work_force_id = $workForceID ?? null;
        $militaryService->person_id = $data['personID'] ?? null;
        $militaryService->save();

        return $militaryService;
    }

    public function readMilitaryService(int $id): ?MilitaryService
    {
        return MilitaryService::find($id);
    }

    public function militaryServiceUpdate(MilitaryService $militaryService, array $data): MilitaryService
    {
        $militaryService->military_service_status_id = $data['militaryServiceStatusID'] ?? null;
        $militaryService->exemption_type_id = $data['exemptionTypeID'] ?? null;
        $militaryService->issue_date = $data['issueDate'] ?? null;
        $militaryService->save();

        return $militaryService;
    }
}
