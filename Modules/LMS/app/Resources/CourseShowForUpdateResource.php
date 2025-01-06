<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;

class CourseShowForUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        // Group by course_alias_id to organize the data by course
        $grouped = $this->resource->groupBy(function ($item) {
            return $item->course_alias_id;
        });

        return $grouped->map(function ($group) {
            $courseInfo = $group->first();
            $groupedTargets = $group->groupBy('course_target_id');

            $courseTargets = $groupedTargets->map(function ($groupedTarget) {
                return $groupedTarget->groupBy(function ($targetInfo) {
                    return $targetInfo->course_target_id . '_' . $targetInfo->ounit_feature_alias_id;
                })->map(function ($nestedGroup) {
                    return $nestedGroup->map(function ($targetInfo) {
                        $isForAllEmployees = is_null($targetInfo->job_alias_title) &&
                            is_null($targetInfo->level_alias_name) &&
                            is_null($targetInfo->position_alias_name);
                        return [
                            'id' => $targetInfo->course_target_id,
                            'ounit_name' => $targetInfo->ounit_alias_name ?? null,
                            'ounit_category' => OunitCategoryEnum::getLabelById($targetInfo->ounit_category_id),
                            'level_info' => $targetInfo->level_alias_name ?? null,
                            'job_info' => $targetInfo->job_alias_title ?? null,
                            'position_info' => $targetInfo->position_alias_name ?? null,
                            'isForAllEmployees' => $isForAllEmployees,
                            'property_info' => [
                                'name' => $targetInfo->oucProperty_name ?? null,
                                'id' => $targetInfo->oucProperty_id ?? null,
                            ],
                            'value_info' => [
                                'value' => $targetInfo->value_alias_value ?? null,
                                'operator' => $targetInfo->value_alias_operator ?? null,
                            ],
                            'ounit_feature_alias_id' => $targetInfo->ounit_feature_alias_id ?? null,

                        ];
                    })->unique(function ($item) {
                        return $item['ounit_name'] . $item['level_info'] . $item['position_info'];
                    });
                });
            });

            $preReqs = $group->map(function ($item) {
                return [
                    'id' => $item->pre_reg_alias_id,
                    'title' => $item->pre_reg_alias_title,
                ];
            })->unique('id');

            return [
                'course_info' => [
                    'id' => $courseInfo->course_alias_id,
                    'title' => $courseInfo->course_alias_title,
                    'description' => $courseInfo->course_alias_description,
                    'is_required' => $courseInfo->course_alias_is_required,
                    'expiration_date' => $courseInfo->course_alias_expiration_date,
                    'access_date' => $courseInfo->course_alias_access_date,
                    'privacy_id' => $courseInfo->course_alias_privacy_id,
                ],
                'video' => [
                    'slug' => $courseInfo->course_video_slug,
                    'title' => $courseInfo->course_video_title,
                    'id' => $courseInfo->course_video_id,
                ],
                'cover' => [
                    'slug' => $courseInfo->course_cover_slug,
                    'title' => $courseInfo->course_cover_title,
                    'id' => $courseInfo->course_cover_id,
                ],
                'pre_req' => $preReqs,  // Return all prerequisites as an array
                'course_targets' => $courseTargets->unique(), // Filter out duplicate groups
            ];
        });
    }
}
