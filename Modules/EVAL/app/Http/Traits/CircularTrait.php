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
use Modules\LMS\app\Models\OucProperty;
use Modules\LMS\app\Models\OucPropertyValue;
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

            ])
            ->get();


        return $query;
    }

    public function singleCircularSidebar($circularID)
    {
        $query = EvalCircular::joinRelationship('statuses')
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

        $percentageForTotalForm = round($countEvalsForTotalForm / ($villageCount) * 100, 2) ;
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

        $expiredDate = Carbon::parse($query->expiredDate);


        return [
            $query,
            $expiredDate
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
        $deletedStatus=$this->deletedCircularStatus();
        $update=EvalCircularStatus::updateOrCreate([
            'eval_circular_id'=>$circularID,
            'status_id'=>$deletedStatus->id,
            'updated_at'=>now(),
        ]);
        return $update;

    }

    public function EvaluationCompletedList()
    {
        $list = EvalCircular::query()
            ->joinRelationship('evalCircularStatus.status')
            ->select([
                'eval_circulars.id as id',
                'statuses.name as status',
                'statuses.class_name as status_class',
                'eval_circulars.title as title',
                'eval_circulars.expired_date as expiredDate',

            ])
            ->where('statuses.name', EvalCircularStatusEnum::WAITING)
            ->get();
        return $list->map(function ($item) {
            $expiredDate = $item->expiredDate ? Carbon::parse($item->expiredDate) : null;
            $deadLine = $expiredDate ? $expiredDate->diffInDays(now()) : null;
            return [
                'title' => $item->title,
                'deadline' => $deadLine,
                'status' => $item->status,
                'status_class' => $item->status_class,
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

    public function addVariableSection($circularID,$data)
    {
        $section= EvalCircularSection::create([
            'title' => $data['name'],
            'eval_circular_id' => $circularID,

        ]);

        $indicator= EvalCircularIndicator::create([
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

    public function listing($data)
    {
        $ids = json_decode($data['ids']);
        $properties = OucProperty::whereIn('ounit_cat_id' , $ids)->select('id' , 'name')->get();

        $valueID = json_decode($data['valueID']);
        $propertyValues = OucPropertyValue::where('ouc_property_id' , $valueID)->select('id' , 'value')->get();
        return[
            'properties' => $properties,
            'propertyValues' => $propertyValues
        ];

    }



    public function deletedCircularStatus()
    {
        return  Status::where('model',EvalCircular::class)
            ->where('name',EvalCircularStatusEnum::DELETED->value)
            ->first();
    }

    public function pishnevisCircularStatus()
    {
        return Status::where('model', EvalCircular::class)
            ->where('name', EvalCircularStatusEnum::PISHNEVIS->value)
            ->first();
    }




}
