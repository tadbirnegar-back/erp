<?php

namespace Modules\PFM\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\PFM\app\Http\Enums\BookletStatusEnum;

class ListOfBookletsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'booklet_name' => "عوارض نامه دهیاری $this->ounit_name",
            'status' => [
                'name' => $this->status_name,
                'className' => $this->status_class,
                'confirmedDate' => $this->status_name == BookletStatusEnum::MOSAVAB->value ? $this->status_created_date : null,
            ]
        ];
    }
}
