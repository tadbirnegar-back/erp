<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LessonListResource extends ResourceCollection
{
    public string $baseUrl;

    /**
     * Transform the resource collection into an array.
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->baseUrl = url('/') . '/'; // Initialize base URL
    }

    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {

        return [
            'data' => $this->collection->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'cover' => $item->cover_slug ? [
                        'slug' => $this->baseUrl . $item->cover_slug,
                    ] : null,
                    'status' => [
                        'name' => $item->latestStatus->name,
                        'className' => $item->latestStatus->class_name,
                    ],
                    'counts' => [
                        'chapters' => $item->chapters_count ?? 0,
                        'lessons' => $item->lessons_count ?? 0,
                    ],
                ];
            }),
        ];
    }
}
