<?php

namespace Modules\PFM\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowCircularsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
