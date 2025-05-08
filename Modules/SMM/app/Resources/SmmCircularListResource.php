<?php

namespace Modules\SMM\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\SMM\app\Enums\CircularStatusEnum;

class SmmCircularListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => [
                'statusName' => $this->status_name,
                'statusClassName' => $this->status_class_name,
            ],
            'dispatchDate' => $this->status_name == CircularStatusEnum::DRAFT->value ? '-' : DateformatToHumanReadableJalali(convertGregorianToJalali($this->status_create_date), false),
            'fiscalYearName' => $this->fiscal_year_name,
        ];
    }
}
