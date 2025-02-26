<?php

namespace Modules\EVAL\app\Http\Traits;

use Carbon\Carbon;
use GuzzleHttp\Promise\Create;
use Modules\EVAL\app\Models\EvalCircular;
use \Modules\EVAL\app\Http\Enums\EvalCircularStatusEnum;
use Modules\EVAL\app\Models\EvalCircularSection;
use Modules\EVAL\app\Models\EvalCircularStatus;
use Modules\EVAL\app\Models\EvalEvaluation;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\StatusMS\app\Models\Status;

trait CircularTrait
{

    public function AddCircular($data, $user)
    {
        $status = Status::where('model', EvalCircular::class)->where('name', EvalCircularStatusEnum::PISHNEVIS->value)->first();
        $expiredDate = now()->addDays($data['deadline']);
        $circular = EvalCircular::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'maximum_value' => $data['maximumValue'],
            'file_id' => $data['fileID'],
            'creator_id' => $user->id,
            'create_date' => now(),
            'expired_date' => $data['expiredDate']? convertPersianToGregorianBothHaveTimeAndDont($data['expiredDate']) : null,
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
        $searchTerm = $data['title'] ?? null;

        $query = EvalCircular::whereRaw('MATCH(title) AGAINST(?)', [$searchTerm])
            ->orWhere('title', 'LIKE', '%' . $searchTerm . '%')
            ->orderBy('title')
            ->joinRelationship('evalCircularStatus.status')
            ->select([
                'statuses.name as status',
                'statuses.class_name as status_class',
                'eval_circulars.title as title',
                'eval_circulars.id as circularID'

            ])
            ->get();

        return $query->flatten();
    }

    public function singleCircularSidebar($circularID)
    {
        $query = EvalCircular::joinRelationship('statuses')
            ->joinRelationship('file.extension')
            ->select([
                'eval_circulars.id as id',
                'eval_circulars.title as title',
                'eval_circulars.description as description',
                'eval_circulars.maximum_value as MaximumValue',
                'eval_circulars.file_id as fileID',
                'eval_circulars.create_date as createDate',
                'eval_circulars.expired_date as expiredDate',
                'statuses.name as statusName',
                'files.slug as downloadUrl',
                'files.size as fileSize',
                'extensions.name as extensionName',
            ])
            ->where('eval_circulars.id', $circularID)
            ->latest('id')
            ->get();
        $completedCircularCount = $this->singleCircularMain($circularID);
        return [
            'data' => $query,
            'completedCircularCount' => $completedCircularCount
        ];

    }


    public function singleCircularMain($circularID)
    {
        $villageCount = VillageOfc::count();

        $countEvalsForTotalForm = EvalEvaluation::
        whereNotNull('target_ounit_id')
            ->where('parent_id', null)
            ->where('eval_circular_id', $circularID)
            ->count();

        $countEvalsForCompeleteForm = EvalEvaluation::
            where('sum','!=',null)
            ->where('parent_id', null)
            ->where('eval_circular_id', $circularID)
            ->count();

        $percentageForTotalForm=round($countEvalsForTotalForm/($villageCount)*100,2).'%';
        $percentageForCompeleteForm=round($countEvalsForCompeleteForm/($villageCount)*100,2).'%';

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
        joinRelationship('statuses')
            ->joinRelationship('file.extension')
            ->select([
                'eval_circulars.id as id',
                'eval_circulars.title as title',
                'eval_circulars.description as description',
                'eval_circulars.maximum_value as MaximumValue',
                'eval_circulars.file_id as fileID',
                'files.slug as downloadUrl',
                'eval_circulars.create_date as createDate',
                'eval_circulars.expired_date as expiredDate'
            ])
            ->where('eval_circulars.id', $circularID)
            ->first();

        $createDate = Carbon::parse($query->createDate);
        $expiredDate = $query->expiredDate ? Carbon::parse($query->expiredDate) : null;
        $deadline = $expiredDate ? $expiredDate->diffInDays($createDate) : null;


        return [
            'data' => $query,
            'deadline' => $deadline,
        ];
    }

    public function circularEdit($circularID, $data)
    {
        $circular = EvalCircular::where('id', $circularID)->first();

        $updateData = [
            'title' => $data['title'] ?? $circular->title,
            'description' => $data['description'] ?? $circular->description,
            'maximum_value' => $data['maximumValue'] ?? $circular->maximum_value,
            'file_id' => $data['fileID'] ?? $circular->file_id,
        ];

        // Add expired_date only if 'deadline' is provided
        if (isset($data['deadline'])) {
            $updateData['expired_date'] = now()->addDays($data['deadline']);
        }

        $circular->update($updateData);

        return $circular;
    }

    public function deleteCircular($circularID)
    {
        $circular = EvalCircularStatus::where('eval_circular_id', $circularID)->first();

        $deletedStatus = Status::where('model', EvalCircular::class)
            ->where('name', EvalCircularStatusEnum::DELETED->value)
            ->first();

        $circular->where('eval_circular_id', $circularID)->update([
            'status_id' => $deletedStatus->id,
            'updated_at' => now(),
        ]);
        return $circular;


    }

    public function arzyabiEnrollmentList()
    {
        $list = EvalCircular::query()
            ->joinRelationship('evalCircularStatus.status')
            ->select([
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

    public function dropDownsOfAddVariable($circularID)
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




}
