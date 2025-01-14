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
            'StateOfc' => [
                ["value" => $stateValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::StateOfc->value) , "position" => $this->positionFilteredCatShow($stateValue)],
                ["value" => $cityValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::CityOfc->value) , "position" => $this->positionFilteredCatShow($cityValue)],
                ["value" => $districtValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::DistrictOfc->value) , "position" => $this->positionFilteredCatShow($districtValue)],
                ["value" => $villageValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value) , "position" => $this->positionFilteredCatShow($villageValue)],
                ["value"=> intval($stateValue.$cityValue.$districtValue.$villageValue), "label" => "همه دسته های سازمانی" , "position" => $this->positionFilteredCatForAllShow($stateValue.$cityValue.$districtValue.$villageValue)],
            ],
            'CityOfc' => [
                ["value" => $cityValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::CityOfc->value) , "position" => $this->positionFilteredCatShow($cityValue)],
                ["value" => $districtValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::DistrictOfc->value) , "position" => $this->positionFilteredCatShow($districtValue)],
                ["value" => $villageValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value) , "position" => $this->positionFilteredCatShow($villageValue)],
                ["value"=> intval($cityValue.$districtValue.$villageValue), "label" => "همه دسته های سازمانی" , "position" => $this->positionFilteredCatForAllShow($cityValue.$districtValue.$villageValue)]
            ],
            'DistrictOfc' => [
                ["value" => $districtValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::DistrictOfc->value) , "position" => $this->positionFilteredCatShow($districtValue)],
                ["value" => $villageValue, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value) , "position" => $this->positionFilteredCatShow($villageValue)],
                ["value"=> intval($districtValue.$villageValue), "label" => "همه دسته های سازمانی" , "position" => $this->positionFilteredCatForAllShow($districtValue.$villageValue)]
            ],
            'VillageOfc' => [
                ["value" => OunitCategoryEnum::VillageOfc->value, "label" => OunitCategoryEnum::getLabelById(OunitCategoryEnum::VillageOfc->value) , "position" => $this->positionFilteredCatShow($villageValue)],
            ]
        ];
    }

    private function formatChilds(string $type): array
    {
        $components = $this->components();

        return array_map(function ($component) {
            return [
                'value' => $component['value'],
                'label' => $component['label'],
                'position' => $component['position'] ?? null,
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
