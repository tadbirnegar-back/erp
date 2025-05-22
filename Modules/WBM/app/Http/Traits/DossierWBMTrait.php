<?php

namespace Modules\WBM\app\Http\Traits;


use Modules\BDM\app\Http\Enums\BdmReportTypesEnum;
use Modules\BDM\app\Http\Enums\EngineersTypeEnum;
use Modules\BDM\app\Http\Enums\PermitStatusesEnum;
use Modules\BDM\app\Http\Traits\DossierTrait;
use Modules\BDM\app\Http\Traits\PermitTrait;
use Modules\BDM\app\Models\Engineer;
use Modules\BDM\app\Models\ReportItem;
use Modules\BDM\app\Models\ReportType;
use Modules\HRMS\app\Http\Enums\ScriptTypesEnum;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\ScriptType;
use Modules\PersonMS\app\Models\Natural;

trait DossierWBMTrait
{
    use RecruitmentScriptTrait , PermitTrait , DossierTrait;
    public function TasksOfEngineers($pageNum, $perPage , $personID)
    {
        $scriptType = ScriptType::where('title', ScriptTypesEnum::MASOULE_FAANI->value)->first();
        $activeScriptStatus = $this->activeRsStatus();
        $PermitStatuses = [$this->fifteenthStatus()->id , $this->sixteenthStatus()->id , $this->seventeenthStatus()->id , $this->eighteenthStatus()->id];


        $engineers = Engineer::join('bdm_engineers_building' , function ($join) {
            $join->on('bdm_engineers_building.engineer_id' , '=' , 'bdm_engineers.id')
                ->where('bdm_engineers_building.engineer_type_id' , '=' , EngineersTypeEnum::NAZER->id());
        })
            ->join('bdm_building_dossiers' , 'bdm_building_dossiers.id' , '=' , 'bdm_engineers_building.dossier_id')
            ->join('bdm_owners' , function ($join) {
                $join->on('bdm_owners.dossier_id' , '=' , 'bdm_building_dossiers.id')
                    ->where('is_main_owner' , '=' , true);
            })
            ->join('persons' , 'bdm_owners.person_id' , '=' , 'persons.id')
            ->join('naturals' , function ($join) {
                $join->on('persons.personable_id' , '=' , 'naturals.id')
                    ->where('persons.personable_type' , '=' , Natural::class);
            })
            ->join('users' , 'bdm_owners.person_id' , '=' , 'users.person_id')
            ->join('bdm_estates' , 'bdm_building_dossiers.id' , '=' , 'bdm_estates.dossier_id')
            ->join('bdm_estate_app_sets' , 'bdm_estates.id' , '=' , 'bdm_estate_app_sets.estate_id')
            ->join('pfm_prop_applications' , 'bdm_estate_app_sets.app_id' , '=' , 'pfm_prop_applications.id')
            ->join('bdm_building_permit_status', function ($join) {
                $join->on('bdm_building_dossiers.id', '=', 'bdm_building_permit_status.dossier_id')
                    ->whereRaw('bdm_building_permit_status.id = (SELECT MAX(id) FROM bdm_building_permit_status WHERE dossier_id = bdm_building_dossiers.id)');
            })
            ->join('statuses as status_permit', 'bdm_building_permit_status.status_id', '=', 'status_permit.id')
            ->join('recruitment_scripts' , function ($join) use ($scriptType) {
                $join->on('bdm_estates.ounit_id' , '=' , 'recruitment_scripts.organization_unit_id')
                    ->where('recruitment_scripts.script_type_id' , '=' , $scriptType->id);
            })
            ->join('recruitment_script_status', function ($join) use ($activeScriptStatus) {
                $join->on('recruitment_scripts.id', '=', 'recruitment_script_status.recruitment_script_id')
                    ->whereRaw('recruitment_script_status.id = (SELECT MAX(id) FROM recruitment_script_status WHERE recruitment_script_id = recruitment_scripts.id)')
                    ->where('recruitment_script_status.status_id' , '=' , $activeScriptStatus->id);
            })
            ->join('work_forces' , function ($join) {
                $join->on('recruitment_scripts.employee_id' , '=' , 'work_forces.workforceable_id')
                    ->where('work_forces.workforceable_type' , '=' , Employee::class);
            })
            ->join('persons as masuleFani' , 'work_forces.person_id' , '=' , 'masuleFani.id')
            ->join('naturals as masuleFani_naturals' , function ($join) {
                $join->on('masuleFani.personable_id' , '=' , 'masuleFani_naturals.id')
                    ->where('masuleFani.personable_type' , '=' , Natural::class);
            })
            ->join('organization_units as village' , 'bdm_estates.ounit_id' , '=' , 'village.id')
            ->select([
                'bdm_building_dossiers.id as dossier_id',
                'bdm_building_dossiers.tracking_code as tracking_code',
                'persons.display_name as display_name',
                'pfm_prop_applications.name as application_name',
                'village.name as village_name',
                'status_permit.name as status_name',
                'masuleFani.display_name as masuleFani_name',
                'masuleFani_naturals.mobile as masuleFani_mobile',
            ])
            ->whereIn('status_permit.id' , $PermitStatuses)
            ->where('bdm_engineers.person_id' , '=' , $personID)
            ->paginate($perPage , ['*'] , 'page' , $pageNum);
        $engineers->map(function ($item) {
            $currentEnum = PermitStatusesEnum::tryFrom($item->status_name);
            if ($currentEnum) {
                $currentId = $currentEnum->id();
                $nextEnum = array_filter(PermitStatusesEnum::cases(), fn($case) => $case->id() === $currentId + 1);
                $nextEnum = reset($nextEnum);
            }
            $nextStatusData = [
                'permit_status_name' => $nextEnum->value,
            ];
            $item->status_name = $nextStatusData['permit_status_name'];
        });

        return $engineers;
    }

    public function ItemsForEngineers($id)
    {
        $status = $this->findCurrentPermitStatusOfDossier($id);
        if($status->permit_status_name == PermitStatusesEnum::fifteenth->value){
            $items = ReportItem::where('report_type_id' , BdmReportTypesEnum::FIRST_REPORT->value)->get();
            $reportType = ReportType::where('name' , BdmReportTypesEnum::FIRST_REPORT->getName())->first();
            return [
                'dossier_id' => $id,
                'report_type' => $reportType,
                'items' => $items,
            ];
        }else if($status->permit_status_name == PermitStatusesEnum::sixteenth->value){
            $items = ReportItem::where('report_type_id' , BdmReportTypesEnum::SECOND_REPORT->value)->get();
            $reportType = ReportType::where('name' , BdmReportTypesEnum::SECOND_REPORT->getName())->first();
            return [
                'dossier_id' => $id,
                'report_type' => $reportType,
                'items' => $items,
            ];
        }else if($status->permit_status_name == PermitStatusesEnum::seventeenth->value){
            $items = ReportItem::where('report_type_id' , BdmReportTypesEnum::THIRD_REPORT->value)->get();
            $reportType = ReportType::where('name' , BdmReportTypesEnum::THIRD_REPORT->getName())->first();
            return [
                'dossier_id' => $id,
                'report_type' => $reportType,
                'items' => $items,
            ];
        }else if($status->permit_status_name == PermitStatusesEnum::eighteenth->value){
            $items = ReportItem::where('report_type_id' , BdmReportTypesEnum::FOURTH_REPORT->value)->get();
            $reportType = ReportType::where('name' , BdmReportTypesEnum::FOURTH_REPORT->getName())->first();
            return [
                'dossier_id' => $id,
                'report_type' => $reportType,
                'items' => $items,
            ];
        }
    }
}
