<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\LMS\app\Http\Enums\CourseStatusEnum;
use Modules\LMS\app\Http\Enums\LessonStatusEnum;
use Modules\LMS\app\Http\Traits\CourseTargetTrait;
use Illuminate\Support\Number;
use Modules\LMS\app\Http\Traits\LessonTrait;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Lesson;

class PublishCoursePreviewResource extends JsonResource
{
    use CourseTargetTrait , LessonTrait;

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
                    $mergedTarget = [
                        'id' => null,
                        'ounit_name' => null,
                        'level_info' => [],
                        'job_info' => [],
                        'position_info' => [],
                        'isForAllEmployees' => true,
                        'property_info' => [
                            'name' => null,
                            'id' => null,
                        ],
                        'value_info' => [
                            'value' => null,
                            'operator' => null,
                        ],
                    ];

                    foreach ($nestedGroup as $targetInfo) {
                        // Update values
                        $mergedTarget['id'] = $targetInfo->course_target_id ?? $mergedTarget['id'];
                        $mergedTarget['ounit_name'] = $targetInfo->ounit_alias_name ?? $mergedTarget['ounit_name'];

                        // Append to arrays
                        $mergedTarget['level_info'] = $this->concatValues($mergedTarget['level_info'], $targetInfo->level_alias_name);
                        $mergedTarget['job_info'] = $this->concatValues($mergedTarget['job_info'], $targetInfo->job_alias_title);
                        $mergedTarget['position_info'] = $this->concatValues($mergedTarget['position_info'], $targetInfo->position_alias_name);

                        $mergedTarget['isForAllEmployees'] = (string)(int)($mergedTarget['isForAllEmployees'] && $targetInfo->isForAllEmployees);
                        $mergedTarget['property_info']['name'] = $targetInfo->oucProperty_name ?? $mergedTarget['property_info']['name'];
                        $mergedTarget['value_info']['value'] = $targetInfo->value_alias_value ?? $mergedTarget['value_info']['value'];

                        if ($mergedTarget['property_info']['name'] != 'درجه') {
                            $mergedTarget['value_info']['value'] = $targetInfo->value_alias_value !== null
                                ? ($targetInfo->value_alias_value == 1 ? 'بله' : 'خیر')
                                : $mergedTarget['value_info']['value'];
                        }                        $mergedTarget['property_info']['id'] = $targetInfo->oucProperty_id ?? $mergedTarget['property_info']['id'];
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
            })->filter(function ($item) {
                return !is_null($item['id']);
            })->unique('id')->values();

            if($courseInfo->course_video_size != null){
                $sizeWithUnitVideo = Number::fileSize($courseInfo->course_video_size, 2, 3);
                $partsvideo = explode(' ', $sizeWithUnitVideo, 2);
            }


            $sizeWithCover = Number::fileSize($courseInfo->course_cover_size, 2, 3);
            $partscover = explode(' ', $sizeWithCover, 2);

            $chapters = $group->groupBy('chapters_alias_id')->map(function ($chapterGroup, $chapterId) {

                $chapterName = $chapterGroup->first()->chapters_alias_title;
                $chapterReadOnly = $chapterGroup->first()->chapters_alias_read_only ? true : false;
                $lessons = $chapterGroup->map(function ($lesson) {
                    return [
                        'lesson_id' => $lesson->lessons_alias_id,
                        'lesson_title' => $lesson->lessons_alias_title,
                    ];
                })->unique()->values()->toArray();

                return [
                    'chapter_id' => $chapterId,
                    'chapter_name' => $chapterName,
                    'lessons' => $lessons,
                    'readOnly' => $chapterReadOnly,
                    'warningToDelete' => $chapterName !== 'بدون فصل' && count($lessons) > 0,
                ];
            });

            $courseStatus =  $this -> courseStatushandle($courseInfo->course_alias_id);
            $name = $courseStatus->latestStatus->name;
            $class_name = $courseStatus->latestStatus->class_name;

            return [
                'course_info' => [
                    'id' => $courseInfo->course_alias_id,
                    'title' => $courseInfo->course_alias_title,
                    'description' => $courseInfo->course_alias_description,
                    'is_required' => ["name" => $courseInfo->course_alias_is_required ? 'اجباری' : 'اختیاری' , "class_name" => 'primary'],
                    'expiration_date' => convertDateTimeGregorianToJalaliDateTime($courseInfo->course_alias_expiration_date),
                    'access_date' => convertDateTimeGregorianToJalaliDateTime($courseInfo->course_alias_access_date),
                    'privacy' => [ 'id' => $courseInfo->privacy_alias_id , 'name' => $courseInfo->privacy_alias_name  , 'class_name' => 'primary'],
                    'price' => $courseInfo->course_alias_price
                ],
                'cover' => [
                    'slug' => url($courseInfo->course_cover_slug),
                    'title' => $courseInfo->course_cover_title,
                    'id' => $courseInfo->course_cover_id,
                    'size' => intval(Number::fileSize($courseInfo->course_cover_size, 2, 3)) . ' ' . $partscover[1],
                ],
                'video' => [
                    'slug' => $courseInfo->course_video_slug == null ? null : url($courseInfo->course_video_slug),
                    'title' => $courseInfo->course_video_title == null ? null : $courseInfo->course_video_title,
                    'id' => $courseInfo->course_video_id == null ? null : $courseInfo->course_video_id,
//                    'size' => $courseInfo == null ? null : intval(Number::fileSize($courseInfo->course_video_size, 2, 3)) . ' ' . $partsvideo[1],
                ],
                'pre_req' => $preReqs,
                'course_targets' => $courseTargets->values(),
                'chapters' => $chapters->first()['chapter_id'] == '' ? [] : $chapters->values(),
                'status' => ["name" => $name ,  "class_name" => $class_name],
                'buttons' => $this->ButtonsToRender()[$name],
            ];
        })->first();
    }
    private function isForAllCats($ids)
    {
        return count($ids) > 1;
    }

    private function exactCat($id)
    {
        return OunitCategoryEnum::getLabelById($id);
    }
    /**
     * Concatenate values if both are not null.
     */
    private function concatValues($existingValue, $newValue)
    {
        // Ensure both are arrays
        $existingValue = is_array($existingValue) ? $existingValue : ($existingValue ? [$existingValue] : []);
        $newValue = $newValue ? [$newValue] : [];

        // Merge and remove duplicates
        return array_values(array_unique(array_merge($existingValue, $newValue)));
    }


    private function ButtonsToRender()
    {
        return [
            CourseStatusEnum::PISHNEVIS->value => [
                'PublishBtn' ,
                'CourseOperationBtn'
            ],
            CourseStatusEnum::WAITING_TO_PRESENT->value => [
                'EditBtn' ,
                'CancelBtn'
            ],
            CourseStatusEnum::PRESENTING->value => [
                'CancelBtn' ,
            ],
            CourseStatusEnum::ENDED->value => ['noBtn'],
            CourseStatusEnum::DELETED->value => ['noBtn'],
            CourseStatusEnum::CANCELED->value => ['noBtn'],
        ];
    }

    private function courseStatushandle($course_id)
    {
        return Course::with('latestStatus')->find($course_id);
    }

}
