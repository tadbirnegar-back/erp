<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request,): array
    {
//        dd($this->resource);
        return [
            'id' => $this->id,
            'title' => $this->title,

            'lessons' => [
                'id' => $this->lesson_id,
                'title' => $this->lesson_title
            ],
            'creator' => [
                'id' => $this->creator_id,
            ],
            'difficulty' => [
                'id' => $this->difficulty_id,
            ],
            'question_type' => [
                'id' => $this->question_type_id,
                'name' => $this->question_type_name
            ],
            'repository' => [
                'id' => $this->repository_id,
                'name' => $this->repository_name
            ],
            'status' => [
                'id' => $this->status_id,
                'name' => $this->status_name,
                'className' => $this->status_class_name
            ],


        ];


    }
}
