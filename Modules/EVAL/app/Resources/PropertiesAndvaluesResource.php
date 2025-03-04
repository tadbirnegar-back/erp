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
        // Default response structure
        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'values' => $this->formatValues($this->values->toArray(), $this->name), // Pass property name to formatValues
        ];

        // Check if the name is "جمعیت"
        if ($this->name === 'جمعیت') {
            foreach ($this->values as $value) {
                // Check for the ">" operator and value > 1000
                if ($value['operator'] === '>' && (int)$value['value'] > 1000) {
                    $response['evaluation'] = 'بیشتر از هزار نفر';
                    break; // Exit the loop once the condition is met
                } else {
                    $response['evaluation'] = 'کم از هزار نفر';
                }
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
            // For "درجه", do not set the "name" field to "بله"
            if ($propertyName === 'درجه') {
                return [
                    'id' => $value['id'],
                    'name' => $value['value']
                    // Do not include the "name" field for "درجه"
                ];
            }

            // For other properties, set the "name" field based on the value
            return [
                'id' => $value['id'],
                'name' => $value['value'] ? "بله" : "خیر",
            ];
        }, $values);
    }
}
