<?php

namespace Modules\EVAL\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\EVAL\app\Http\Enums\EvalCircularStatusEnum;

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
        $status = $firstItem->statusName ?? null;
        $statusName = ($status == EvalCircularStatusEnum::PISHNEVIS->value) ? true : false;

        $grouped = $this->collection->groupBy('sectionTitle');

        $sections = $grouped->map(function ($items, $sectionTitle) {
            $firstSection = $items->first();

            if ($sectionTitle == null) {
                return [];
            }

            $indicators = $items->groupBy('indicatorsTitle')->map(function ($indicators, $indicatorsTitle) {
                $firstIndicator = $indicators->first();

                // Check if the indicator is empty or invalid
                $isEmptyIndicator = empty($indicatorsTitle) &&
                    is_null($firstIndicator->indicatorsID ?? null) &&
                    is_null($firstIndicator->coefficient ?? null);

                // If the indicator is empty, return null (it will be filtered out later)
                if ($isEmptyIndicator) {
                    return null;
                }

                return [
                    'indicatorsTitle' => $indicatorsTitle,
                    'indicatorsID' => $firstIndicator->indicatorsID ?? null,
                    'coefficient' => $firstIndicator->coefficient ?? null,
                    'variables' => $indicators->map(function ($item) {
                        // Check if the variable is empty or invalid
                        $isEmptyVariable = is_null($item->variableID ?? null) &&
                            is_null($item->variableName ?? null) &&
                            is_null($item->weight ?? null);

                        // If the variable is empty, return null (it will be filtered out later)
                        if ($isEmptyVariable) {
                            return null;
                        }

                        return [
                            'variableID' => $item->variableID ?? null,
                            'variableName' => $item->variableName ?? null,
                            'weight' => $item->weight ?? null,
                        ];
                    })->filter()->values()->toArray(), // Filter out null variables
                ];
            })->filter()->values()->toArray(); // Filter out null indicators

            return [
                'sectionTitle' => $sectionTitle,
                'sectionID' => $firstSection->sectionID ?? null,
                'indicators' => !empty($indicators) ? $indicators : [], // Ensure indicators is an empty array if empty
            ];
        })->values()->toArray();

        return (object) [
            'name' => $name,
            'sections' => $this->getSections($sections),
            'indicators' => $this->getIndicators($sections),
            'variables' => $this->getVariables($sections),
            'Editable' => $statusName,
        ];
    }

    private function getSections($items)
    {
        return !empty($items[0]) ? $items : [];
    }

    private function getIndicators($items)
    {
        return !empty($items[0]) ? $items : [];
    }

    private function getVariables($items)
    {
        return !empty($items[0]) ? $items : [];
    }
}
