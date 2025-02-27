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
use Modules\OUnitMS\app\Models\TownOfc;
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
        $circularId = $eval->eval_circular_id;

        $forbiddenOunittype = [addslashes(TownOfc::class),addslashes(VillageOfc::class)];
        $stringForbiddenOunittype = implode("','",$forbiddenOunittype);

        $myQueryAsString = "WITH RECURSIVE `laravel_cte` AS (
        (SELECT `organization_units`.*, 0 AS `depth`, CAST(`id` AS CHAR(65535)) AS `path`
         FROM `organization_units`
         WHERE `organization_units`.`id` = $evalOunit)
        UNION ALL
        (SELECT `organization_units`.*, `depth` - 1 AS `depth`, CONCAT(`path`, '.', `organization_units`.`id`)
         FROM `organization_units`
         INNER JOIN `laravel_cte` ON `laravel_cte`.`parent_id` = `organization_units`.`id`)
    )
    SELECT * FROM `laravel_cte` WHERE `unitable_type` NOT IN ('$stringForbiddenOunittype')";


        $myQueryAsForAncestors = "WITH RECURSIVE `laravel_cte` AS (
        (SELECT `organization_units`.*, 0 AS `depth`, CAST(`id` AS CHAR(65535)) AS `path`
         FROM `organization_units`
         WHERE `organization_units`.`id` = $evalOunit)
        UNION ALL
        (SELECT `organization_units`.*, `depth` - 1 AS `depth`, CONCAT(`path`, '.', `organization_units`.`id`)
         FROM `organization_units`
         INNER JOIN `laravel_cte` ON `laravel_cte`.`parent_id` = `organization_units`.`id`)
    )
    SELECT * FROM `laravel_cte`";

        //Get Only The Village One
        $result=OrganizationUnit::join('users','organization_units.head_id','=','users.id')
            ->join('persons','persons.id','=','users.person_id')
            ->join('eval_evaluations as eval','eval.target_ounit_id','=','organization_units.id')
            ->leftJoin('users as evaluator','evaluator.id','=','eval.evaluator_id')
            ->leftJoin('persons as evaluator_person','evaluator_person.id','=','evaluator.person_id')
            ->where('organization_units.id',$evalOunit)
            ->where('eval.eval_circular_id',$circularId)
            ->leftJoin('eval_evaluation_answers as answers', 'answers.eval_evaluation_id', '=', 'eval.id')
            ->leftJoin('eval_circular_variables as variables', 'variables.id', '=', 'answers.eval_circular_variables_id')
            ->leftJoin('eval_circular_indicators as indicators', 'indicators.id', '=', 'variables.eval_circular_indicator_id')
            ->leftJoin('eval_circular_sections as sections', 'sections.id', '=', 'indicators.eval_circular_section_id')
            ->leftJoin('village_ofcs as village_alias', 'village_alias.id', '=', 'eval.target_ounit_id')
            ->whereColumn('eval.target_ounit_id', '=', 'eval.evaluator_ounit_id')
            ->select([
                'organization_units.id as ou_id',
                'organization_units.name as ou_name',
                'persons.display_name as head_name',
                'eval.id as eval_id',
                'eval.parent_id as parent_id',
                'eval.title as eval_title',
                'eval.sum as eval_sum',
                'eval.average as eval_average',
                'eval.eval_circular_id as eval_circuit_id',
                'answers.id as answer_id',
                'answers.value as answer_value',
                'variables.id as variable_id',
                'variables.title as variable_title',
                'variables.description as variable_description',
                'indicators.id as indicator_id',
                'indicators.title as indicator_title',
                'sections.id as section_id',
                'sections.title as section_title',
                'village_alias.abadi_code as village_abadi_code',
                'evaluator_person.display_name as evaluator_name',
            ])
            ->withoutGlobalScopes()
        ->get();

        //Get the Ancestors
        $ancestors=DB::table(DB::raw("($myQueryAsString) as ounits_alias"))
            ->leftJoin('eval_evaluations as eval', function ($join) use ($circularId) {
                $join->on('ounits_alias.id', '=','eval.evaluator_ounit_id')
                    ->where('eval.eval_circular_id',$circularId);
            })
            ->leftJoin('users','ounits_alias.head_id','=','users.id')
            ->leftJoin('persons','persons.id','=','users.person_id')
            ->leftJoin('eval_evaluations','eval_evaluations.evaluator_ounit_id','=','ounits_alias.id')
            ->leftJoin('users as evaluator','evaluator.id','=','eval_evaluations.evaluator_id')
            ->leftJoin('persons as evaluator_person','evaluator_person.id','=','evaluator.person_id')
            ->leftJoin('eval_evaluation_answers as answers', 'answers.eval_evaluation_id', '=', 'eval.id')
            ->leftJoin('eval_circular_variables as variables', 'variables.id', '=', 'answers.eval_circular_variables_id')
            ->leftJoin('eval_circular_indicators as indicators', 'indicators.id', '=', 'variables.eval_circular_indicator_id')
            ->leftJoin('eval_circular_sections as sections', 'sections.id', '=', 'indicators.eval_circular_section_id')
            ->leftJoin('village_ofcs as village_alias', 'village_alias.id', '=', 'eval.target_ounit_id')

            ->select([
                'ounits_alias.id as ou_id',
                'ounits_alias.unitable_type as ou_type',
                'ounits_alias.name as ou_name',
                'persons.display_name as head_name',
                'evaluator_person.display_name as evaluator_name',
                'eval.id as eval_id',
                'eval.parent_id as parent_id',
                'eval.title as eval_title',
                'eval.sum as eval_sum',
                'eval.average as eval_average',
                'eval.eval_circular_id as eval_circuit_id',
                'answers.id as answer_id',
                'answers.value as answer_value',
                'variables.id as variable_id',
                'variables.title as variable_title',
                'variables.description as variable_description',
                'indicators.id as indicator_id',
                'indicators.title as indicator_title',
                'sections.id as section_id',
                'sections.title as section_title',
                'village_alias.abadi_code as village_abadi_code',
                'evaluator_person.display_name as evaluator_name',
            ])
        ->get();

        return ["village" => $result, "ancestors" => $ancestors , "ounits" => DB::select($myQueryAsForAncestors)];
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
