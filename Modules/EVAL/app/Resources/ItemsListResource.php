<?php

namespace Modules\EVAL\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemsListResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return object
     */
    public function toArray($request): object
    {
        $firstItem = $this->collection->first();
        $name = $firstItem->name ?? null;

        $grouped = $this->collection->groupBy('sectionTitle');

        $sections = $grouped->map(function ($items, $sectionTitle) {
            $firstSection = $items->first();

            return [
                'sectionTitle' => $sectionTitle,
                'sectionID' => $firstSection->sectionID,
                'indicators' => $items->groupBy('indicatorsTitle')->map(function ($indicators, $indicatorsTitle) {
                    $firstIndicator = $indicators->first();

                    return [
                        'indicatorsTitle' => $indicatorsTitle,
                        'indicatorsID' => $firstIndicator->indicatorsID,
                        'coefficient' => $firstIndicator->coefficient,
                        'variables' => $indicators->map(function ($item) {
                            return [
                                'variableID' => $item->variableID,
                                'variableName' => $item->variableName,
                                'weight' => $item->weight,
                            ];
                        })->values()->toArray(),
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        return (object) [
            'name' => $name,
            'sections' => $sections,
        ];
    }
}
