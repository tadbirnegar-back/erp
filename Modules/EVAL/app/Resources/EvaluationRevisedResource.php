<?php

namespace Modules\EVAL\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\StateOfc;

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
        return [
            $data->filter(function ($item) use ($type) {
                return $item->ou_type == $type;
            })->values()->groupBy('variable_id')
        ];
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

        $district = $this->getEvaluationDataForDeclaredOunitType($this['ancestors'], DistrictOfc::class);
        $bakhshdar = $this->getAncesstorsPersonData($this['ancestors'], DistrictOfc::class);

        $city = $this->getEvaluationDataForDeclaredOunitType($this['ancestors'], CityOfc::class);
        $farmandar = $this->getAncesstorsPersonData($this['ancestors'], CityOfc::class);

        $state = $this->getEvaluationDataForDeclaredOunitType($this['ancestors'], StateOfc::class);
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
                'dehyar' => $data['dehyar'],
                'district' => $data['district'],
                'canEvaluate' => false,
            ];
        }

        if ($farmandar && $farmandar['head_id'] == $userID) {
            return [
                'dehyar' => $data['dehyar'],
                'district' => $data['district'],
                'city' => $data['city'],
                'canEvaluate' => false,
            ];
        }

        if ($stateOfc && $stateOfc['head_id'] == $userID) {
            return [
                'dehyar' => $data['dehyar'],
                'district' => $data['district'],
                'city' => $data['city'],
                'state' => $data['state'],
                'canEvaluate' => false,
            ];
        }

        $ounitType =  $this->NotEvaluatedyet($userID);
        if($ounitType == DistrictOfc::class){
            return [
                'dehyar' => $data['dehyar'],
                'canEvaluate' => true,
            ];
        }

        if($ounitType == CityOfc::class){
            return [
                'dehyar' => $data['dehyar'],
                'district' => $data['district'],
                'canEvaluate' => true,
            ];
        }

        if($ounitType == StateOfc::class){
            return [
                'dehyar' => $data['dehyar'],
                'district' => $data['district'],
                'city' => $data['city'],
                'canEvaluate' => true,
            ];
        }
    }


    private function NotEvaluatedyet($userID)
    {
        foreach ($this['ounits'] as $ounit) {
            if ($ounit->head_id == $userID) {
                return $ounit->unitable_type;
            }
        }
        return 'none';
    }


}
