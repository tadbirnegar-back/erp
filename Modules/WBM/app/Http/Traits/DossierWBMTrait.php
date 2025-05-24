<?php

namespace Modules\WBM\app\Http\Traits;


use Modules\BDM\app\Http\Enums\BdmReportTypesEnum;
use Modules\BDM\app\Http\Enums\EngineersTypeEnum;
use Modules\BDM\app\Http\Enums\PermitStatusesEnum;
use Modules\BDM\app\Http\Traits\DossierTrait;
use Modules\BDM\app\Http\Traits\PermitTrait;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\DossierReport;
use Modules\BDM\app\Models\Engineer;
use Modules\BDM\app\Models\ReportDataItem;
use Modules\BDM\app\Models\ReportItem;
use Modules\BDM\app\Models\ReportType;
use Modules\HRMS\app\Http\Enums\ScriptTypesEnum;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\ScriptType;
use Modules\ODOC\app\Http\Enums\OdocDocumentComponentsTypeEnum;
use Modules\ODOC\app\Models\Document;
use Modules\PersonMS\app\Models\Natural;

trait DossierWBMTrait
{
    use RecruitmentScriptTrait, PermitTrait, DossierTrait;

    public function TasksOfEngineers($data,$pageNum, $perPage, $personID)
    {
        $scriptType = ScriptType::where('title', ScriptTypesEnum::MASOULE_FAANI->value)->first();
        $activeScriptStatus = $this->activeRsStatus();
        $PermitStatuses = [$this->eighthStatus()->id, $this->fifteenthStatus()->id, $this->sixteenthStatus()->id, $this->seventeenthStatus()->id, $this->eighteenthStatus()->id];


        $engineers = Engineer::join('bdm_engineers_building', function ($join) {
            $join->on('bdm_engineers_building.engineer_id', '=', 'bdm_engineers.id')
                ->where('bdm_engineers_building.engineer_type_id', '=', EngineersTypeEnum::NAZER->id());
        })
            ->join('bdm_building_dossiers', 'bdm_building_dossiers.id', '=', 'bdm_engineers_building.dossier_id')
            ->join('bdm_owners', function ($join) {
                $join->on('bdm_owners.dossier_id', '=', 'bdm_building_dossiers.id')
                    ->where('is_main_owner', '=', true);
            })
            ->join('persons', 'bdm_owners.person_id', '=', 'persons.id')
            ->join('naturals', function ($join) {
                $join->on('persons.personable_id', '=', 'naturals.id')
                    ->where('persons.personable_type', '=', Natural::class);
            })
            ->join('users', 'bdm_owners.person_id', '=', 'users.person_id')
            ->join('bdm_estates', 'bdm_building_dossiers.id', '=', 'bdm_estates.dossier_id')
            ->join('bdm_estate_app_sets', 'bdm_estates.id', '=', 'bdm_estate_app_sets.estate_id')
            ->join('pfm_prop_applications', 'bdm_estate_app_sets.app_id', '=', 'pfm_prop_applications.id')
            ->join('bdm_building_permit_status', function ($join) {
                $join->on('bdm_building_dossiers.id', '=', 'bdm_building_permit_status.dossier_id')
                    ->whereRaw('bdm_building_permit_status.id = (SELECT MAX(id) FROM bdm_building_permit_status WHERE dossier_id = bdm_building_dossiers.id)');
            })
            ->join('statuses as status_permit', 'bdm_building_permit_status.status_id', '=', 'status_permit.id')
            ->join('recruitment_scripts', function ($join) use ($scriptType) {
                $join->on('bdm_estates.ounit_id', '=', 'recruitment_scripts.organization_unit_id')
                    ->where('recruitment_scripts.script_type_id', '=', $scriptType->id);
            })
            ->join('recruitment_script_status', function ($join) use ($activeScriptStatus) {
                $join->on('recruitment_scripts.id', '=', 'recruitment_script_status.recruitment_script_id')
                    ->whereRaw('recruitment_script_status.id = (SELECT MAX(id) FROM recruitment_script_status WHERE recruitment_script_id = recruitment_scripts.id)')
                    ->where('recruitment_script_status.status_id', '=', $activeScriptStatus->id);
            })
            ->join('work_forces', function ($join) {
                $join->on('recruitment_scripts.employee_id', '=', 'work_forces.workforceable_id')
                    ->where('work_forces.workforceable_type', '=', Employee::class);
            })
            ->join('persons as masuleFani', 'work_forces.person_id', '=', 'masuleFani.id')
            ->join('naturals as masuleFani_naturals', function ($join) {
                $join->on('masuleFani.personable_id', '=', 'masuleFani_naturals.id')
                    ->where('masuleFani.personable_type', '=', Natural::class);
            })
            ->join('organization_units as village', 'bdm_estates.ounit_id', '=', 'village.id')
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
            ->whereIn('status_permit.id', $PermitStatuses)
            ->when(isset($data['name']), function ($query) use ($data) {
                $query->where('bdm_building_dossiers.tracking_code', 'like', '%' . $data['name'] . '%');
            })

            ->where('bdm_engineers.person_id', '=', $personID)
            ->paginate($perPage, ['*'], 'page', $pageNum);
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
            if ($nextStatusData['permit_status_name'] == PermitStatusesEnum::ninth->value) {
                $item->action_type = 'uploadPlan';
            } else {
                $item->action_type = 'fullFillReports';
            }
        });

        return $engineers;
    }

    public function ItemsForEngineers($id)
    {
        $status = $this->findCurrentPermitStatusOfDossier($id);
        $currentEnum = PermitStatusesEnum::tryFrom($status->permit_status_name);
        if ($currentEnum) {
            $currentId = $currentEnum->id();
            $nextEnum = array_filter(PermitStatusesEnum::cases(), fn($case) => $case->id() === $currentId + 1);
            $nextEnum = reset($nextEnum);
        }
        if ($status->permit_status_name == PermitStatusesEnum::fifteenth->value) {
            $items = ReportItem::where('report_type_id', BdmReportTypesEnum::FIRST_REPORT->value)->get();
            $reportType = ReportType::where('name', BdmReportTypesEnum::FIRST_REPORT->getName())->first();
            return [
                'dossier_id' => $id,
                'report_type' => $reportType,
                'items' => $items,
                'nextStatus' => $nextEnum->value,
            ];
        } else if ($status->permit_status_name == PermitStatusesEnum::sixteenth->value) {
            $items = ReportItem::where('report_type_id', BdmReportTypesEnum::SECOND_REPORT->value)->get();
            $reportType = ReportType::where('name', BdmReportTypesEnum::SECOND_REPORT->getName())->first();
            return [
                'dossier_id' => $id,
                'report_type' => $reportType,
                'items' => $items,
                'nextStatus' => $nextEnum->value,
            ];
        } else if ($status->permit_status_name == PermitStatusesEnum::seventeenth->value) {
            $items = ReportItem::where('report_type_id', BdmReportTypesEnum::THIRD_REPORT->value)->get();
            $reportType = ReportType::where('name', BdmReportTypesEnum::THIRD_REPORT->getName())->first();
            return [
                'dossier_id' => $id,
                'report_type' => $reportType,
                'items' => $items,
                'nextStatus' => $nextEnum->value,
            ];
        } else if ($status->permit_status_name == PermitStatusesEnum::eighteenth->value) {
            $items = ReportItem::where('report_type_id', BdmReportTypesEnum::FOURTH_REPORT->value)->get();
            $reportType = ReportType::where('name', BdmReportTypesEnum::FOURTH_REPORT->getName())->first();
            return [
                'dossier_id' => $id,
                'report_type' => $reportType,
                'items' => $items,
                'nextStatus' => $nextEnum->value,
            ];
        }
    }

    public function makeBDMReportItems($data, $dossierID, $user)
    {
        $dossierReport = DossierReport::where('dossier_id', $dossierID)
            ->where('report_type_id', $data['reportTypeID'])
            ->first();

        if (!$dossierReport) {
            $reportData = $this->prepareDossierReportData($data, $dossierID, $user);
            $dossierReport = DossierReport::create($reportData);
        }

        $items = json_decode($data['report_items']);
        $lastStatus = $this->findCurrentPermitStatusOfDossier($dossierID);
        if($lastStatus->permit_status_name == PermitStatusesEnum::fifteenth->value)
        {
            $componentToRender = OdocDocumentComponentsTypeEnum::FoundationConcreteLayingPDF->value;
        }
        if($lastStatus->permit_status_name == PermitStatusesEnum::sixteenth->value)
        {
            $componentToRender = OdocDocumentComponentsTypeEnum::StructureSekeletonPDF->value;
        }

        if($lastStatus->permit_status_name == PermitStatusesEnum::seventeenth->value)
        {
            $componentToRender = OdocDocumentComponentsTypeEnum::HardeningSofteningStructurePDF->value;
        }

        if($lastStatus->permit_status_name == PermitStatusesEnum::eighteenth->value)
        {
            $componentToRender = OdocDocumentComponentsTypeEnum::FinalReportPDF->value;
        }

        $dossier = BuildingDossier::join('bdm_owners' , function ($join) {
            $join->on('bdm_owners.dossier_id', '=', 'bdm_building_dossiers.id')
                ->where('is_main_owner', '=', true);
        })
            ->join('bdm_engineers_building' , function ($join) {
                $join->on('bdm_engineers_building.dossier_id', '=', 'bdm_building_dossiers.id')
                    ->where('bdm_engineers_building.engineer_type_id', '=', EngineersTypeEnum::NAZER->id());
            })
            ->join('bdm_engineers', 'bdm_engineers_building.engineer_id', '=', 'bdm_engineers.id')
            ->join('persons as nazer', 'bdm_engineers.person_id', '=', 'nazer.id')
            ->join('persons as main_owner', 'bdm_owners.person_id', '=', 'main_owner.id')
            ->join('bdm_estates', 'bdm_building_dossiers.id', '=', 'bdm_estates.dossier_id')
            ->select([
                'bdm_building_dossiers.id as dossier_id',
                'bdm_building_dossiers.tracking_code as tracking_code',
                'bdm_building_dossiers.created_date as created_date',
                'main_owner.display_name as owner_name',
                'nazer.display_name as nazer_name',
                'bdm_engineers.dossier_number',
                'bdm_engineers.registration_number',
                'bdm_estates.allow_floor',
                'bdm_estates.area',
            ])
            ->find($dossierID);
        $dossier->building_material = '';

        if(in_array($lastStatus->permit_status_name, [PermitStatusesEnum::fifteenth->value , PermitStatusesEnum::sixteenth->value , PermitStatusesEnum::seventeenth->value])){
            $itemsForPdf = [
                "component_to_render" => $componentToRender,
                "items" => [],
                'description' => $dossierReport->description,
                'report_violation_id' => $dossierReport->report_violation_id,
                'tracking_code' => $dossier->tracking_code,
                'dossier_date' => $dossier->created_date,
                'nazer_name' => $dossier->nazer_name,
                'registration_number' => $dossier->registration_number,
                'dossier_number' => $dossier->dossier_number,
                'building_material' => $dossier->building_material,
                'floor_area' => $dossier->area,
                'allow_floor' => $dossier->allow_floor,
                'owner_name' => $dossier->owner_name,
            ];
        }else{
            $foundation = Document::where('model_id', $dossierID)
                ->where('model', BuildingDossier::class)
                ->where('component_to_render', OdocDocumentComponentsTypeEnum::FoundationConcreteLayingPDF->value)
                ->select([
                    'component_to_render',
                    'title',
                    'serial_number'
                ])
                ->first();
            $structure = Document::where('model_id', $dossierID)
                ->where('model', BuildingDossier::class)
                ->where('component_to_render', OdocDocumentComponentsTypeEnum::StructureSekeletonPDF->value)
                ->select([
                    'component_to_render',
                    'title',
                    'serial_number'
                ])
                ->first();
            $hardening = Document::where('model_id', $dossierID)
                ->where('model', BuildingDossier::class)
                ->where('component_to_render', OdocDocumentComponentsTypeEnum::HardeningSofteningStructurePDF->value)
                ->select([
                    'component_to_render',
                    'title',
                    'serial_number'
                ])
                ->first();
            $itemsForPdf = [
                "component_to_render" => $componentToRender,
                "items" => [],
                'description' => $dossierReport->description,
                'report_violation_id' => $dossierReport->report_violation_id,
                'tracking_code' => $dossier->tracking_code,
                'dossier_date' => $dossier->created_date,
                'nazer_name' => $dossier->nazer_name,
                'registration_number' => $dossier->registration_number,
                'dossier_number' => $dossier->dossier_number,
                'building_material' => $dossier->building_material,
                'floor_area' => $dossier->area,
                'allow_floor' => $dossier->allow_floor,
                'owner_name' => $dossier->owner_name,
                'foundation' => $foundation,
                'structure' => $structure,
                'hardening' => $hardening,
            ];

        }


        foreach ($items as $item) {
            $reportDataItem = ReportDataItem::where('report_id', $dossierReport->id)
                ->where('report_item_id', $item)
                ->first();
            if (!$reportDataItem) {
                $reportDataItem = $this->prepareReportDataItem($dossierReport->id, $item);
                 ReportDataItem::create($reportDataItem);
            }
            $itemsForPdf["items"][] = $reportDataItem;
        }

        $this->makeReportPdfs($dossierID , $itemsForPdf , $user);

        $this->upgradeOneLevel($dossierID);
    }

    protected function prepareDossierReportData($data, $dossierID, $user)
    {
        return [
            'dossier_id' => $dossierID,
            'report_type_id' => $data['reportTypeID'],
            'description' => $data['description'] ?? null,
            'report_violation_id' => $data['reportViolationID'] ?? null,
            'creator_id' => $user->id,
            'created_date' => now(),
        ];
    }

    protected function prepareReportDataItem($reportId, $itemId)
    {
        return [
            'report_id' => $reportId,
            'report_item_id' => $itemId,
        ];
    }

}
