<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LessonListResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
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
                        'slug' => $this->baseUrl . $item->cover_slug,
                    ] : null,
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
