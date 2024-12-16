<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TeacherListResource extends ResourceCollection
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
            'id' => $request->id,
            'workforceable_type' => $request->workforceable_type,
            'workforceable_id' => $request->workforceable_id,
            'isMarried' => $request->isMarried,
            'person' => [
                'id' => $request->person_id,
                'display_name' => $request->display_name,
                'avatar' => [
                    'slug' => $this->baseUrl . $request->slug,
                    'size' => $request->size,

                ],
            ],
        ];
    }
}
