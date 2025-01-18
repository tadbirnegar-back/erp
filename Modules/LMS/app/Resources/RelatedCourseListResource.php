<?php

namespace Modules\LMS\app\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RelatedCourseListResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = collect($this->resource->items()); // Access the items in the paginator

        // Filter data where course_id exists
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

            // Extract the first record to access common properties
            $firstRecord = $group->first();

            // Dynamically determine the `village` property based on column_name
            $columnName = $firstRecord['column_name'] ?? null;
            $propValue = $firstRecord['prop_value'] ?? null;

            if ($columnName && $propValue) {
                // Map column names to their corresponding village properties
                $villagePropertyMap = [
                    'degree' => 'village_degree',
                    'isTourism' => 'village_tourism',
                    'isFarm' => 'village_farm',
                    'isAttached_to_city' => 'village_attached_to_city',
                    'hasLicense' => 'village_license',
                ];

                $villageProperty = $villagePropertyMap[$columnName] ?? null;

                if ($villageProperty) {
                    // Filter group based on the village property and prop_value
                    $group = $group->filter(function ($item) use ($villageProperty, $propValue) {
                        return isset($item[$villageProperty]) && $item[$villageProperty] == $propValue;
                    });
                }
            }

            // Return null if the group is empty after filtering
            if ($group->isEmpty()) {
                return null;
            }

            return [
                'course_id' => $firstRecord['course_id'],
                'course_title' => $firstRecord['course_title'],
                'course_exp_date' => $firstRecord['course_exp_date'] ?  Carbon::parse($firstRecord['course_exp_date'])->diffInDays(Carbon::now()) : null,
                'status_name' => $firstRecord['status_name'],
                'class_name' => $firstRecord['class_name'],
                'cover_slug' => url($firstRecord['cover_slug']),
                'lesson_count' => $distinctLessonIds,
                'distinct_content_types' => $distinctContentTypes,
            ];
        });

        // Remove null entries caused by empty groups
        $groupedData = $groupedData->filter()->values();

        // Pagination metadata
        $pagination = $this->resource->toArray($request);

        return [
            'data' => $groupedData,
            'pagination' => $pagination['links'],
        ];
    }
}
