<?php

namespace Modules\HRMS\app\Http\Repositories;

use Mockery\Exception;
use Modules\HRMS\app\Models\EducationalRecord;

class EducationalRecordRepository
{
    protected EducationalRecord $educationalRecord;

    /**
     * @param EducationalRecord $educationalRecord
     */
    public function __construct(EducationalRecord $educationalRecord)
    {
        $this->educationalRecord = $educationalRecord;
    }

    public function store(array $data)
    {
        /**
         * @var EducationalRecord $educationalRecord
         */
        try {

            \DB::beginTransaction();

            $educationalRecord = new $this->educationalRecord();

            $educationalRecord->university_name = $data['UniversityName'];
            $educationalRecord->field_of_study = $data['FieldOfStudy'];
            $educationalRecord->start_date = $data['StartDate'];
            $educationalRecord->end_date = $data['EndDate'] ?? null;
            $educationalRecord->average = $data['Average'] ?? null;
            $educationalRecord->work_force_id = $data['WorkForceID'];
            $educationalRecord->level_of_educational_id = $data['LevelOfEducationalID'] ?? null;

            $educationalRecord->save();

            \DB::commit();

            return $educationalRecord;

        } catch (Exception $e) {

            \DB::rollBack();
            return $e;
        }

    }

}
