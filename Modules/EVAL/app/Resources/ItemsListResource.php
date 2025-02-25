<?php

namespace Modules\EVAL\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemsListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {

        return [
            'section' => [
                'sectionTitle' => $this['sectionTitle'],

                'indicator' => [
                    'indicatorsTitle' => $this->indicatorsTitle,

                    'coefficient' => [
                        'coefficient' => $this->coefficient,

                        'variable' => [
                            'variableName' => $this->variableName,

                            'weight' => [
                                'weight' => $this->weight,
                            ],
                        ],
                    ],
                ],
            ],

        ];

    }
}
