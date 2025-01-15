<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RelatedCourseListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $data = $this -> resource;

            return [
                'course_id' => $data -> course_id,
                'course_title' =>  $data -> course_title,
                'course_exp_date' => $data -> course_exp_date,
                'status_name' => $data -> status_name,
                'lesson_id' => $data -> lesson_id,
                'content_type_alias_name' => $data -> content_type_alias_name,
                'target_id' => $data -> target_id,
                'village_degree' => $data -> village_degree,
                'village_tourism' => $data -> village_tourism,
                'village_farm' => $data -> village_farm,
                'village_attached_to_city' => $data -> village_attached_to_city,
                'village_license' => $data -> village_license,
                'prop_value' => $data -> prop_value,

            ];

    }
}
