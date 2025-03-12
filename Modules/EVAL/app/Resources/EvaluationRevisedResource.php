<?php

namespace Modules\EVAL\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Log;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\VillageOfc;

class EvaluationRevisedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        $userID = $this['user']->id;

        return $this->componentToRender($userID);
    }

    private function getEvaluationDataForDeclaredOunitType($data, $type)
    {
            $filteredData = $data->filter(function ($item) use ($type) {
                return $item->ou_type == $type;
            })->values();
            return [$filteredData];



    }



    private function getAncesstorsPersonData($data, $type)
    {
        $firstData = $data->filter(function ($item) use ($type) {
            return $item->ou_type == $type;
        })->first();
        return [
            'name' => $firstData->head_name,
            'avg' => $firstData->eval_average,
            'sum' => $firstData->eval_sum,
            'head_id' => $firstData->head_id,
            'ou_type' => $firstData->ou_type,
            'evaluator_id' => $firstData->evaluator_id,
        ];
    }

    private function getPersonData($data)
    {
        return [
            'name' => $data['head_name'],
            'avg' => $data['eval_average'],
            'sum' => $data['eval_sum'],
            'head_id' => $data['head_id'],

        ];
    }


    private function componentToRender($userID)
    {
        $dehyar = $this->getPersonData($this['village'][0]);

        $district = $this->getEvaluationDataForDeclaredOunitType($this['ancestors'], DistrictOfc::class)[0];
        $bakhshdar = $this->getAncesstorsPersonData($this['ancestors'], DistrictOfc::class);

        $city = $this->getEvaluationDataForDeclaredOunitType($this['ancestors'], CityOfc::class)[0];
        $farmandar = $this->getAncesstorsPersonData($this['ancestors'], CityOfc::class);

        $state = $this->getEvaluationDataForDeclaredOunitType($this['ancestors'], StateOfc::class)[0];
        $stateOfc = $this->getAncesstorsPersonData($this['ancestors'], StateOfc::class);

        // Organize data
        $data = [
            'dehyar' => [
                "personalInfo" => $dehyar,
                "evaluation" => $this['village'],
            ],
            'district' => [
                "personalInfo" => $bakhshdar,
                'evaluation' => $district,
            ],
            'city' => [
                "personalInfo" => $farmandar,
                'evaluation' => $city,
            ],
            'state' => [
                "personalInfo" => $stateOfc,
                'evaluation' => $state,
            ]
        ];


        if ($bakhshdar && $bakhshdar['head_id'] == $userID) {
            return [
                'VillageOfc' => $data['dehyar'],
                'DistrictOfc' => $data['district'],
                'canEvaluate' => false,
                'role' => "DistrictOfc",
                'previousEval' => "VillageOfc"
            ];
        }

        if ($farmandar && $farmandar['head_id'] == $userID) {
            return [
                'VillageOfc' => $data['dehyar'],
                'DistrictOfc' => $data['district'],
                'CityOfc' => $data['city'],
                'canEvaluate' => false,
                'role' => "CityOfc",
//                'previousEval' => is_null($data['district'][0]['answer_id']) ? 'VillageOfc' : 'DistrictOfc'
                'previousEval' => $data['district'][0]['answer_id']
            ];
        }

        if ($stateOfc && $stateOfc['head_id'] == $userID) {
            return [
                'VillageOfc' => $data['dehyar'],
                'DistrictOfc' => $data['district'],
                'CityOfc' => $data['city'],
                'StateOfc' => $data['state'],
                'canEvaluate' => false,
                'role' => "StateOfc",
            ];
        }

        $ounitType = $this->NotEvaluatedyet($userID);

        if ($ounitType == DistrictOfc::class) {
            return [
                'VillageOfc' => $data['dehyar'],
                'DistrictOfc' => $data['district'],
                'canEvaluate' => true,
                'role' => "DistrictOfc",
            ];
        }

        if ($ounitType == CityOfc::class) {
            return [
                'VillageOfc' => $data['dehyar'],
                'DistrictOfc' => $data['district'],
                'CityOfc' => $data['city'],
                'canEvaluate' => true,
                'role' => "CityOfc",
                'previousEval' => $data['district'][0]['answer_id']
            ];
        }

        if ($ounitType == StateOfc::class) {
            return [
                'VillageOfc' => $data['dehyar'],
                'DistrictOfc' => $data['district'], -
                'CityOfc' => $data['city'],
                'StateOfc' => $data['state'],
                'canEvaluate' => true,
                'role' => "StateOfc",
            ];
        }
    }


    private function NotEvaluatedyet($userID)
    {
        $ounit = $this['ounits']->firstWhere('head_id', $userID);
        return $ounit ? $ounit->unitable_type : 'none';
    }

}
