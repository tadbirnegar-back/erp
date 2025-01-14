<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\HRMS\app\Http\Traits\PositionTrait;

class LiveOunitSearchForCourseResource extends JsonResource
{
    use PositionTrait;
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => class_basename($this->unitable_type),
            'childs' => $this->formatChilds(class_basename($this->unitable_type)),
            'ancestors' => $this->formatAncestors(),
            'abadi_code' => $this->unitable->abadi_code
        ];
    }


    private function components(): array
    {
        $stateValue = OunitCategoryEnum::StateOfc->value;
        $cityValue = OunitCategoryEnum::CityOfc->value;
        $districtValue = OunitCategoryEnum::DistrictOfc->value;
        $villageValue = OunitCategoryEnum::VillageOfc->value;

        return [
            'StateOfc' => $this->getComponentWithPositions($stateValue, [
                ["value" => $stateValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::StateOfc->value)],
                ["value" => $cityValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::CityOfc->value)],
                ["value" => $districtValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::DistrictOfc->value)],
                ["value" => $villageValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value)],
                ["value" => intval($stateValue . $cityValue . $districtValue . $villageValue), "label" => "همه دسته های سازمانی"]
            ]),
            'CityOfc' => $this->getComponentWithPositions($cityValue, [
                ["value" => $cityValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::CityOfc->value)],
                ["value" => $districtValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::DistrictOfc->value)],
                ["value" => $villageValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value)],
                ["value" => intval($cityValue . $districtValue . $villageValue), "label" => "همه دسته های سازمانی"]
            ]),
            'DistrictOfc' => $this->getComponentWithPositions($districtValue, [
                ["value" => $districtValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::DistrictOfc->value)],
                ["value" => $villageValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value)],
                ["value" => intval($districtValue . $villageValue), "label" => "همه دسته های سازمانی"]
            ]),
            'VillageOfc' => $this->getComponentWithPositions($villageValue, [
                ["value" => OunitCategoryEnum::VillageOfc->value, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value)],
            ])
        ];
    }

    private function getComponentWithPositions(int $catId, array $baseComponents): array
    {
        $positions = $this->positionFilteredCatShow($catId);

        return array_map(function ($component) use ($positions) {
            return [
                'value' => $component['value'],
                'label' => $component['label'],
                'positions' => $positions ? $positions->map(function ($position) {
                    return [
                        'id' => $position->id,
                        'name' => $position->name,
                        'levels' => $position->levels->map(function ($level) {
                            return [
                                'id' => $level->id,
                                'name' => $level->name,
                            ];
                        }),
                    ];
                })->toArray() : [], // Default to an empty array if no positions are found
            ];
        }, $baseComponents);
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

    private function formatAncestors(): array
    {
        return $this->ancestors->reverse()->map(function ($ancestor) {
            return [
                'id' => $ancestor->id,
                'name' => $ancestor->name,
                'type' => class_basename($ancestor->unitable_type),
            ];
        })->toArray();
    }

}
