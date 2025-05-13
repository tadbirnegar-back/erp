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
                'name'=>$this->resource['file_name'],
                'id' => $this->resource['file_id'],
                "slug" => $this->resource['file_slug'],
                "size" => Number::fileSize($this->resource['file_size'], 2, 3),
            ],
            'fiscal_year' => [
                'id' => $this->resource['fiscal_year_id'],
                'name' => $this->resource['fiscal_year_name'],
                'start_date' =>  convertDateTimeGregorianToJalaliDateTimeOnlyDatePart($this->resource['start_date']),
                'end_date' =>convertDateTimeGregorianToJalaliDateTimeOnlyDatePart($this->resource['end_date']),
            ],
        ];
    }
}
