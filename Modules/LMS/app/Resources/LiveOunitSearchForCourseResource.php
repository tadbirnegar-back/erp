<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;

class LiveOunitSearchForCourseResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => class_basename($this->unitable_type),
            'childs' => $this -> components()[class_basename($this->unitable_type)]
        ];
    }

    private function components()
    {
        return [
            'StateOfc' => [
                [OunitCategoryEnum::StateOfc->value, OunitCategoryEnum::getLabelById(OunitCategoryEnum::StateOfc->value)],
                [OunitCategoryEnum::CityOfc->value, OunitCategoryEnum::getLabelById(OunitCategoryEnum::CityOfc->value)],
                [OunitCategoryEnum::DistrictOfc->value, OunitCategoryEnum::getLabelById(OunitCategoryEnum::DistrictOfc->value)],
                [OunitCategoryEnum::VillageOfc->value, OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value)],
            ],
            'CityOfc' => [
                [OunitCategoryEnum::CityOfc->value, OunitCategoryEnum::getLabelById(OunitCategoryEnum::CityOfc->value)],
                [OunitCategoryEnum::DistrictOfc->value, OunitCategoryEnum::getLabelById(OunitCategoryEnum::DistrictOfc->value)],
                [OunitCategoryEnum::VillageOfc->value, OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value)],
            ],
            'DistrictOfc' => [
                [OunitCategoryEnum::DistrictOfc->value, OunitCategoryEnum::getLabelById(OunitCategoryEnum::DistrictOfc->value)],
                [OunitCategoryEnum::VillageOfc->value, OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value)],
            ],
            'VillageOfc' => [
                [OunitCategoryEnum::VillageOfc->value, OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value)],
            ]
        ];
    }
}
