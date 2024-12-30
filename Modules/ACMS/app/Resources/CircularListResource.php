<?php

namespace Modules\ACMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CircularListResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {

        return [
            [
                'id' => $this->circular_id,
                'name' => $this->circular_name,
                'status' => [
                    'name' => $this->status_name,
                    'class_name' => $this->status_class_name
                ]
            ]

        ];
    }
}
