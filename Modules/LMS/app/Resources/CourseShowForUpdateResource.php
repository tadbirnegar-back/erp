<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\LMS\app\Http\Traits\CourseTargetTrait;
use Illuminate\Support\Number;

class CourseShowForUpdateResource extends JsonResource
{
    use CourseTargetTrait;

    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        $grouped = $this->resource->groupBy(function ($item) {
            return $item->course_alias_id;
        });

        return $grouped->map(function ($group) {
            $courseInfo = $group->first();
            $groupedTargets = $group->groupBy('course_target_id');

            $courseTargets = $groupedTargets->map(function ($groupedTarget) {
                $categories = [];
                $mappedTargets = $groupedTarget->groupBy(function ($targetInfo) use (&$categories) {
                    $key = $targetInfo->course_target_id . '_' . $targetInfo->ounit_category_id;
                    if (preg_match('/_(\d+)/', $key, $matches)) {
                        $categories[] = (int)$matches[1];
                    }
                    return $key;
                })->map(function ($nestedGroup) {
                    // Merge targets based on the rules
                    $mergedTarget = [
                        'id' => null,
                        'ounit_name' => null,
                        'level_info' => null,
                        'job_info' => null,
                        'position_info' => null,
                        'isForAllEmployees' => true, // Default to true
                        'property_info' => [
                            'name' => null,
                            'id' => null,
                        ],
                        'value_info' => [
                            'value' => null,
                            'operator' => null,
                        ]
                    ];
                    foreach ($nestedGroup as $targetInfo) {
                        // Update values if they are not null
                        $mergedTarget['id'] = $targetInfo->course_target_id ?? $mergedTarget['id'];
                        $mergedTarget['ounit_name'] = $targetInfo->ounit_alias_name ?? $mergedTarget['ounit_name'];
                        $mergedTarget['level_info'] = $targetInfo->level_alias_name ?? $mergedTarget['level_info'];
                        $mergedTarget['job_info'] = $targetInfo->job_alias_title ?? $mergedTarget['job_info'];
                        $mergedTarget['position_info'] = $targetInfo->position_alias_name ?? $mergedTarget['position_info'];
                        $mergedTarget['isForAllEmployees'] = (string)(int)($mergedTarget['isForAllEmployees'] && $targetInfo->isForAllEmployees);
                        $mergedTarget['property_info']['name'] = $targetInfo->oucProperty_name ?? $mergedTarget['property_info']['name'];
                        $mergedTarget['property_info']['id'] = $targetInfo->oucProperty_id ?? $mergedTarget['property_info']['id'];
                        $mergedTarget['value_info']['value'] = $targetInfo->value_alias_value ?? $mergedTarget['value_info']['value'];
                        $mergedTarget['value_info']['operator'] = $targetInfo->value_alias_operator ?? $mergedTarget['value_info']['operator'];
                    }

                    return $mergedTarget;
                });

                $uniqueTargets = $mappedTargets->unique(function ($item) {
                    return json_encode($item); // Ensure uniqueness by comparing the serialized target
                });

                $ounits = $this->getCourseTargetOunit($groupedTarget->first()->course_target_id);
                $uniqueCategories = array_values(array_unique($ounits));
                $isForAllCats = $this->isForAllCats($uniqueCategories);

                return [
                    'categories' => $isForAllCats
                        ? ['همه دسته های سازمانی']
                        : array_map([$this, 'exactCat'], $uniqueCategories),
                    'targets' => $uniqueTargets->values(),
                ];
            });

            $preReqs = $group->map(function ($item) {
                return [
                    'id' => $item->pre_reg_alias_id,
                    'title' => $item->pre_reg_alias_title,
                ];
            })->unique('id')->values();
            $sizeWithUnitVideo = Number::fileSize($courseInfo->course_video_size, 2, 3);
            $partsvideo = explode(' ', $sizeWithUnitVideo, 2);

            $sizeWithCover = Number::fileSize($courseInfo->course_cover_size, 2, 3);
            $partscover = explode(' ', $sizeWithCover, 2);
            return [
                'course_info' => [
                    'id' => $courseInfo->course_alias_id,
                    'title' => $courseInfo->course_alias_title,
                    'description' => $courseInfo->course_alias_description,
                    'is_required' => $courseInfo->course_alias_is_required,
                    'expiration_date' => convertDateTimeGregorianToJalaliDateTime($courseInfo->course_alias_expiration_date),
                    'access_date' => convertDateTimeGregorianToJalaliDateTime($courseInfo->course_alias_access_date),
                    'privacy_id' => $courseInfo->course_alias_privacy_id,
                ],
                'video' => [
                    'slug' => $courseInfo->course_video_slug,
                    'title' => $courseInfo->course_video_title,
                    'id' => $courseInfo->course_video_id,
                    'size' => intval(Number::fileSize($courseInfo->course_video_size, 2, 3)).' '.$partsvideo[1],
                ],
                'cover' => [
                    'slug' => $courseInfo->course_cover_slug,
                    'title' => $courseInfo->course_cover_title,
                    'id' => $courseInfo->course_cover_id,
                    'size' => intval(Number::fileSize($courseInfo->course_cover_size, 2, 3)).' '.$partscover[1],
                ],
                'pre_req' => $preReqs,
                'course_targets' => $courseTargets->values(), // convert to array of objects
            ];
        });
    }
    private function isForAllCats($ids)
    {
        return count($ids) > 1;
    }

    private function exactCat($id)
    {
        return OunitCategoryEnum::getLabelById($id);
    }
}
