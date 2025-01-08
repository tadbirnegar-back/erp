<?php

namespace Modules\LMS\app\Http\Traits;

use Illuminate\Support\Facades\Log;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Level;
use Modules\HRMS\app\Models\Position;
use Modules\LMS\app\Models\CourseEmployeeFeature;
use Modules\LMS\app\Models\CourseOunitFeature;
use Modules\LMS\app\Models\CourseTarget;
use Modules\LMS\app\Models\TargetOunitCat;

trait CourseTargetTrait
{
    public function storeCourseTarget($courseId, $data)
    {
        $targets = json_decode($data);
        foreach ($targets as $target) {
            $courseTargetRecord = CourseTarget::create([
                'course_id' => $courseId,
                'parent_ounit_id' => $target->ounit_id,
            ]);
            //Store Course Feature
            if (isset($target->ounitCatIds) && !empty($target->ounitCatIds)) {
                foreach ($target->ounitCatIds as $catId) {
                    TargetOunitCat::create([
                        'course_target_id' => $courseTargetRecord->id,
                        'ounit_cat_id' => $catId,
                    ]);
                }
            }
            if (isset($target->oucPropertyValueID) && !is_null($target->oucPropertyValueID)) {
                CourseOunitFeature::create([
                    'course_target_id' => $courseTargetRecord->id,
                    'ouc_property_value' => $target->oucPropertyValueID
                ]);
            }
            //Store Employee Feature
            if (isset($target->features) && !is_null($target->features)) {
                if (isset($target->features->jobs) && !is_null($target->features->jobs)) {
                    foreach ($target->features->jobs as $job) {
                        CourseEmployeeFeature::create([
                            'course_target_id' => $courseTargetRecord->id,
                            'propertyble_type' => Job::class,
                            'propertyble_id' => $job,
                        ]);
                    }
                }
                if (isset($target->features->levels) && !is_null($target->features->levels)) {
                    foreach ($target->features->levels as $level) {
                        CourseEmployeeFeature::create([
                            'course_target_id' => $courseTargetRecord->id,
                            'propertyble_type' => Level::class,
                            'propertyble_id' => $level,
                        ]);
                    }
                }
                if (isset($target->features->positions)) {
                    if (!empty($target->features->positions)) {
                        foreach ($target->features->positions as $position) {
                            CourseEmployeeFeature::create([
                                'course_target_id' => $courseTargetRecord->id,
                                'propertyble_type' => Position::class,
                                'propertyble_id' => $position,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function deleteCourseTarget($ids)
    {
        CourseTarget::destroy($ids);
    }


    public function getCourseTargetOunit($id)
    {
        $ouctarget = TargetOunitCat::where('course_target_id', $id)->get();
        $ids = $ouctarget->pluck('ounit_cat_id')->toArray();
        return $ids;
    }

}
