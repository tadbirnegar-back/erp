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

    public function showPreDatas($eval, $user)
    {
        $evalOunit = $eval->target_ounit_id;
        $circularId = $eval->eval_circular_id;

        $forbiddenOunittype = [addslashes(TownOfc::class), addslashes(VillageOfc::class)];
        $forbiddenOunittypeTown = addslashes(TownOfc::class);
        $stringForbiddenOunittype = implode("','", $forbiddenOunittype);

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
    SELECT * FROM `laravel_cte` WHERE `unitable_type` != '$forbiddenOunittypeTown'";
        $CompletedStatus = $this->evaluationDoneStatus();

        $result = OrganizationUnit::join('users', 'organization_units.head_id', '=', 'users.id')
            ->join('persons', 'persons.id', '=', 'users.person_id')
            ->join('eval_evaluations as eval', 'eval.target_ounit_id', '=', 'organization_units.id')
            ->join('evalEvaluation_status as eval_status_alias', 'eval_status_alias.eval_evaluation_id', '=', 'eval.id')
            ->where('eval_status_alias.status_id', $CompletedStatus->id)
            ->leftJoin('users as evaluator', 'evaluator.id', '=', 'eval.evaluator_id')
            ->leftJoin('persons as evaluator_person', 'evaluator_person.id', '=', 'evaluator.person_id')
            ->where('organization_units.id', $evalOunit)
            ->where('eval.eval_circular_id', $circularId)
            ->leftJoin('eval_evaluation_answers as answers', 'answers.eval_evaluation_id', '=', 'eval.id')
            ->leftJoin('eval_circular_variables as variables', 'variables.id', '=', 'answers.eval_circular_variables_id')
            ->leftJoin('eval_circular_indicators as indicators', 'indicators.id', '=', 'variables.eval_circular_indicator_id')
            ->leftJoin('eval_circular_sections as sections', 'sections.id', '=', 'indicators.eval_circular_section_id')
            ->leftJoin('eval_circulars as circular_alias', 'circular_alias.id', '=', 'eval.eval_circular_id')
            ->leftJoin('village_ofcs as village_alias', 'village_alias.id', '=', 'eval.target_ounit_id')
            ->whereColumn('eval.target_ounit_id', '=', 'eval.evaluator_ounit_id')
            ->select([
                'organization_units.id as ou_id',
                'organization_units.name as ou_name',
                'persons.display_name as head_name',
                'evaluator.id as head_id',
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
                'variables.weight as variable_weight',
                'indicators.id as indicator_id',
                'indicators.title as indicator_title',
                'indicators.coefficient as indicator_coefficient',
                'sections.id as section_id',
                'sections.title as section_title',
                'village_alias.abadi_code as village_abadi_code',
                'evaluator_person.display_name as evaluator_name',
                'users.id as evaluator_id',
                'eval_status_alias.created_at as eval_date',
                'circular_alias.maximum_value as circular_max_value',
            ])
            ->whereNotNull('variables.id')
            ->withoutGlobalScopes()
            ->get();

        //Get the Ancestors
        $ancestors = DB::table(DB::raw("($myQueryAsString) as ounits_alias"))
            ->leftJoin('eval_evaluations as eval', function ($join) use ($circularId) {
                $join->on('ounits_alias.id', '=', 'eval.evaluator_ounit_id')
                    ->where('eval.eval_circular_id', $circularId);
            })
            ->leftJoin('users', 'ounits_alias.head_id', '=', 'users.id')
            ->leftJoin('persons', 'persons.id', '=', 'users.person_id')
            ->leftJoin('eval_evaluations', 'eval_evaluations.evaluator_ounit_id', '=', 'ounits_alias.id')
            ->leftJoin('users as evaluator', 'evaluator.id', '=', 'eval_evaluations.evaluator_id')
            ->leftJoin('persons as evaluator_person', 'evaluator_person.id', '=', 'evaluator.person_id')
            ->leftJoin('eval_evaluation_answers as answers', 'answers.eval_evaluation_id', '=', 'eval.id')
            ->leftJoin('eval_circular_variables as variables', 'variables.id', '=', 'answers.eval_circular_variables_id')
            ->leftJoin('eval_circular_indicators as indicators', 'indicators.id', '=', 'variables.eval_circular_indicator_id')
            ->leftJoin('eval_circular_sections as sections', 'sections.id', '=', 'indicators.eval_circular_section_id')
            ->leftJoin('eval_circulars as circular_alias', 'circular_alias.id', '=', 'eval.eval_circular_id')
            ->leftJoin('village_ofcs as village_alias', 'village_alias.id', '=', 'eval.target_ounit_id')
            ->select([
                'ounits_alias.id as ou_id',
                'ounits_alias.unitable_type as ou_type',
                'ounits_alias.name as ou_name',
                'persons.display_name as head_name',
                'evaluator.id as head_id',
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
                'users.id as evaluator_id',
                'circular_alias.maximum_value as circular_max_value',
            ])
            ->get();

        $ounitsAncestors = OrganizationUnit::with(['ancestorsAndSelf' => function ($query) {
            $query->where('unitable_type', '!=', TownOfc::class);
            $query->with('head.person.position');
        }])->find($evalOunit);

        return ["village" => $result, "ancestors" => $ancestors, "ounits" => $ounitsAncestors, 'user' => $user];
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

    public function isPersonAllowToEvaluate($user, $eval)
    {
        $forbiddenOunittypeTown = addslashes(TownOfc::class);

        $myQueryAsForAncestors = "WITH RECURSIVE `laravel_cte` AS (
        (SELECT `organization_units`.*, 0 AS `depth`, CAST(`id` AS CHAR(65535)) AS `path`
         FROM `organization_units`
         WHERE `organization_units`.`id` = $eval->target_ounit_id)
        UNION ALL
        (SELECT `organization_units`.*, `depth` - 1 AS `depth`, CONCAT(`path`, '.', `organization_units`.`id`)
         FROM `organization_units`
         INNER JOIN `laravel_cte` ON `laravel_cte`.`parent_id` = `organization_units`.`id`)
    )
    SELECT * FROM `laravel_cte` WHERE `unitable_type` != '$forbiddenOunittypeTown'";

        $ounits = DB::select($myQueryAsForAncestors);

        $head = [];
        foreach ($ounits as $ounit) {
            if ($ounit->head_id == $user->id) {
                $head[] = $ounit;
            }
        }

        $evalsBefore = [];
        foreach ($head as $head) {
            $evalsBefore[] = EvalEvaluation::
            where('target_ounit_id', $eval->target_ounit_id)
                ->where('eval_circular_id', $eval->eval_circular_id)
                ->where('evaluator_id', $head->head_id)
                ->where('evaluator_ounit_id', $head->id)
                ->first();
        }
        return $evalsBefore[0] == null;

    }

    public function evaluate($eval, $data, $user)
    {
        $forbiddenOunittypeTown = addslashes(TownOfc::class);

        $myQueryAsForAncestors = "WITH RECURSIVE `laravel_cte` AS (
        (SELECT `organization_units`.*, 0 AS `depth`, CAST(`id` AS CHAR(65535)) AS `path`
         FROM `organization_units`
         WHERE `organization_units`.`id` = $eval->target_ounit_id)
        UNION ALL
        (SELECT `organization_units`.*, `depth` - 1 AS `depth`, CONCAT(`path`, '.', `organization_units`.`id`)
         FROM `organization_units`
         INNER JOIN `laravel_cte` ON `laravel_cte`.`parent_id` = `organization_units`.`id`)
    )
    SELECT * FROM `laravel_cte` WHERE `unitable_type` != '$forbiddenOunittypeTown'";

        $ounits = DB::select($myQueryAsForAncestors);

        $heads = [];
        foreach ($ounits as $ounit) {
            if ($ounit->head_id == $user->id) {
                $heads[] = $ounit;
            }
        }

        $noneEvaluatedBefore = [];
        foreach ($heads as $head) {
            $evalutedBeforeEvals = EvalEvaluation::where('eval_circular_id', $eval->eval_circular_id)
                ->where('target_ounit_id', $eval->target_ounit_id)
                ->where('evaluator_id', $head->head_id)
                ->where('evaluator_ounit_id', $head->id)
                ->first();

            if ($evalutedBeforeEvals == null) {
                $noneEvaluatedBefore[] = $head;
            }
        }
        $usersOunit = $noneEvaluatedBefore[0];

        $answers = json_decode($data['answers']);

        $newEvalID = $this->createEvaluation($eval, $user, $usersOunit);
        $this->setAnswers($newEvalID, $answers);
        $this->calculateEvaluation($newEvalID, $user);
    }

    private function createEvaluation($eval, $user, $ounit)
    {
        $lastEval = EvalEvaluation::where('eval_circular_id', $eval->eval_circular_id)
            ->where('target_ounit_id', $eval->target_ounit_id)->orderby('id', 'desc')->first();
        $lastEvalId = $lastEval->id;

        $newEval = EvalEvaluation::create([
            'eval_circular_id' => $eval->eval_circular_id,
            'target_ounit_id' => $eval->target_ounit_id,
            'evaluator_id' => $user->id,
            'evaluator_ounit_id' => $ounit->id,
            'title' => $eval->title,
            'description' => $eval->description,
            'create_date' => now(),
            'creator_id' => $user->id,
            'parent_id' => $lastEvalId,
            'is_revised' => true,
        ]);
        return $newEval->id;
    }

    public function villagesNotInCirclesOfTarget($circular)
    {
        $allData = $circular->load('variables.evalVariableTargets.oucPropertyValue.oucProperty');
        $variables = $allData->variables;

        $forbiddenVillages = [];
        if ($variables->contains(fn($variable) => method_exists($variable, 'evalVariableTargets') &&
            (!$variable->relationLoaded('evalVariableTargets') || $variable->evalVariableTargets->isEmpty())
        )) {
            return $forbiddenVillages;
        }

        $result = [];

        foreach ($variables as $variable) {
            foreach ($variable->evalVariableTargets as $target) {
                if ($target->relationLoaded('oucPropertyValue') && $target->oucPropertyValue) {
                    $propertyValue = $target->oucPropertyValue->value;
                    $propertyColumnName = $target->oucPropertyValue->oucProperty->column_name;
                    $variableId = $variable->id;
                    $operator = $target->oucPropertyValue->operator;
                    $result[] = [
                        'value' => $propertyValue,
                        'column_name' => $propertyColumnName,
                        'variable_id' => $variableId,
                        'operator' => $operator
                    ];
                }
            }
        }

        $groupedByData = collect($result)->groupBy('variable_id');

        $villagesIds = [];
        $AllVillagesIds = VillageOfc::pluck('id')->toArray();

        $groupedByData = $groupedByData->map(function ($item) use ($AllVillagesIds, &$villagesIds) {
            return $item->map(function ($nestedItem) use ($AllVillagesIds, &$villagesIds) {
                $value = $nestedItem['value'];
                $columnName = $nestedItem['column_name'];
                $operator = $nestedItem['operator'];
                switch ($operator) {
                    case '=':
                        $newOperator = '!=';
                        break;
                    case '>':
                        $newOperator = '<';
                        break;
                    case '<':
                        $newOperator = '>';
                        break;
                }

                $villageIds = VillageOfc::where("$columnName", "$newOperator", $value)->pluck('id')->toArray();

                $villagesIds[] = $villageIds;

                return array_merge($nestedItem, ['villages_ids' => $villageIds]);
            });
        });

        $commonVillages = empty($villagesIds) ? [] : array_intersect(...$villagesIds);

        return collect($commonVillages)->values();
    }

    public function villagesNotInCirclesOfTargetForRemake($circular)
    {
        $allData = $circular->load('variables.evalVariableTargets.oucPropertyValue.oucProperty');
        $variables = $allData->variables;

        $result = [];

        foreach ($variables as $variable) {
            foreach ($variable->evalVariableTargets as $target) {
                if ($target->relationLoaded('oucPropertyValue') && $target->oucPropertyValue) {
                    $propertyValue = $target->oucPropertyValue->value;
                    $propertyColumnName = $target->oucPropertyValue->oucProperty->column_name;
                    $variableId = $variable->id;
                    $operator = $target->oucPropertyValue->operator;
                    $result[] = [
                        'value' => $propertyValue,
                        'column_name' => $propertyColumnName,
                        'variable_id' => $variableId,
                        'operator' => $operator
                    ];
                }
            }
        }

        $groupedByData = collect($result)->groupBy('variable_id');

        $villagesIds = [];

        $groupedByData = $groupedByData->map(function ($item) use (&$villagesIds) {
            return $item->map(function ($nestedItem) use (&$villagesIds) {
                $value = $nestedItem['value'];
                $columnName = $nestedItem['column_name'];
                $operator = $nestedItem['operator'];
                switch ($operator) {
                    case '=':
                        $newOperator = '!=';
                        break;
                    case '>':
                        $newOperator = '<';
                        break;
                    case '<':
                        $newOperator = '>';
                        break;
                }

                $villageIds = VillageOfc::where("$columnName", "$newOperator", $value)->pluck('id')->toArray();

                $villagesIds[] = $villageIds;

                return array_merge($nestedItem, ['villages_ids' => $villageIds]);
            });
        });


        $commonVillages = empty($villagesIds) ? [] : array_intersect(...$villagesIds);


        $evaluatedBefore = EvalEvaluation::where('eval_circular_id', $circular->id)
            ->where('is_revised', false)
            ->orderBy('target_ounit_id', 'asc')
            ->pluck('target_ounit_id')
            ->toArray();


        $NotAllowedOunits = OrganizationUnit::where('unitable_type', VillageOfc::class)
            ->whereIn('id', $evaluatedBefore)
            ->orWhereIn('unitable_id', $commonVillages)
            ->pluck('id')
            ->toArray();

        return collect($NotAllowedOunits)->values();
    }

}
