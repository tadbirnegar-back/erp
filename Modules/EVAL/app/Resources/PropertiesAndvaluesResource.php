<?php

namespace Modules\EVAL\App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertiesAndvaluesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'values' => $this->formatValues($this->values->toArray(), $this->name),
        ];

        if ($this->name === 'جمعیت') {
            $populationFlag = true;

            foreach ($response['values'] as &$value) {
                if ($populationFlag) {
                    $value['population'] = 'بیشتر از هزار نفر';
                } else {
                    $value['population'] = 'کمتر از هزار نفر';
                }

                $value['name'] = $value['population'];

                $populationFlag = !$populationFlag;
            }
        }

        return $response;
    }



    /**
     * Format the values array to remove unwanted fields.
     *
     * @param array $values The values array to format.
     * @param string $propertyName The name of the property.
     * @return array The formatted values array.
     */
    private function formatValues(array $values, string $propertyName): array
    {
        return array_map(function ($value) use ($propertyName) {
            if ($propertyName === 'درجه') {
                return [
                    'id' => $value['id'],
                    'name' => $value['value']
                ];
            }

            return [
                'id' => $value['id'],
                'name' => $value['value'] ? "بله" : "خیر",
            ];
        }, $values);
    }
}
