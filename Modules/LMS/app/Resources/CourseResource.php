<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
        return [
            'data' => $this->collection->transform(function ($item) {
                return [
                    'course' => [
                        'id' => $item->person_id,
                        'title' => $item->display_name,
                        'cover' => [
                            'slug' => $this->baseUrl . $item->slug,

                        ],

                    ]
                ];
            })
        ];
    }
}





