<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\CourseCourse;

trait CourseCourseTrait
{
    public function storePreRequisite($course_id, $preRequisiteCourseIDs)
    {
        $preparedData = $this->prepareData($course_id, $preRequisiteCourseIDs);
        CourseCourse::insert($preparedData);
    }

    private function prepareData($course_id, $preRequisiteCourseIDs)
    {
        $preIds = json_decode($preRequisiteCourseIDs, true);
        $data = [];

        foreach ($preIds as $preId) {
            $data[] = [
                'main_course_id' => $course_id,
                'prerequisite_course_id' => $preId,
            ];
        }

        return $data;
    }
}
