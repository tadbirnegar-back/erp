<?php

namespace Modules\SMM\app\Traits;

use DB;
use Modules\HRMS\app\Http\Enums\RecruitmentScriptStatusEnum;
use Modules\HRMS\app\Models\Employee;

trait StaffTrait
{

    public function employeesListForContract(array $data, array $ounitIDs)
    {
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;

        $emps = Employee::joinRelationship('recruitmentScripts.scriptType', [
            'recruitmentScripts' => function ($join) use ($ounitIDs) {
                $join->finalStatus()
                    ->where('rss.name', RecruitmentScriptStatusEnum::ACTIVE->value)
                    ->whereIntegerInRaw('recruitment_scripts.organization_unit_id', $ounitIDs)
                    ->join('positions', 'recruitment_scripts.position_id', '=', 'positions.id')
                    ->join('organization_units', 'recruitment_scripts.organization_unit_id', '=', 'organization_units.id');
            }
        ])
            ->joinRelationship('workForce.person.natural', [
                'person' => function ($join) {
                    $join->leftJoin('files', function ($on) {
                        $on->on('persons.profile_picture_id', '=', 'files.id');
                    });
                }
            ])
            ->select(
                'organization_units.id as organization_unit_id',
                'organization_units.name as organization_unit_name',
                DB::raw('JSON_ARRAYAGG(JSON_OBJECT("employee_id", employees.id,
                 "display_name", persons.display_name,
                  "national_code", persons.national_code,
                  "gender",naturals.gender_id,
                  "person_id", persons.id,
                  "file_id", files.id,
                  "file_slug", files.slug,
                  "file_size", files.size,
                  "file_name", files.name,
                  "position_name", positions.name,
                  "script_name", script_types.title,
                  "rs_id",recruitmentScripts.id)) as
                 employees')
            )
            ->groupBy('organization_units.id', 'organization_units.name')
            ->paginate($perPage, page: $pageNum);

        return $emps;
    }
}
