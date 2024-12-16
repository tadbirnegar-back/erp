<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CourseResource extends ResourceCollection
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
        if ($this->collection->isEmpty()) {
            return [
                'data' => [],

            ];
        }

        // در صورت پر بودن مجموعه
        return [
            'data' => $this->collection->transform(function ($item) {
                return [
                    'course' => [
                        'id' => $item->id,
                        'title' => $item->title,
                        'cover' => [
                            'slug' => $this->baseUrl . $item->slug,
                        ],
                    ],
                ];
            }),
        ];
    }
}
