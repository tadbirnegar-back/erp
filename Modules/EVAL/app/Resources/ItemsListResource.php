<?php

namespace Modules\EVAL\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemsListResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {

        $grouped = $this->collection->groupBy('sectionTitle');

        $data = $grouped->map(function ($items, $sectionTitle) {
            return [
                'sectionTitle' => $sectionTitle,
                'indicators' => $items->groupBy('indicatorsTitle')->map(function ($indicators, $indicatorsTitle) {
                    return [
                        'indicatorsTitle' => $indicatorsTitle,
                        'variables' => $indicators->map(function ($item) {
                            return [
                                'variableName' => $item->variableName,
                                'coefficient' => $item->coefficient,
                                'weight' => $item->weight,
                            ];
                        })->values(),
                    ];
                }),
            ];
        });

        // Return the data as an object instead of an array
        return [
            'data' => (object) $data->toArray(),
        ];
    }
}
