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
                'chapter' => [
                    'id' => $request->chapter_id,
                    'title' => $request->title,
                    'course_id' => $request->course_id,],
                ' lessons' => [
                    'id' => $request->id,
                    'title' => $request->title,
                    'description' => $request->description,
                ],
                'status' => [
                    'id' => $request->id,
                    'title' => $request->title,
                    'description' => $request->description,
                ],
                'question' => [
                    'id' => $request->id,
                    'title' => $request->title,
                    'description' => $request->description,
                ]

            ]];
    }
}
