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
            'id' => $request->id,
            'title' => $this->title,

            'lessons' => [
                'id' => $request->lessonID,
                'title' => $this->lesson_title
            ],
            'creator' => [
                'id' => $request->creatorID,
            ],
            'difficulty' => [
                'id' => $request->difficultyID,
                'name' => $this->difficulty_name
            ],
            'questionType' => [
                'id' => $request->questionTypeID,
                'name' => $this->question_type_name
            ],
            'repository' => [
                'id' => $request->repositoryID,
                'name' => $this->repository_name
            ],
            'status' => [
                'id' => $request->statusID,
                'name' => $this->status_name,
                'className' => $this->status_class_name
            ],


        ];


    }
}
