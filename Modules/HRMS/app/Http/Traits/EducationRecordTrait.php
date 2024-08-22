<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Collection;
use Modules\HRMS\app\Models\EducationalRecord;

trait EducationRecordTrait
{
    public function EducationalRecordStore(array|Collection $dataToInsert, int $workForceID)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $dataToInsert = [$dataToInsert];
        }

        $preparedData = $this->EducationalRecordDataPreparation($dataToInsert, $workForceID);
        $educationalRecord = EducationalRecord::insert($preparedData->toArray()[0]);

        $records = EducationalRecord::orderBy('id', 'desc')->take(count($dataToInsert))->get();


        return $records;

    }

    private function EducationalRecordDataPreparation(array|Collection $educations, int $workForceID)
    {
        if (is_array($educations)) {
            $educations = collect($educations);
        }

        $recordsToInsert = $educations->map(fn($data) => [
            'id' => $data['erID'] ?? null,
            'university_name' => $data['universityName'] ?? null,
            'field_of_study' => $data['fieldOfStudy'] ?? null,
            'start_date' => $data['startDate'] ?? null,
            'end_date' => $data['endDate'] ?? null,
            'average' => $data['average'] ?? null,
            'work_force_id' => $workForceID,
            'level_of_educational_id' => $data['levelOfEducationalID'] ?? null,
        ]);
        return $recordsToInsert;
    }

    public function EducationalRecordUpdate(array $data, EducationalRecord $educationalRecord)
    {

        $educationalRecord->university_name = $data['universityName'];
        $educationalRecord->field_of_study = $data['fieldOfStudy'];
        $educationalRecord->start_date = $data['startDate'];
        $educationalRecord->end_date = $data['endDate'] ?? null;
        $educationalRecord->average = $data['average'] ?? null;
        $educationalRecord->work_force_id = $data['workForceID'];
        $educationalRecord->level_of_educational_id = $data['levelOfEducationalID'] ?? null;

        $educationalRecord->save();


        return $educationalRecord;

    }

    public function EducationalRecordBulkUpdate(array|Collection $data, int $workForceID)
    {
        $dataToUpsert = $this->EducationalRecordDataPreparation($data, $workForceID);
        $result = EducationalRecord::upsert($dataToUpsert->toArray(), ['id']);
        return $result;
    }

}
