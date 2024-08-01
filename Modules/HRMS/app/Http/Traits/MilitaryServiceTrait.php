<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\MilitaryService;

trait MilitaryServiceTrait
{

    //write crud for military service here
    public function militaryServiceStore(array $data, int $workForceID)
    {
        $militaryService = new MilitaryService();
        $militaryService->military_service_type_id = $data['militaryServiceTypeID']??null;
        $militaryService->exemption_type_id = $data['exemptionTypeID']??null;
        $militaryService->start_date = $data['startDate']??null;
        $militaryService->end_date = $data['endDate']??null;
        $militaryService->work_force_id = $workForceID;
        $militaryService->save();

        return $militaryService;
    }

    public function readMilitaryService(int $id): ?MilitaryService
    {
        return MilitaryService::find($id);
    }

    public function militaryServiceUpdate(MilitaryService $militaryService, array $data): ?MilitaryService
    {
        $militaryService->military_service_type_id = $data['militaryServiceTypeID']??null;
        $militaryService->exemption_type_id = $data['exemptionTypeID']??null;
        $militaryService->start_date = $data['startDate']??null;
        $militaryService->end_date = $data['endDate']??null;
        $militaryService->work_force_id = $data['workForceID'];
        $militaryService->save();

        return $militaryService;
    }
}
