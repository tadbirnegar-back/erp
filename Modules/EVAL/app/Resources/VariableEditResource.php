<?php

namespace Modules\EVAL\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\EVAL\app\Http\Traits\CircularTrait;

class VariableEditResource extends JsonResource
{
    use CircularTrait;

    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        // Assuming $variableID is passed through the resource constructor or request

        // Call the trait method to get the data

        return [
            'section' => [
                'sectionTitle' => $this['variable']['sectionTitle'] ?? null,
                'sectionID' => $this['variable']['sectionID'] ?? null,

            'indicators' => [
                'indicatorsTitle' => $this['variable']['indicatorsTitle'] ?? null,
                'indicatorsID' => $this['variable']['indicatorsID'] ?? null,
            ],
            ],
        ];
    }
}
