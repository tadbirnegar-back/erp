<?php

namespace Modules\EVAL\App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertiesAndvaluesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        // Check if the resource is a property
            return [
                'value' => $this->id,
                'label' => $this->name,
            ];

        // Check if the resource is a property value
//            return [
//                'value' => $this->id,
//                'label' => $this->value ? "بله" : "خیر", // Adjust this logic as needed
//            ];
//
//
//        // Default fallback (if neither model is matched)
//        return [
//            'value' => $this->id,
//            'label' => 'Unknown',
//        ];
    }
}
