<?php

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
        return [
            'course' => [
                'id' => $request->person_id,
                'title' => $request->display_name,
                'cover' => [
                    'slug' => $this->baseUrl . $request->slug,

                ],

            ]
        ];
    }
}
