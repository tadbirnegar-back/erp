<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CourseListResource extends ResourceCollection
{
    protected string $baseUrl;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->baseUrl = url('/');
    }


    public function toArray($request): array
    {
        return [
            'data' => $this->collection->transform(function ($item) {
                $distinctChaptersCount = $item->chapters->filter()->unique()->count();
                $distinctLessonsCount = $item->chapters->flatMap->lessons->filter()->unique()->count();
                $distinctQuestionsCount = $item->chapters->flatMap->lessons->flatMap->questions
                    ->filter(function ($question) {
                        return $question->status->name == 'فعال';
                    })->filter()->unique()->count();


                $statuses = $item->statusCourse->map(function ($statusCourse) {
                    return [
                        'name' => $statusCourse->status->name ?? null,
                        'className' => $statusCourse->status->class_name ?? null,
                    ];
                })->filter()->values()->toArray();

                return [
                    'id' => $item['course_id'],
                    'title' => $item['title'],
                    'cover' => $item['cover_slug'] ? [
                        'slug' => url($item['cover_slug']),
                    ] : null,
                    'statuses' => $statuses,
                    'counts' => [
                        'questions' => $distinctQuestionsCount,
                        'chapters' => $distinctChaptersCount,
                        'lessons' => $distinctLessonsCount,
                    ],
                ];
            })->unique()->all(),
        ];
    }
}
