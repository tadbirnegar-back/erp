<?php

namespace Modules\HRMS\app\Http\Repositories;

use Mockery\Exception;
use Modules\HRMS\app\Models\EducationalRecord;

class EducationalRecordRepository
{
//    protected EducationalRecord $educationalRecord;
//
//    /**
//     * @param EducationalRecord $educationalRecord
//     */
//    public function __construct(EducationalRecord $educationalRecord)
//    {
//        $this->educationalRecord = $educationalRecord;
//    }

    public function store(string $jsonData, int $workForceID)
    {


        $dataToInsert = $this->dataPreparation($jsonData, $workForceID);

        /**
         * @var EducationalRecord $educationalRecord
         */
        try {

            \DB::beginTransaction();
            $educationalRecord = EducationalRecord::insert($dataToInsert);
//            $educationalRecord = new EducationalRecord();
//
//            $educationalRecord->university_name = $data['universityName'];
//            $educationalRecord->field_of_study = $data['fieldOfStudy'];
//            $educationalRecord->start_date = $data['startDate'];
//            $educationalRecord->end_date = $data['endDate'] ?? null;
//            $educationalRecord->average = $data['average'] ?? null;
//            $educationalRecord->work_force_id = $data['workForceID'];
//            $educationalRecord->level_of_educational_id = $data['levelOfEducationalID'] ?? null;
//
//            $educationalRecord->save();

            \DB::commit();
            $records = EducationalRecord::orderBy('id', 'desc')->take(count($dataToInsert))->get();


            return $records;

        } catch (Exception $e) {

            \DB::rollBack();
            return $e;
        }

    }

    public function update(array $data, int $id)
    {

        try {

            \DB::beginTransaction();

            /**
             * @var EducationalRecord $educationalRecord
             */
            $educationalRecord = EducationalRecord::findOrFail($id);
            if (is_null($educationalRecord)) {
                return null;
            }
            $educationalRecord->university_name = $data['universityName'];
            $educationalRecord->field_of_study = $data['fieldOfStudy'];
            $educationalRecord->start_date = $data['startDate'];
            $educationalRecord->end_date = $data['endDate'] ?? null;
            $educationalRecord->average = $data['average'] ?? null;
            $educationalRecord->work_force_id = $data['workForceID'];
            $educationalRecord->level_of_educational_id = $data['levelOfEducationalID'] ?? null;

            $educationalRecord->save();

            \DB::commit();

            return $educationalRecord;

        } catch (Exception $e) {

            \DB::rollBack();
            return $e;
        }
    }

    private function dataPreparation(string $json, int $workForceID)
    {
        $educations = json_decode($json, true);

        $recordsToInsert = array_map(function ($data) use ($workForceID) {
            return [
                'university_name' => $data['universityName'],
                'field_of_study' => $data['fieldOfStudy'],
                'start_date' => $data['startDate'],
                'end_date' => $data['endDate'] ?? null,
                'average' => $data['average'] ?? null,
                'work_force_id' => $workForceID,
                'level_of_educational_id' => $data['levelOfEducationalID'] ?? null,
            ];
        }, $educations);

        return $recordsToInsert;
    }


}
