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
        })->map(function ($item) {
            $item->eval_date = convertDateTimeGregorianToJalaliDateTime($item->eval_date);
            return $item;
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
                "evaluation" => $this['village']->map(function ($item) {
                    $item->eval_date = convertDateTimeGregorianToJalaliDateTime($item->eval_date);
                    return $item;
                })->values(),
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


        $ounitType = $this->NotEvaluatedyet($userID);


        if ($bakhshdar && $bakhshdar['sum'] != null && $ounitType == DistrictOfc::class) {

            return [
                'VillageOfc' => $data['dehyar'],
                'DistrictOfc' => $data['district'],
                'canEvaluate' => false,
                'role' => "DistrictOfc",
                'previousEval' => "VillageOfc",
                'NullObjects' => $this->nullObjects('VillageOfc', 'DistrictOfc' , $data , $ounitType),
            ];
        }

        if ($farmandar && $farmandar['sum'] != null && $ounitType == CityOfc::class) {
            $previousEval = is_null($data['district']['evaluation'][0]->variable_id) ? 'VillageOfc' : 'DistrictOfc';

            return [
                'VillageOfc' => $data['dehyar'],
                'DistrictOfc' => $data['district'],
                'CityOfc' => $data['city'],
                'canEvaluate' => false,
                'role' => "CityOfc",
                'previousEval' => $previousEval,
                'NullObjects' => $this->nullObjects($previousEval, 'CityOfc' , $data , $ounitType),
            ];
        }

        if ($stateOfc && $stateOfc['sum'] != null && $ounitType == StateOfc::class) {
            $previousEval = is_null($data['city']['evaluation'][0]->variable_id) ? (is_null($data['district']['evaluation'][0]->variable_id) ? 'VillageOfc' : 'DistrictOfc') : 'CityOfc';

            return [
                'VillageOfc' => $data['dehyar'],
                'DistrictOfc' => $data['district'],
                'CityOfc' => $data['city'],
                'StateOfc' => $data['state'],
                'canEvaluate' => false,
                'role' => "StateOfc",
                'previousEval' => $previousEval,
                'NullObjects' => $this->nullObjects($previousEval, 'StateOfc' , $data , $ounitType),

            ];
        }

        if($ounitType == VillageOfc::class){
            return [
                'VillageOfc' => $data['dehyar'],
                'canEvaluate' =>false,
                'role' => "VillageOfc",
                'previousEval' => "VillageOfc",
                'NullObjects' => [],
            ];
        }


        //-------------------------------------------------------Not Revised yet-----------------------------------------------------------------------------------------

        if ($ounitType == DistrictOfc::class) {
            return [
                'VillageOfc' => $data['dehyar'],
                'DistrictOfc' => $data['district'],
                'canEvaluate' => $this->canEvaluate($data , $ounitType),
                'role' => "DistrictOfc",
                'previousEval' => "VillageOfc",
                'NullObjects' => $this->nullObjects('VillageOfc', 'DistrictOfc' , $data , $ounitType),
            ];
        }

        if ($ounitType == CityOfc::class) {
            $previousEval = is_null($data['district']['evaluation'][0]->variable_id) ? 'VillageOfc' : 'DistrictOfc';
            return [
                'VillageOfc' => $data['dehyar'],
                'DistrictOfc' => $data['district'],
                'CityOfc' => $data['city'],
                'canEvaluate' => $this->canEvaluate($data , $ounitType),
                'role' => "CityOfc",
                'previousEval' => $previousEval,
                'NullObjects' => $this->nullObjects($previousEval, 'CityOfc' , $data , $ounitType),
            ];
        }

        if ($ounitType == StateOfc::class) {
            $previousEval = is_null($data['city']['evaluation'][0]->variable_id) ? (is_null($data['district']['evaluation'][0]->variable_id) ? 'VillageOfc' : 'DistrictOfc') : 'CityOfc';
            return [
                'VillageOfc' => $data['dehyar'],
                'DistrictOfc' => $data['district'],
                'CityOfc' => $data['city'],
                'StateOfc' => $data['state'],
                'canEvaluate' => true,
                'role' => "StateOfc",
                'previousEval' => $previousEval,
                'NullObjects' => $this->nullObjects($previousEval, 'StateOfc' , $data , $ounitType),
            ];
        }
    }

    private function NotEvaluatedyet($userID)
    {
        $ounitTypes = [StateOfc::class,CityOfc::class,DistrictOfc::class,VillageOfc::class];

        foreach ($ounitTypes as $ounitType) {
            $ounit = $this['ounits']->where('unitable_type' , $ounitType)->firstWhere('head_id', $userID);
            if ($ounit) {
                return $ounit->unitable_type;
            }
        }
        return 'none';
    }

    private function nullObjects($previous, $now , $data , $ounitType)
    {
        $ounits = ['VillageOfc', 'DistrictOfc', 'CityOfc', 'StateOfc'];

        if($ounitType == DistrictOfc::class){
            if($data['city']['evaluation'][0]->variable_id != null || $data['state']['evaluation'][0]->variable_id != null){
                $arraysForState[] = "DistrictOfc";
            }
        }

        if($ounitType == CityOfc::class){
            $prevIndex = array_search($previous, $ounits);
            $nowIndex = array_search($now, $ounits);

            $arraysForState = [];
            if($data['city']['evaluation'][0]->variable_id == null){
                if($data['state']['evaluation'][0]->variable_id != null){
                    $arraysForState[] = "CityOfc";
                }
            }

            $start = min($prevIndex, $nowIndex);
            $end = max($prevIndex, $nowIndex);

            $sliced =  array_slice($ounits, $start + 1, $end - $start - 1);

            return array_unique(array_merge($arraysForState , $sliced));
        }


        $arraysForState = [];

        if($data['city']['evaluation'][0]->variable_id == null){
            $arraysForState[] = "CityOfc";
        }

        if($data['district']['evaluation'][0]->variable_id == null){
            $arraysForState[] = "DistrictOfc";
        }

        return array_unique($arraysForState);
    }

    private function canEvaluate($data , $ounitType)
    {
        $ounitTypes = [StateOfc::class,CityOfc::class,DistrictOfc::class,VillageOfc::class];

        if($ounitType == DistrictOfc::class){
            if($data['city']['evaluation'][0]->variable_id != null || $data['state']['evaluation'][0]->variable_id != null){
                return false;
            }else{
                return true;
            }
        }
        if($ounitType == CityOfc::class){
            if($data['state']['evaluation'][0]->variable_id != null){
                return false;
            }else{
                return true;
            }
        }
    }

}
