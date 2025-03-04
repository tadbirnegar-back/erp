<?php

namespace Modules\EVAL\app\Http\Traits;

use Carbon\Carbon;
use GuzzleHttp\Promise\Create;
use Modules\EVAL\app\Models\EvalCircular;
use \Modules\EVAL\app\Http\Enums\EvalCircularStatusEnum;
use Modules\EVAL\app\Models\EvalCircularIndicator;
use Modules\EVAL\app\Models\EvalCircularSection;
use Modules\EVAL\app\Models\EvalCircularStatus;
use Modules\EVAL\app\Models\EvalCircularVariable;
use Modules\EVAL\app\Models\EvalEvaluation;
use Modules\EVAL\app\Models\EvalVariableTarget;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\LMS\app\Models\OucProperty;
use Modules\LMS\app\Models\OucPropertyValue;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\StatusMS\app\Models\Status;

trait CircularTrait
{
    use EvaluationTrait;

    public function AddCircular($data, $user)
    {
        $status = $this->pishnevisCircularStatus();
        $circular = EvalCircular::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'maximum_value' => $data['maximumValue'],
            'file_id' => $data['fileID'],
            'creator_id' => $user->id,
            'create_date' => now(),
            'expired_date' => $data['expiredDate'] ? convertPersianToGregorianBothHaveTimeAndDont($data['expiredDate']) : null,
        ]);
        EvalCircularStatus::create([
            'status_id' => $status->id,
            'created_at' => now(),
            'updated_at' => now(),
            'eval_circular_id' => $circular->id,
        ]);
        return $circular;

    }

    public function CircularsList(array $data = [])
    {
        $searchTerm = $data['name'] ?? null;

        $query = EvalCircular::query()
       -> whereRaw('MATCH(title) AGAINST(?)', [$searchTerm])
            ->orWhere('title', 'LIKE', '%' . $searchTerm . '%')
            ->joinRelationship('lastStatusOfEvalCircular.status')
            ->whereRaw('eval_circular_statuses.created_at =
                    (SELECT MAX(created_at) FROM eval_circular_statuses WHERE
                    eval_circular_id = eval_circulars.id)')
            ->select([
                'statuses.id as status_id',
                'statuses.name as status',
                'statuses.class_name as status_class',
                'eval_circulars.title as name',
                'eval_circulars.id as circularID',

            ])
            ->distinct()
            ->get();

        return $query;
    }

    public function listOfDistrictWaitingAndCompletedList($user)
    {

        $user = OrganizationUnit::where('unitable_type', DistrictOfc::class)
            ->where('head_id', $user->id)
            ->with(['descendantsAndSelf' => function ($query) {
                $query->where('unitable_type', VillageOfc::class);
            }])
            ->get()
            ->toArray();

        $villageIds = collect($user)
            ->pluck('descendants_and_self.*.id')
            ->flatten()
            ->toArray();


        $eval = EvalEvaluation::query()
            ->joinRelationship('EvalEvaluationStatus.status')
            ->latest('evalEvaluation_status.id')

            ->joinRelationship('evalCircular')
            ->joinRelationship('targetOunits')
            ->select([
                'eval_evaluations.id as id',
                'statuses.name as status',
                'statuses.class_name as status_class',
                'eval_evaluations.title as title',
                'eval_circulars.expired_date as expiredDate',
                'organization_units.name as ounit_name',
                'organization_units.head_id as head_id'
            ])
         ->whereIn('target_ounit_id', $villageIds)

        ->whereIn('statuses.name', [
                EvalCircularStatusEnum::WAITING,
                EvalCircularStatusEnum::COMPLETED,
            ])
            ->distinct()
            ->get();
        return $eval->map(function ($item) {
            $expiredDate = $item->expiredDate ? Carbon::parse($item->expiredDate) : null;
            $deadLine = $expiredDate ? $expiredDate->diffInDays(now()) : null;
            return [
                'id' => $item->id,
                'title' => $item->title,
                'deadline' => $deadLine,
                'status' => $item->status,
                'status_class' => $item->status_class,
                'ounit_name' => $item->ounit_name,
            ];

        });

    }

    public function listOfDistrictCompletedList($user)
    {
        $user = OrganizationUnit::where('unitable_type', DistrictOfc::class)
            ->where('head_id', $user->id)
            ->with(['descendantsAndSelf' => function ($query) {
                $query->where('unitable_type', VillageOfc::class);
            }])
            ->get()
            ->toArray();

        $villageIds = collect($user)
            ->pluck('descendants_and_self.*.id')
            ->flatten()
            ->toArray();


        $eval = EvalEvaluation::query()
            ->joinRelationship('EvalEvaluationStatus.status')
            ->latest('evalevaluation_status.id')

            ->joinRelationship('evalCircular')
            ->joinRelationship('targetOunits')
            ->select([
                'eval_evaluations.id as id',
                'statuses.name as status',
                'statuses.class_name as status_class',
                'eval_evaluations.title as title',
                'eval_circulars.expired_date as expiredDate',
                'organization_units.name as ounit_name',
                'organization_units.head_id as head_id'
            ])
            ->whereIn('target_ounit_id', $villageIds)

            ->where('statuses.name', EvalCircularStatusEnum::COMPLETED)
            ->distinct()
            ->get();
        return $eval->map(function ($item) {
            $expiredDate = $item->expiredDate ? Carbon::parse($item->expiredDate) : null;
            $deadLine = $expiredDate ? $expiredDate->diffInDays(now()) : null;
            return [
                'id' => $item->id,
                'title' => $item->title,
                'deadline' => $deadLine,
                'status' => $item->status,
                'status_class' => $item->status_class,
                'ounit_name' => $item->ounit_name,
            ];

        });

    }

    public function singleCircularSidebar($circularID)
    {
        $query = EvalCircular::query()
            ->joinRelationship('evalCircularStatus.status')
            ->latest('eval_circular_statuses.id')
            ->joinRelationship('file.extension')
            ->select([
                'eval_circulars.id as id',
                'eval_circulars.title as name',
                'eval_circulars.description as description',
                'eval_circulars.maximum_value as MaximumValue',
                'eval_circulars.file_id as fileID',
                'eval_circulars.create_date as createDate',
                'eval_circulars.expired_date as expiredDate',
                'statuses.name as statusName',
                'statuses.class_name as className',
                'files.slug as downloadUrl',
                'files.size as fileSize',
                'extensions.name as extensionName',
            ])
            ->where('eval_circulars.id', $circularID)
            ->get();

        $completedCircularCount = $this->singleCircularMain($circularID);

        return [
            'data' => $query,
            'completedCircularCount' => $completedCircularCount
        ];
    }


    public function singleCircularMain($circularID)
    {
        //        counting VillageOfc , countEvalsForTotalForm , countEvalsForCompeleteForm

        $villageCount = VillageOfc::count();

        $countEvalsForTotalForm = EvalEvaluation::
        whereNotNull('target_ounit_id')
            ->where('parent_id', null)
            ->where('eval_circular_id', $circularID)
            ->count();

        $countEvalsForCompeleteForm = EvalEvaluation::
        where('sum', '!=', null)
            ->where('parent_id', null)
            ->where('eval_circular_id', $circularID)
            ->count();
        $countWaitingForCompelete = EvalEvaluation::query()
            ->joinRelationship('evalEvaluationStatus.status')
            ->latest('evalevaluation_status.id')
            ->where('status_id', $this->evaluationWaitToDoneStatus()->id)
            ->count();
        $getNotifiedTime = EvalCircular::query()
            ->joinRelationship('evalCircularStatus.status')
            ->select('eval_circular_statuses.created_at')
            ->where('status_id', $this->notifiedCircularStatus()->id)
            ->get();

        //        calculate percentage

        $percentageForTotalForm = ($countEvalsForTotalForm / ($villageCount));
        $percentageForCompeleteForm = ($countEvalsForCompeleteForm / ($villageCount));

        return [
            'countEvals' => $countEvalsForTotalForm,
            'percentage' => $percentageForTotalForm,
            'countEvalsForCompeleteForm' => $countEvalsForCompeleteForm,
            'percentageForCompeleteForm' => $percentageForCompeleteForm,
            'WaitingToDone' => $countWaitingForCompelete,
            'notifiedTime' => explode(' ', convertDateTimeGregorianToJalaliDateTime($getNotifiedTime))[0]];
    }


    public function lastDataForEditCircular($circularID)
    {
        $query = EvalCircular::
        joinRelationship('file.extension')
            ->select([
                'eval_circulars.id as id',
                'eval_circulars.title as name',
                'eval_circulars.description as description',
                'eval_circulars.maximum_value as MaximumValue',
                'eval_circulars.file_id as fileID',
                'files.slug as downloadUrl',
                'files.size as fileSize',
                'extensions.name as extensionName',
                'eval_circulars.create_date as createDate',
                'eval_circulars.expired_date as expiredDate'
            ])
            ->where('eval_circulars.id', $circularID)
            ->first();


        return [
            'query' => $query,
        ];
    }

    public function circularEdit($circularID, $data, $user)
    {
        $circular = EvalCircular::where('id', $circularID)->first();
        $updateData = [
            'title' => $data['title'] ?? $circular->title,
            'description' => $data['description'] ?? $circular->description,
            'maximum_value' => $data['maximumValue'] ?? $circular->maximum_value,
            'file_id' => $data['fileID'] ?? $circular->file_id,
            'created_date' => now(),
            'creator_id' => $user->id,
            'expired_date' => $data['expiredDate'] ? convertPersianToGregorianBothHaveTimeAndDont($data['expiredDate']) : null,

        ];

        $circular->update($updateData);

        return $circular;
    }

    public function deleteCircular($circularID)
    {
        $deletedStatus = $this->deletedCircularStatus();
        $update = EvalCircularStatus::updateOrCreate([
            'eval_circular_id' => $circularID,
            'status_id' => $deletedStatus->id,
            'updated_at' => now(),
        ]);
        return $update;

    }

    public function EvaluationCompletedList($user)
    {
        $list = EvalEvaluation::query()
            ->joinRelationship('EvalEvaluationStatus.status')
            ->latest('evalevaluation_status.id')

            ->joinRelationship('evalCircular')
            ->joinRelationship('targetOunits')
            ->select([
                'eval_evaluations.id as id',
                'statuses.name as status',
                'statuses.class_name as status_class',
                'eval_evaluations.title as title',
                'eval_circulars.expired_date as expiredDate',
                'organization_units.name as ounit_name',
                'organization_units.head_id as head_id'
            ])
            ->where('organization_units.head_id', $user->id)
            ->where('statuses.name', EvalCircularStatusEnum::WAITING)
            ->distinct()
            ->get();
        return $list->map(function ($item) {
            $expiredDate = $item->expiredDate ? Carbon::parse($item->expiredDate) : null;
            $deadLine = $expiredDate ? $expiredDate->diffInDays(now()) : null;
            return [
                'id' => $item->id,
                'title' => $item->title,
                'deadline' => $deadLine,
                'status' => $item->status,
                'status_class' => $item->status_class,
                'ounit_name' => $item->ounit_name,
            ];

        });
    }

    public function completingItems($circularID)
    {
        return EvalCircularSection::query()
            ->joinRelationship('evalCircular')
            ->joinRelationship('evalCircularIndicators.evalCircularVariable')
            ->select([
                'eval_circulars.title as name',
                'eval_circular_sections.title as sectionTitle',
                'eval_circular_sections.id as sectionID',
                'eval_circular_indicators.title as indicatorsTitle',
                'eval_circular_indicators.id as indicatorsID',
                'eval_circular_variables.id as variableID',
                'eval_circular_indicators.coefficient as coefficient',
                'eval_circular_variables.title as variableName',
                'eval_circular_variables.weight as weight',
            ])
            ->where('eval_circular_sections.eval_circular_id', $circularID)
            ->get();

    }



    public function requirementOfAddVariable($circularID)
    {
        $dropDown = EvalCircular::joinRelationship('evalCircularSections.evalCircularIndicators')
            ->select([
                'eval_circular_sections.id as sectionID',
                'eval_circular_sections.title as title',
                'eval_circular_indicators.title as indicatorsTitle',
                'eval_circular_indicators.id as indicatorsID',
            ])
            ->where('eval_circulars.id', $circularID)
            ->get();

        $grouped = $dropDown->groupBy('sectionID')->map(function ($items, $sectionID) {
            return (object)[
                'sectionID' => $sectionID,
                'section_title' => $items->first()->title,
                'indicators' => $items->map(function ($item) {
                    return (object)[
                        'id' => $item->indicatorsID,
                        'title' => trim($item->indicatorsTitle)
                    ];
                })->values()
            ];
        })->values();

        return $grouped->all();
    }

    public function addVariableSection($circularID, $data)
    {
        $section = EvalCircularSection::create([
            'title' => $data['sectionName'],
            'eval_circular_id' => $circularID,
        ]);

        $existingIndicator = EvalCircularIndicator::where('eval_circular_section_id', $section->id)->first();

        $coefficient = $existingIndicator ? $existingIndicator->coefficient : $data['coefficient'];

        $indicator = EvalCircularIndicator::create([
            'title' => $data['IndicatorName'],
            'eval_circular_section_id' => $section->id,
            'coefficient' => $coefficient,
        ]);

        $variable = EvalCircularVariable::create([
            'title' => $data['variableName'],
            'eval_circular_indicator_id' => $indicator->id,
            'weight' => $data['weight'],
            'description' => $data['description'] ?? null,
        ]);

        $target = json_decode($data['oucPropertyValueID']);
        foreach ($target as $propertyValueID) {
            $targets[] = EvalVariableTarget::create([
                'eval_circular_variables_id' => $variable->id,
                'ouc_property_value_id' => $propertyValueID,
            ]);
        }

        return $targets;
    }



    public function editVariable($sectionID, $data)
    {
         EvalCircularSection::where('id', $sectionID)->update([
            'title' => $data['sectionName'],
        ]);

        $indicator = EvalCircularIndicator::where('eval_circular_section_id', $sectionID)
            ->update([
                'title' => $data['IndicatorName'],
                'coefficient' => $data['coefficient'] ?? null,
            ]);

        $variable= EvalCircularVariable::where('eval_circular_indicator_id', $indicator)
            ->update([
                'title' => $data['variableName'],
                'weight' => $data['weight'] ?? null,
                'description' => $data['description'] ?? null,
            ]);
        $target = json_decode($data['oucPropertyValueID']);
        foreach ($target as $propertyValueID) {
            $targets[] = EvalVariableTarget::create([
                'eval_circular_variables_id' => $variable->id,
                'ouc_property_value_id' => $propertyValueID,
            ]);
        }
        return $targets;
    }
    public function lastDataForEditVariable($variableID)
    {
        return EvalCircularSection::query()
            ->joinRelationship('evalCircular')
            ->joinRelationship('evalCircularIndicators.evalCircularVariable')
            ->select([
                'eval_circulars.title as name',
                'eval_circular_sections.title as sectionTitle',
                'eval_circular_sections.id as sectionID',
                'eval_circular_indicators.title as indicatorsTitle',
                'eval_circular_indicators.id as indicatorsID',
                'eval_circular_variables.id as variableID',
                'eval_circular_indicators.coefficient as coefficient',
                'eval_circular_variables.title as variableName',
                'eval_circular_variables.weight as weight',
            ])
            ->where('eval_circular_variables.id', $variableID)
            ->first();
    }


    public function editSection($section, $data)
    {
        $section->title = $data['name'];
        $section->save();


        return $section;
    }


    public function editIndicator($indicator, $data)
    {
        $indicator->title = $data['name'];
        $indicator->coefficient = $data['coefficient'];
        $indicator->save();

        return $indicator;
    }

    public function deleteSection($sectionID)
    {
        $section = EvalCircularSection::where('eval_circular_sections.id', $sectionID)->first();
        if ($section) {
            $section->delete();
        }
        return $section;

    }

    public function deleteIndicator($indicatorID)
    {

        $indicator = EvalCircularIndicator::where('eval_circular_indicators.id', $indicatorID)->first();

        if ($indicator) {
            $indicator->delete();
        }
        return $indicator;
    }

    public function deleteVariable($variableID)
    {
        return EvalCircularVariable::where('eval_circular_variables.id', $variableID)->delete();
    }


    public function deletedCircularStatus()
    {
        return Status::where('model', EvalCircular::class)
            ->where('name', EvalCircularStatusEnum::DELETED->value)
            ->first();
    }

    public function expiredCircularStatus()
    {
        return Status::where('model', EvalCircular::class)
            ->where('name', EvalCircularStatusEnum::EXPIRED->value)
            ->first();
    }

    public function pishnevisCircularStatus()
    {
        return Status::where('model', EvalCircular::class)
            ->where('name', EvalCircularStatusEnum::PISHNEVIS->value)
            ->first();
    }

    public function notifiedCircularStatus()
    {
        return Status::where('model', EvalCircular::class)
            ->where('name', EvalCircularStatusEnum::NOTIFIED->value)
            ->first();
    }


}
