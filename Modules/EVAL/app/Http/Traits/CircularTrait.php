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
use Modules\EvalMS\app\Models\Evaluation;
use Modules\LMS\app\Models\OucProperty;
use Modules\LMS\app\Models\OucPropertyValue;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\StatusMS\app\Models\Status;

trait CircularTrait
{

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

        $query = EvalCircular::whereRaw('MATCH(title) AGAINST(?)', [$searchTerm])
            ->orWhere('title', 'LIKE', '%' . $searchTerm . '%')
            ->orderBy('title')
            ->joinRelationship('evalCircularStatus.status')
            ->select([
                'statuses.name as status',
                'statuses.class_name as status_class',
                'eval_circulars.title as name',
                'eval_circulars.id as circularID'

            ])->distinct()
            ->get();

        return $query;
    }

    public function listOfDistrict($user)
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



//        $list = EvalEvaluation::query()
//            ->joinRelationship('EvalEvaluationStatus.status')
//            ->joinRelationship('evalCircular')
//            ->joinRelationship('targetOunits')
//            ->select([
//                'eval_evaluations.id as id',
//                'statuses.name as status',
//                'statuses.class_name as status_class',
//                'eval_evaluations.title as title',
//                'eval_circulars.expired_date as expiredDate',
//                'organization_units.name as ounit_name',
//                'organization_units.head_id as head_id'
//            ])
//            ->whereIn('statuses.name', [
//                EvalCircularStatusEnum::WAITING,
//                EvalCircularStatusEnum::COMPLETED,
//            ])
//            ->distinct()
//            ->get();
//        return $list;
//        return $list->map(function ($item) {
//            $expiredDate = $item->expiredDate ? Carbon::parse($item->expiredDate) : null;
//            $deadLine = $expiredDate ? $expiredDate->diffInDays(now()) : null;
//            return [
//                'id' => $item->id,
//                'title' => $item->title,
//                'deadline' => $deadLine,
//                'status' => $item->status,
//                'status_class' => $item->status_class,
//                'ounit_name' => $item->ounit_name,
//            ];
//
//        });
    }

    public function singleCircularSidebar($circularID)
    {
        $query = EvalCircular::query()
            ->joinRelationship('statuses')
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
//            ->latest('id')
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

        //        calculate percentage

        $percentageForTotalForm = round($countEvalsForTotalForm / ($villageCount) * 100, 2);
        $percentageForCompeleteForm = round($countEvalsForCompeleteForm / ($villageCount) * 100, 2);

        return [
            'countEvals' => $countEvalsForTotalForm,
            'percentage' => $percentageForTotalForm,
            'countEvalsForCompeleteForm' => $countEvalsForCompeleteForm,
            'percentageForCompeleteForm' => $percentageForCompeleteForm,
        ];
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
            ->joinRelationship('evalCircularIndicators.evalCircularVariable')
            ->select([
                'eval_circular_sections.title as sectionTitle',
                'eval_circular_indicators.title as indicatorsTitle',
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
        });

        return $grouped->all();
    }

    public function addVariableSection($circularID, $data)
    {
        $section = EvalCircularSection::create([
            'title' => $data['name'],
            'eval_circular_id' => $circularID,

        ]);

        $indicator = EvalCircularIndicator::create([
            'title' => $data['name'],
            'eval_circular_section_id' => $section->id,
            'coefficient' => $data['coefficient'],
            'is_correct' => $data['isCorrect'],
        ]);
        $variable = EvalCircularVariable::create([
            'title' => $data['name'],
            'eval_circular_indicator_id' => $indicator->id,
            'weight' => $data['weight'],
            'description' => $data['description'],
        ]);
        return $variable;
    }


    public function editVariable($circularID, $data)
    {
        $section = EvalCircularSection::where('eval_circular_id', $circularID)->first();

        if ($section) {
            $section->update([
                'title' => $data['name'],
                'eval_circular_id' => $circularID,
            ]);
        }

        $indicator = EvalCircularIndicator::where('eval_circular_section_id', $section->id)->first();

        if ($indicator) {
            $indicator->update([
                'title' => $data['name'],
                'eval_circular_section_id' => $section->id,
                'coefficient' => $data['coefficient'],
                'is_correct' => $data['isCorrect'],
            ]);
        }

        $variable = EvalCircularVariable::where('eval_circular_indicator_id', $indicator->id)->first();

        if ($variable) {
            $variable->update([
                'title' => $data['name'],
                'eval_circular_indicator_id' => $indicator->id,
                'weight' => $data['weight'],
                'description' => $data['description'],
            ]);
        }

        return $variable;
    }

    public function editSection($circularID, $data)
    {
        $section = EvalCircularSection::where('eval_circular_id', $circularID)->first();

        if ($section) {
            $section->update([
                'title' => $data['name'],
                'eval_circular_id' => $circularID,
            ]);
        }

        return $section;
    }

    public function editIndicator($circularID, $data)
    {
        $section = EvalCircularSection::where('eval_circular_id', $circularID)->first();

        $indicator = EvalCircularIndicator::where('eval_circular_section_id', $section->id)->first();

        if ($indicator) {
            $indicator->update([
                'title' => $data['name'],
                'eval_circular_section_id' => $data['sectionID'],
                'coefficient' => $data['coefficient'],
                'is_correct' => $data['isCorrect'],
            ]);
        }

        return $indicator;
    }

    public function deleteSection($circularID)
    {
        $section = EvalCircularSection::where('eval_circular_id', $circularID)->first();
        if ($section) {
            $section->delete();
        }
        return $section;

    }

    public function deleteIndicator($circularID)
    {
        $section = EvalCircularSection::where('eval_circular_id', $circularID)->first();

        $indicator = EvalCircularIndicator::where('eval_circular_section_id', $section->id)->first();

        if ($indicator) {
            $indicator->delete();
        }
        return $indicator;
    }


    public function deletedCircularStatus()
    {
        return Status::where('model', EvalCircular::class)
            ->where('name', EvalCircularStatusEnum::DELETED->value)
            ->first();
    }

    public function pishnevisCircularStatus()
    {
        return Status::where('model', EvalCircular::class)
            ->where('name', EvalCircularStatusEnum::PISHNEVIS->value)
            ->first();
    }


}
