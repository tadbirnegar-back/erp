<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\CourseRecord;

trait CourseRecordTrait
{

    public function courseRecordStore(array $data,int $workforceID): CourseRecord
    {
        $courseRecord = new CourseRecord();
        $courseRecord->title = $data['title'];
        $courseRecord->duration = $data['duration'];
        $courseRecord->location = $data['location'];
        $courseRecord->workforce_id = $workforceID;
        $courseRecord->start_date = $data['startDate'];
        $courseRecord->end_date = $data['endDate'];
        $courseRecord->save();

        return $courseRecord;
    }

    public function readCourseRecord(int $id): ?CourseRecord
    {
        return CourseRecord::find($id);
    }

    public function courseRecordUpdate(CourseRecord $courseRecord, array $data): ?CourseRecord
    {
            $courseRecord->title = $data['title'];
            $courseRecord->duration = $data['duration'];
            $courseRecord->location = $data['location'];
            $courseRecord->workforce_id = $data['workforceID'];
            $courseRecord->start_date = $data['startDate'];
            $courseRecord->end_date = $data['endDate'];
            $courseRecord->save();

        return $courseRecord;
    }

    public function deleteCourseRecord(int $id): bool
    {
        $courseRecord = CourseRecord::find($id);
        if ($courseRecord) {
            return $courseRecord->delete();
        }

        return false;
    }
}
