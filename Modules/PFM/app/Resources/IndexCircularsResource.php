<?php

namespace Modules\PFM\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IndexCircularsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->circular_name,
            'fiscal_year' => $this->fiscal_year_name,
            'status' => [
                'name' => $this->status_name,
                'class' => $this->status_class,
                'published_date' => $this->status_name == 'ابلاغ شده' ? $this->status_created_date : null,
            ]
        ];
    }
}
