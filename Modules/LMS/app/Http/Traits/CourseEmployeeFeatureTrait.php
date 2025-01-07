<?php

namespace Modules\LMS\app\Http\Traits;

use Modules\LMS\app\Models\CourseEmployeeFeature;

trait CourseEmployeeFeatureTrait
{
    public function deleteCourseEmpFeature($ids)
    {
        CourseEmployeeFeature::whereIn('course_target_id' , $ids)->delete();
    }
}
