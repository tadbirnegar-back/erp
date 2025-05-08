<?php

namespace Modules\SMM\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Modules\SMM\app\Enums\CircularStatusEnum;

class SmmCircularShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'minWage' => $this->min_wage,
            'marriageBenefit' => $this->marriage_benefit,
            'childBenefit' => $this->child_benefit,
            'rentBenefit' => $this->rent_benefit,
            'groceryBenefit' => $this->grocery_benefit,
            'file' => [
                'title' => $this->file_name,
                'slug' => url('/') . '/' . $this->file_slug,
                'size' => Number::fileSize($this->file_size),
            ],
            'fiscalYear' => $this->fiscal_year->name,
            'status' => [
                'statusName' => $this->status_name,
                'statusClassName' => $this->status_class_name,
            ],
            'dispatchDate' => $this->status_name == CircularStatusEnum::DRAFT->value
                ? '-'
                : DateformatToHumanReadableJalali(convertGregorianToJalali($this->status_create_date), false),

        ];
    }
}
