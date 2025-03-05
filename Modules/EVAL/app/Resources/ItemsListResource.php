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

            if($sectionTitle == null){
                return [];
            }
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
            'sections' => $this->getSections($sections),
            'indicators' => $this->getIndicators($sections),
            'variables'=>$this->getVariables($sections),
            'Editable' => $statusName,
        ];
    }

    private function getSections($items){
        $data = [];
        if(empty($items[0])){
            $data = [];
        }else{
            $data = $items;
        }
        return $data;
    }
    private function getIndicators($items){
        $data = [];
        if(empty($items[0])){
            $data = [];
        }else{
            $data = $items;
        }
        return $data;
    } private function getVariables($items){
        $data = [];
        if(empty($items[0])){
            $data = [];
        }else{
            $data = $items;
        }
        return $data;
    }
}
