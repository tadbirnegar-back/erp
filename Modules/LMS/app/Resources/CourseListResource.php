<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CourseListResource extends ResourceCollection
{
    protected string $baseUrl;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->baseUrl = url('/'); // Initialize base URL
    }

    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        //null check

        return [
            'data' => $this->collection->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,

                    'cover' => $item->cover_slug ? [
                        'slug' => url($item->cover_slug),
                    ] : null,
                    'statuses' => [
                        'name' => $item->latestStatus->name,
                        'className' => $item->latestStatus->class_name,
                    ],
                    'counts' => [
                        'chapters' => $item->chapters_count ?? 0,
                        'lessons' => $item->lessons_count ?? 0,
                        'questions' => $item->questions_count ?? 0,
                    ],

                ];
            }),
        ];
    }
}
