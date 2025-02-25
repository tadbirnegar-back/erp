<?php

namespace Modules\EVAL\app\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\EVAL\app\Http\Enums\EvaluationStatusEnum;
use Modules\EVAL\app\Models\EvalEvaluation;
use Modules\EVAL\app\Models\EvalEvaluationAnswer;
use Modules\EVAL\app\Models\EvalEvaluationStatus;
use Modules\EVAL\app\Models\EvalVariableTarget;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\VillageOfc;

trait EvaluationTrait

{
    public function indexForOnlyOneStatus($id, $statusID)
    {
        $eval = EvalEvaluation::whereHas('lastStatusOfEvaluation', function ($query) use ($statusID) {
            $query->where('status_id', $statusID);
        })->find($id);
        return $eval;
    }

    public function showVariables($village, $id)
    {
        $ounitCategoryId = OunitCategoryEnum::VillageOfc;

        //declaring key variables
        $variables = [
            'hasLicense' => $village->unitable->hasLicense,
            'isAttached_to_city' => $village->unitable->isAttached_to_city,
            'isTourism' => $village->unitable->isTourism,
            'isFarm' => $village->unitable->isFarm,
            'degree' => $village->unitable->degree,
            'population_1395' => $village->unitable->population_1395,
        ];

        $result = EvalEvaluation::query()
            ->leftJoinRelationship('evalCircular.evalCircularSections.evalCircularIndicators.evalCircularVariable.evalVariableTargets.oucPropertyValue.oucProperty')
            ->select([
                //eval
                'eval_evaluations.id as eval_id',
                //circulars
                'eval_circulars.maximum_value as circular_max_value',
                //sections
                'eval_circular_sections.title as section_title',
                //indicators
                'eval_circular_indicators.title as indicator_title',
                'eval_circular_indicators.coefficient as indicator_coefficient',
                //variables
                'eval_circular_variables.id as variable_id',
                'eval_circular_variables.title as variable_title',
                'eval_circular_variables.weight as variable_weight',
                'eval_circular_variables.description as variable_description',
                //targets
                'ouc_property_values.value as ouc_property_value',
                'ouc_property_values.operator as ouc_property_operation',
                'ouc_properties.column_name as ouc_property_col_name',
                'ouc_properties.name as ouc_property_name',
            ])
            ->whereNotNull('eval_circular_variables.id')
            ->where('eval_evaluations.id', $id)
            ->distinct('variable_id')
            ->get()
            ->filter(function ($row) use ($variables) {
                if (is_null($row->ouc_property_col_name)) {
                    return true;
                }

                if (isset($variables[$row->ouc_property_col_name])) {
                    $villageValue = $variables[$row->ouc_property_col_name];
                    $propertyValue = $row->ouc_property_value;
                    $operation = $row->ouc_property_operation;

                    switch ($operation) {
                        case '>':
                            return $villageValue > $propertyValue;
                        case '<':
                            return $villageValue < $propertyValue;
                        case '>=':
                            return $villageValue >= $propertyValue;
                        case '<=':
                            return $villageValue <= $propertyValue;
                        case '==':
                        case '=':
                            return $villageValue == $propertyValue;
                        default:
                            return false;
                    }
                }

                return false;
            });

        return $result->values();
    }

    public function setAnswers($evaluationId, $answers)
    {
        $data = [];

        foreach ($answers as $answer) {
            $data[] = [
                'eval_evaluation_id' => $evaluationId,
                'eval_circular_variables_id' => $answer->variable_id,
                'value' => $answer->value,
            ];
        }

        if (!empty($data)) {
            EvalEvaluationAnswer::insert($data);
        }
    }

    public function calculateEvaluation($evaluationId, $user)
    {
        $evaluations = EvalEvaluationAnswer::with('evalCircularVariables.evalCircularIndicator')
            ->where('eval_evaluation_id', $evaluationId)
            ->get();

        $groupedResults = $evaluations->groupBy(function ($evaluation) {
            return $evaluation->evalCircularVariables->eval_circular_indicator_id ?? 'unknown';
        });

        $finalResults = [];
        $totalSum = 0;
        $totalCoefficient = 0;

        foreach ($groupedResults as $indicatorId => $answers) {
            $sum = 0;

            foreach ($answers as $answer) {
                $sum += $answer->value * $answer->evalCircularVariables->weight / 100;
            }

            $coefficient = $answers->first()->evalCircularVariables->evalCircularIndicator->coefficient ?? 1;

            $finalResults[$indicatorId] = $sum * $coefficient;

            $totalSum += $finalResults[$indicatorId];
            $totalCoefficient += $coefficient;
        }

        $weightedAverage = round($totalCoefficient > 0 ? $totalSum / $totalCoefficient : 0, 1);

        EvalEvaluation::find($evaluationId)->update([
            'sum' => $totalSum,
            'average' => $weightedAverage,
            'evaluator_id' => $user->id
        ]);

        $this->makeEvaluationDone($evaluationId, $user);
    }

    private function makeEvaluationDone($evaluationId, $user)
    {
        $status = $this->evaluationDoneStatus();
        EvalEvaluationStatus::create([
            'eval_evaluation_id' => $evaluationId,
            'status_id' => $status->id,
            'creator_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
            'description' => null,
        ]);
    }

    public function showPreDatas($eval)
    {
        $evalOunit = $eval->target_ounit_id;

        $myQueryAsString = "with recursive `laravel_cte` as ((select `organization_units`.*, 0 as `depth`, cast(`id` as char(65535)) as `path`
         from `organization_units` where `organization_units`.`id` in ($evalOunit))
          union all (select `organization_units`.*, `depth` - 1 as `depth`, concat(`path`, '.', `organization_units`.`id`) from `organization_units` inner join `laravel_cte` on `laravel_cte`.`parent_id` = `organization_units`.`id`))
           select * from `laravel_cte` where `unitable_type` != 'Modules\OUnitMS\app\Models\TownOfc'";


        $result = DB::table(DB::raw($myQueryAsString))


            ->get();

        return DB::select($myQueryAsString);

        $ounit = OrganizationUnit::with(['ancestorsAndSelf' => function ($query) {
            $query->whereIn('unitable_type', [VillageOfc::class, DistrictOfc::class, CityOfc::class, StateOfc::class]);
        }])->find($evalOunit);

        $villageId = $ounit->ancestorsAndSelf->firstWhere('unitable_type', VillageOfc::class)?->id;
        $districtId = $ounit->ancestorsAndSelf->firstWhere('unitable_type', DistrictOfc::class)?->id;
        $cityId = $ounit->ancestorsAndSelf->firstWhere('unitable_type', CityOfc::class)?->id;
        $stateId = $ounit->ancestorsAndSelf->firstWhere('unitable_type', StateOfc::class)?->id;

        $organs = [
            'village' => $villageId,
            'district' => $districtId,
            'city' => $cityId,
            'state' => $stateId
        ];

        $villagerAnswers = $this->getVillageAnswers($eval->id);
        $districtAnswers = $this->getDistrictAnswers($organs, $eval);
        return ["village" => $villagerAnswers, "district" => $districtAnswers];
    }

    private function getDistrictAnswers($organs, $eval)
    {
        return EvalEvaluation::with('evaluationAnswers')
            ->where('eval_circular_id', $eval->eval_circular_id)
            ->where('target_ounit_id', $organs['district'])
            ->first();
    }

    private function getVillageAnswers($evalId)
    {
        return EvalEvaluation::with('evaluationAnswers')->where('id', $evalId)->first();
    }

    public function evaluationDoneStatus()
    {
        return EvalEvaluation::GetAllStatuses()->firstWhere('name', EvaluationStatusEnum::DONE->value);
    }

    public function evaluationWaitToDoneStatus()
    {
        return EvalEvaluation::GetAllStatuses()->firstWhere('name', EvaluationStatusEnum::WAIT_TO_DONE->value);
    }

    public function evaluationExpiredStatus()
    {
        return EvalEvaluation::GetAllStatuses()->firstWhere('name', EvaluationStatusEnum::EXPIRED->value);
    }

}
