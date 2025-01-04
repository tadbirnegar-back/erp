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
            'childs' => $this->formatChilds(class_basename($this->unitable_type))
        ];
    }

    private function components(): array
    {
        $stateValue = OunitCategoryEnum::StateOfc->value;
        $cityValue = OunitCategoryEnum::CityOfc->value;
        $districtValue = OunitCategoryEnum::DistrictOfc->value;
        $villageValue = OunitCategoryEnum::VillageOfc->value;

        return [
            'StateOfc' => [
                ["value" => $stateValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::StateOfc->value)],
                ["value" => $cityValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::CityOfc->value)],
                ["value" => $districtValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::DistrictOfc->value)],
                ["value" => $villageValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value)],
                ["value"=> intval($stateValue.$cityValue.$districtValue.$villageValue), "label" => "همه دسته های سازمانی"]
            ],
            'CityOfc' => [
                ["value" => $cityValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::CityOfc->value)],
                ["value" => $districtValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::DistrictOfc->value)],
                ["value" => $villageValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value)],
                ["value"=> intval($cityValue.$districtValue.$villageValue), "label" => "همه دسته های سازمانی"]
            ],
            'DistrictOfc' => [
                ["value" => $districtValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::DistrictOfc->value)],
                ["value" => $villageValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value)],
                ["value"=> intval($districtValue.$villageValue), "label" => "همه دسته های سازمانی"]
            ],
            'VillageOfc' => [
                ["value" => OunitCategoryEnum::VillageOfc->value, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value)],
            ]
        ];
    }

    private function formatChilds(string $type): array
    {
        $components = $this->components();

        return array_map(function ($component) {
            return [
                'value' => $component['value'],
                'label' => $component['label']
            ];
        }, $components[$type] ?? []);
    }
}
