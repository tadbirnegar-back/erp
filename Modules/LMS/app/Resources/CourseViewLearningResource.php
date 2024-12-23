<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseViewLearningResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'chapter' => [
                'id' => $this->chapter_id,
                'title' => $this->chapter_title,
                'description' => $this->chapter_description,
                'lessons' => [
                    'id' => $this -> lesson_id ,
                    'title' => $this -> lesson_title ,
                    'description' => $this -> lesson_description,
                    'contents' => [
                        'id' => $this->content_id,
                        'title' => $this->content_title,
                        'type' => $this->content_type_name,
                        'file_url' => url($this->files_slug),
                        'teacher' => $this->teacher_name,
                    ],
                    'comments' => [
                        'text' => $this->comment_text,
                        'created_at' => $this->comment_created_at,
                        'commented_person' => $this->commented_person_name,
                    ]
                ]
            ],

        ];
    }
}
