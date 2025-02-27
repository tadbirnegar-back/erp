<?php

namespace Modules\EVAL\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CircularFirstListResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection->transform(function ($item) {
                return [
                    'status' => $item->status,
                    'status_class' => $item->status_class,
                    'name' => $item->name,
                    'circularID' => $item->circularID,
                ];
            }),
        ];
    }
}
