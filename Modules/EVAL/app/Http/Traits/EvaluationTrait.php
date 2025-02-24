<?php

namespace Modules\EVAL\app\Http\Traits;

use Illuminate\Support\Facades\DB;
use Modules\EVAL\app\Http\Enums\EvaluationStatusEnum;
use Modules\EVAL\app\Models\EvalEvaluation;
use Modules\EVAL\app\Models\EvalEvaluationAnswer;
use Modules\EVAL\app\Models\EvalVariableTarget;
use Modules\HRMS\app\Http\Enums\OunitCategoryEnum;

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

    public function calculateEvaluation($evaluationId)
    {
        $evaluation = EvalEvaluationAnswer::with('evalCircularVariables')->where('eval_evaluation_id', $evaluationId)->get();



    }



}
