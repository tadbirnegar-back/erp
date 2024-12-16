<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TeacherListResource extends ResourceCollection
{
    protected string $baseUrl;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->baseUrl = url('/') . '/'; // Initialize base URL
    }

    /**
     * Transform the resource collection into an array.
     */
//    public function toArray($request): array
//    {
//        return [
//            'id' => $request->id,
//            'workforceable_type' => $request->workforceable_type,
//            'workforceable_id' => $request->workforceable_id,
//            'isMarried' => $request->isMarried,
//            'person' => [
//                'id' => $request->person_id,
//                'display_name' => $request->display_name,
//                'avatar' => [
//                    'slug' => $this->baseUrl . $request->slug,
//                    'size' => $request->size,
//
//                ],
//            ],
//        ];
//    }
//}

    public function toArray($request): array
    {
        return [
            'data' => $this->collection->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'workforceable_type' => $item->workforceable_type,
                    'workforceable_id' => $item->workforceable_id,
                    'isMarried' => $item->isMarried,
                    'person' => [
                        'id' => $item->person_id,
                        'display_name' => $item->display_name,
                        'avatar' => $item->slug ? [
                            'slug' => $this->baseUrl . $item->slug,
                            'size' => $item->size,
                        ] : null,
                    ],
                ];
            }),
//            'pagination' => [
//                'total' => $this->resource->total(),
//                'per_page' => $this->resource->perPage(),
//                'current_page' => $this->resource->currentPage(),
//                'last_page' => $this->resource->lastPage(),
//            ],
        ];
    }
}

