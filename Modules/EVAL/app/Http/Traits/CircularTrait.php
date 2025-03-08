<?php

namespace Modules\EVAL\app\Http\Traits;

use Carbon\Carbon;
use GuzzleHttp\Promise\Create;
use Modules\EVAL\app\Http\Enums\EvaluationStatusEnum;
use Modules\EVAL\app\Models\EvalCircular;
use \Modules\EVAL\app\Http\Enums\EvalCircularStatusEnum;
use Modules\EVAL\app\Models\EvalCircularIndicator;
use Modules\EVAL\app\Models\EvalCircularSection;
use Modules\EVAL\app\Models\EvalCircularStatus;
use Modules\EVAL\app\Models\EvalCircularVariable;
use Modules\EVAL\app\Models\EvalEvaluation;
use Modules\EVAL\app\Models\EvalVariableTarget;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\LMS\app\Models\OucProperty;
use Modules\LMS\app\Models\OucPropertyValue;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
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

    public function CircularsList(int $perPage = 10, int $pageNumber = 1, array $data = [])
    {
        $searchTerm = $data['name'] ?? null;

        $query = EvalCircular::query()
            ->whereRaw('MATCH(title) AGAINST(?)', [$searchTerm])
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
            ->distinct();
        $result = $query->paginate($perPage, page: $pageNumber);

        return $result;
    }

    public function listOfDistrictWaitingAndCompletedList(int $perPage = 10, int $pageNumber = 1, array $data = [], $user)
    {
        $user = OrganizationUnit::whereIn('unitable_type', [
            DistrictOfc::class,
            CityOfc::class,
            StateOfc::class
        ])
            ->where('head_id', $user->id)
            ->with(['descendantsAndSelf' => function ($query) {
                $query->where('unitable_type', VillageOfc::class);
            }])
            ->get()
            ->toArray();

        $userOunits = collect($user)->pluck('id')->toArray();
        $villageIds = collect($user)
            ->pluck('descendants_and_self.*.id')
            ->flatten()
            ->toArray();

        $searchTerm = $data['name'] ?? null;

        $evalQuery = EvalEvaluation::query()
            ->where(function ($query) use ($searchTerm) {
                if (!empty($searchTerm)) {
                    $query->whereRaw('MATCH(eval_evaluations.title) AGAINST(?)', [$searchTerm])
                        ->orWhere('eval_evaluations.title', 'LIKE', '%' . $searchTerm . '%');
                }
            })
            ->joinRelationship('EvalEvaluationStatus.status')
            ->latest('evalEvaluation_status.id')
            ->joinRelationship('evalCircular')
            ->joinRelationship('targetOunits')
            ->leftJoin('eval_evaluations as eval_head', function ($join) use ($userOunits) {
                $join->on('eval_head.target_ounit_id', '=', 'eval_evaluations.target_ounit_id')
                    ->on('eval_head.eval_circular_id', '=', 'eval_evaluations.eval_circular_id')
                    ->whereIn('eval_head.evaluator_ounit_id', $userOunits);
            })
            ->select([
                'eval_evaluations.id as id',
                'eval_circulars.expired_date as expiredDate',
                'eval_evaluations.title as title',
                'organization_units.name as ounit_name',
                'organization_units.head_id as head_id',
                'eval_evaluations.evaluator_id as evaluator_id',
                'eval_head.id as eval_head_id',
            ])
            ->whereIn('eval_evaluations.target_ounit_id', $villageIds)
            ->where('statuses.name', EvaluationStatusEnum::DONE->value)
            ->distinct();

        $eval = $evalQuery->paginate($perPage, $pageNumber);

        $eval->getCollection()->transform(function ($item) {
            $expiredDate = $item->expiredDate ? Carbon::parse($item->expiredDate) : null;
            $deadLine = $expiredDate ? $expiredDate->diffInDays(now()) : null;
            return [
                'id' => $item->id,
                'title' => $item->title,
                'deadline' => $deadLine,
                'status' => $item->eval_head_id == null ? EvaluationStatusEnum::WAIT_TO_DONE->value : EvaluationStatusEnum::DONE->value,
                'status_class' => $item->eval_head_id == null ? 'primary' : 'success',
                'ounit_name' => $item->ounit_name,
            ];
        });

        return $eval;
    }


    public function listOfDistrictCompletedList(int $perPage = 10, int $pageNumber = 1, array $data = [], $user)
    {
        $user = OrganizationUnit::whereIn('unitable_type',
            [
                DistrictOfc::class,
                CityOfc::class,
                StateOfc::class,
            ])
            ->where('head_id', $user->id)
            ->with(['descendantsAndSelf' => function ($query) {
                $query->where('unitable_type', VillageOfc::class);
            }])
            ->get()
            ->toArray();
        $userOunits = [];

        foreach ($user as $person) {
            $userOunits[] = $person['id'];
        }

        $villageIds = collect($user)
            ->pluck('descendants_and_self.*.id')
            ->flatten()
            ->toArray();

        $searchTerm = $data['name'] ?? null;

        $evalQuery = EvalEvaluation::query()
            ->whereRaw('MATCH(eval_evaluations.title) AGAINST(?)', [$searchTerm])
            ->orWhere('eval_evaluations.title', 'LIKE', '%' . $searchTerm . '%')
            ->joinRelationship('EvalEvaluationStatus.status')
            ->latest('evalEvaluation_status.id')
            ->joinRelationship('evalCircular')
            ->joinRelationship('targetOunits')
            ->join('eval_evaluations as eval_head', function ($join) use ($userOunits) {
                $join->on('eval_head.target_ounit_id', '=', 'eval_evaluations.target_ounit_id')
                    ->on('eval_head.eval_circular_id', '=', 'eval_evaluations.eval_circular_id')
                    ->whereIn('eval_head.evaluator_ounit_id', $userOunits);
            })
            ->select([
                'eval_evaluations.id as id',
                'eval_circulars.expired_date as expiredDate',
                'organization_units.name as ounit_name',
                'organization_units.head_id as head_id',
                'eval_evaluations.evaluator_id as evaluator_id',
                'eval_head.id as eval_head_id',
            ])
            ->whereIn('eval_evaluations.target_ounit_id', $villageIds)
            ->where('statuses.name', EvalCircularStatusEnum::COMPLETED->value)
            ->distinct();
        $eval = $evalQuery->paginate($perPage, ['*'], 'page', $pageNumber);


        $eval->getCollection()->transform(function ($item) {
            $expiredDate = $item->expiredDate ? Carbon::parse($item->expiredDate) : null;
            $deadLine = $expiredDate ? $expiredDate->diffInDays(now()) : null;
            return [
                'id' => $item->id,
                'title' => $item->title,
                'deadline' => $deadLine,
                'status' => EvaluationStatusEnum::WAIT_TO_DONE->value,
                'status_class' => 'success',
                'ounit_name' => $item->ounit_name,
            ];

        });
        return $eval;

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
                'files.name as fileName',
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
        //     counting all VillageOfc , countEvalsForTotalForm , countEvalsForCompeleteForm
        $circular = EvalCircular::find($circularID);
        $villageCount = VillageOfc::query()
            ->whereIntegerNotInRaw('id', $this->villagesNotInCirclesOfTarget($circular))
            ->count();

        $countEvalsForTotalForm = EvalEvaluation::query()
       -> where('parent_id', null)
            ->where('eval_circular_id', $circularID)
            ->count();

        $countEvalsForCompeleteForm = EvalEvaluation::query()
            ->joinRelationship('evalEvaluationStatus.status')
            ->where('sum', '!=', null)
            ->where('evalEvaluation_status.status_id', $this->evaluationDoneStatus()->id)
            ->where('parent_id', null)
            ->where('eval_circular_id', $circularID)
            ->count();
        $countWaitingForCompelete = EvalEvaluation::query()
            ->joinRelationship('evalEvaluationStatus.status')
            ->latest('evalEvaluation_status.id')
            ->where('status_id', $this->evaluationWaitToDoneStatus()->id)
            ->count();
        $getNotifiedTime = EvalCircular::query()
            ->joinRelationship('evalCircularStatus.status')
            ->select('eval_circular_statuses.created_at')
            ->where('status_id', $this->notifiedCircularStatus()->id)
            ->get();

        //        calculate percentage

        $percentageForTotalForm = ($countEvalsForTotalForm / ($villageCount)) * 100;
        $percentageForCompeleteForm = ($countEvalsForCompeleteForm / ($villageCount)) * 100;

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
                'files.name as fileName',
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
            'created_at' => now(),
        ]);
        return $update;

    }

    public function EvaluationCompletedList($user)
    {
        $list = EvalEvaluation::query()
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
            ->where('organization_units.head_id', $user->id)
            ->where('statuses.name', EvaluationStatusEnum::WAIT_TO_DONE->value)
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
        return EvalCircular::query()
            ->leftJoinRelationship('evalCircularSections.evalCircularIndicators.evalCircularVariable')
            ->leftJoinRelationship('evalCircularStatus.status')
            ->latest('eval_circular_statuses.id')
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
                'statuses.name as statusName',
            ])
            ->where('eval_circulars.id', $circularID)
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

    public function requirementOfEditVariable($variableID)
    {
        $dropDown = EvalCircularVariable::query()
            ->joinRelationship('evalCircularIndicator.evalCircularSection')
            ->select([
                'eval_circular_sections.id as sectionID',
                'eval_circular_sections.title as title',
                'eval_circular_indicators.title as indicatorsTitle',
                'eval_circular_indicators.id as indicatorsID',
            ])
            ->where('eval_circular_variables.id', $variableID)
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

    public function showPropertiesForEdit($variableID)
    {
        $oUnitCatId = OunitCategoryEnum::VillageOfc->value;

        $show = EvalVariableTarget::query()
            ->joinRelationship('oucPropertyValue.oucProperty')
            ->select([
                'ouc_property_values.id as ouc_property_value_id',
                'eval_variable_targets.id as id',
                'ouc_properties.id as oucPropertyId',
                'ouc_properties.name as oucPropertyName',
                'ouc_property_values.value as oucPropertyValue',
                'ouc_property_values.operator as oucPropertyOperator',
            ])
            ->where('eval_circular_variables_id', $variableID)
            ->where('ounit_cat_id', $oUnitCatId)
            ->get()
            ->map(function ($item) {
                if ($item['oucPropertyName'] === 'درجه') {
                    // Convert "درجه" values to "بله" or "خیر"
                    $item['oucPropertyValue'] = $item['oucPropertyValue'] == 1 ? 'بله' : 'خیر';
                } elseif ($item['oucPropertyName'] === 'منطقه توریستی') {
                    $item['oucPropertyValue'] = $item['oucPropertyValue'] == 1 ? 'بله' : 'خیر';
                } elseif ($item['oucPropertyName'] === 'متصل به شهر') {
                    $item['oucPropertyValue'] = $item['oucPropertyValue'] == 1 ? 'بله' : 'خیر';
                } elseif ($item['oucPropertyName'] === 'مدرک') {
                    $item['oucPropertyValue'] = $item['oucPropertyValue'] == 1 ? 'بله' : 'خیر';
                } elseif ($item['oucPropertyName'] === 'منطقه کشاورزی') {
                    $item['oucPropertyValue'] = $item['oucPropertyValue'] == 1 ? 'بله' : 'خیر';
                } elseif ($item['oucPropertyName'] === 'جمعیت') {
                    $item['oucPropertyValue'] = $item['oucPropertyOperator'] == '>' ? 'بیشتر از هزار نفر' : 'کمتر از هزار نفر';
                }
                return $item;
            })
            ->toArray();

        return $show;
    }


    public function addVariable($circularID, $data)
    {
        $section = null;
        if (isset($data['sectionID'])) {
            $section = EvalCircularSection::find($data['sectionID']);
            if (!$section) {
                return ['error' => 'Section not found.'];
            }
        } else {
            $section = EvalCircularSection::create([
                'title' => $data['sectionName'],
                'eval_circular_id' => $circularID
            ]);
        }

        $indicator = null;
        if (isset($data['indicatorID'])) {
            $indicator = EvalCircularIndicator::find($data['indicatorID']);
            if (!$indicator) {
                return response()->json(['error' => 'Indicator not found.'], 404);
            }
        } else {
            $indicator = EvalCircularIndicator::create([
                'title' => $data['IndicatorName'],
                'eval_circular_section_id' => $section->id,
                'coefficient' => $data['coefficient']
            ]);
        }

        $variable = EvalCircularVariable::create([
            'title' => $data['variableName'],
            'eval_circular_indicator_id' => $indicator->id,
            'weight' => $data['weight'],
            'description' => $data['description'] ?? null,
        ]);

        $targets = [];
        $targetData = json_decode($data['oucPropertyValueID'], true);
        if (is_array($targetData) && !empty($targetData)) {
            foreach ($targetData as $propertyValueID) {
                $targets[] = EvalVariableTarget::create([
                    'eval_circular_variables_id' => $variable->id,
                    'ouc_property_value_id' => $propertyValueID,
                ]);
            }
        }
        return [$targets, $variable, $section, $indicator];

    }


    public function editVariable($variableId, $data)
    {

        if (!empty($data['sectionID'])) {
            $section = EvalCircularSection::find($data['sectionID']);
        } else {
            $section = EvalCircularSection::create([
                'title' => $data['sectionName'],
                'eval_circular_id' => $data['evalCircularID'],
            ]);
        }

        if (!empty($data['indicatorID'])) {
            $indicator = EvalCircularIndicator::find($data['indicatorID']);
        } else {
            $indicator = EvalCircularIndicator::create([
                'title' => $data['IndicatorName'],
                'coefficient' => $data['coefficient'] ?? null,
                'eval_circular_section_id' => $section->id,
            ]);
        }
        $variable = EvalCircularVariable::find($variableId);
        if (!$variable) {
            return response()->json(['error' => 'Variable not found'], 404);
        }

        $variable->update([
            'title' => $data['variableName'],
            'weight' => $data['weight'] ?? null,
            'description' => $data['description'] ?? null,
            'eval_circular_indicator_id' => $indicator->id,
        ]);


        $targets = [];
        $targetData = json_decode($data['oucPropertyValueID'], true);
        if (is_array($targetData) && !empty($targetData)) {
            foreach ($targetData as $propertyValueID) {
                $targets[] = EvalVariableTarget::query()
                    ->firstOrCreate([
                        'eval_circular_variables_id' => $variable->id,
                        'ouc_property_value_id' => $propertyValueID,
                    ]);
            }
        }
        $deleteTargets = $this->deleteTargets($data, $variable);

        return response()->json([
            'variable' => $variable,
            'indicator' => $indicator,
            'section' => $section,
            'targets' => $targets,
            'deleteTargets' => $deleteTargets,
        ]);
    }

    public function deleteTargets($data, $variable)
    {
        $delete = json_decode($data['deleteTargets'], true);

        return EvalVariableTarget::whereIn('id', $delete)
            ->where('eval_circular_variables_id', $variable->id)
            ->delete();
    }


    public function lastDataForEditVariable($variableID)
    {
        $result = EvalCircularSection::query()
            ->joinRelationship('evalCircular')
            ->joinRelationship('evalCircularIndicators.evalCircularVariable')
            ->select([
                'eval_circulars.title as name',
                'eval_circular_sections.title as sectionTitle',
                'eval_circular_sections.id as sectionID',
                'eval_circular_sections.eval_circular_id as evalCircularID',
                'eval_circular_indicators.title as indicatorsTitle',
                'eval_circular_indicators.id as indicatorsID',
                'eval_circular_variables.id as variableID',
                'eval_circular_indicators.coefficient as coefficient',
                'eval_circular_variables.title as variableName',
                'eval_circular_variables.weight as weight',
                'eval_circular_variables.description as description',
            ])
            ->where('eval_circular_variables.id', $variableID)
            ->first();
        $property = $this->showPropertiesForEdit($variableID);
        return [
            'variable' => $result,
            'property' => $property
        ];
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
