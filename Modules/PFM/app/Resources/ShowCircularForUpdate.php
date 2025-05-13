<?php

namespace Modules\PFM\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class ShowCircularForUpdate extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->resource['id'],
            'name' => $this->resource['circular_name'],
            'description' => $this->resource['circular_description'],
            'files' => [
                'id' => $this->resource['file_id'],
                "slug" => $this->resource['file_slug'],
                "size" => Number::fileSize($this->resource['file_size'], 2, 3),
            ],
            'fiscal_year' => [
                'name' => $this->resource['fiscal_year_name'],
                'start_date' => convertDateTimeGregorianToJalaliDateTime($this->resource['start_date']),
                'end_date' => convertDateTimeGregorianToJalaliDateTime($this->resource['end_date']),
            ],
        ];
    }
}
