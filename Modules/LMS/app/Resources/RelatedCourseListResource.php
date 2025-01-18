<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RelatedCourseListResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = collect($this->resource);

        $filteredData = $data->filter(function ($item) {
            return isset($item['course_id']);
        });

        // Group data by 'course_id'
        $groupedData = $filteredData->groupBy(function ($item) {
            return $item['course_id'];
        })->values()->map(function ($group) {
            $distinctContentTypes = $group->pluck('content_type_alias_name')->filter()->unique()->values();

            // Filter out null lesson_id values and count distinct lesson_ids
            $validLessonIds = $group->pluck('lesson_id')->filter()->unique();
            $distinctLessonIds = $validLessonIds->count();

            return [
                'course_id' => $group->first()['course_id'],
                'course_title' => $group->first()['course_title'],
                'course_exp_date' => $group->first()['course_exp_date'],
                'status_name' => $group->first()['status_name'],
                'lesson_count' => $distinctLessonIds,
                'distinct_content_types' => $distinctContentTypes,
                'village_degree' => $group->first()['village_degree'] ?? null,
                'village_tourism' => $group->first()['village_tourism'] ?? null,
                'village_farm' => $group->first()['village_farm'] ?? null,
                'village_attached_to_city' => $group->first()['village_attached_to_city'] ?? null,
                'village_license' => $group->first()['village_license'] ?? null,
                'prop_value' => $group->first()['prop_value'] ?? null,
                'column_name' => $group->first()['column_name'] ?? null,
            ];
        });

        return $groupedData->values()->toArray();
    }
}


