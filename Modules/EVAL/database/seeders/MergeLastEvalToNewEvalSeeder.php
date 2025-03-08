<?php

namespace Modules\EVAL\database\seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\EVAL\app\Models\EvalCircular;
use Modules\EVAL\app\Models\EvalCircularVariable;
use Modules\EVAL\app\Models\EvalEvaluation;
use Modules\EVAL\app\Models\EvalEvaluationAnswer;

class MergeLastEvalToNewEvalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
//        DB::table('evaluators')
//            ->where('evaluation_id', 1)
//            ->orderBy('id')
//            ->chunk(500, function ($evals) {
//                foreach ($evals as $eval) {
//                    $targetOunitId = $eval->organization_unit_id;
//                    $newEval = EvalEvaluation::where('target_ounit_id', $targetOunitId)
//                        ->where('eval_circular_id', 3)
//                        ->first();
//
//                    if ($newEval) {
//                        $newEval->update([
//                            'sum' => $eval->sum,
//                            'average' => $eval->average
//                        ]);
//                    }
//                }
//            });

        DB::table('eval_parameters')
            ->orderBy('id')
            ->chunkById(500, function ($oldParameters) {
                $parameterIds = $oldParameters->pluck('id');
                $titles = $oldParameters->pluck('title')->unique();

                // Prefetch all related data
                $answers = DB::table('eval_parameter_answers')
                    ->whereIn('eval_parameter_id', $parameterIds)
                    ->cursor();

                $evaluatorIds = $answers->pluck('evaluator_id')->unique()->filter();
                $evaluators = DB::table('evaluators')
                    ->whereIn('id', $evaluatorIds)
                    ->pluck('organization_unit_id', 'id');

                $organizationUnitIds = $evaluators->values()->unique()->filter();
                $evaluations = EvalEvaluation::whereIn('target_ounit_id', $organizationUnitIds)
                    ->pluck('id', 'target_ounit_id');

                $variables = EvalCircularVariable::whereIn('title', $titles)
                    ->pluck('id', 'title');

                // Array to store missing organization unit IDs
                $missingOrgUnits = [];

                // Process in smaller batches
                $insertData = [];
                $counter = 0;

                foreach ($answers as $answer) {
                    if (!$orgUnitId = $evaluators[$answer->evaluator_id] ?? null) {
                        continue;
                    }

                    if (!$evaluationId = $evaluations[$orgUnitId] ?? null) {
                        // Track missing organization unit
                        $missingOrgUnits[$orgUnitId] = true;
                        continue;
                    }

                    $parameter = $oldParameters->firstWhere('id', $answer->eval_parameter_id);
                    if (!$variableId = $variables[$parameter->title] ?? null) {
                        continue;
                    }

                    $insertData[] = [
                        'value' => $answer->value,
                        'eval_circular_variables_id' => $variableId,
                        'eval_evaluation_id' => $evaluationId,
                    ];

                    // Insert in chunks of 500
                    if (++$counter % 500 === 0) {
                        EvalEvaluationAnswer::insert($insertData);
                        $insertData = [];
                        gc_collect_cycles(); // Force garbage collection
                    }
                }

                // Insert remaining records
                if (!empty($insertData)) {
                    EvalEvaluationAnswer::insert($insertData);
                }

                // Explicitly unset variables
                unset($answers, $evaluators, $evaluations, $variables, $insertData);
                gc_collect_cycles();

                // Log missing organization units
                if (!empty($missingOrgUnits)) {
                    $this->command->info('Organization Units not in new Eval: ' . implode(', ', array_keys($missingOrgUnits)));
                }

            }, $column = 'id');

        $this->command->info('Evaluation data seeded successfully.');

//        DB::table('eval_parameter_answers')
//            ->orderBy('id')
//            ->chunk(500, function ($oldAnswers) {
//                $insertData = [];
//
//                foreach ($oldAnswers as $oldAnswer) {
//                    $oldEval = DB::table('evaluators')->where('id', $oldAnswer->evaluator_id)->first();
//
//                    if (!$oldEval) continue;
//
//                    $newEval = EvalEvaluation::where('target_ounit_id', $oldEval->organization_unit_id)->first();
//                    if (!$newEval) continue;
//
//                    $oldParameters = DB::table('eval_parameters')->where('id', $oldAnswer->eval_parameter_id)->first();
//                    if (!$oldParameters) continue;
//
//                    $newVariable = EvalCircularVariable::where('title', $oldParameters->title)->first();
//                    if (!$newVariable) continue;
//
//                    $insertData[] = [
//                        'value' => $oldAnswer->value,
//                        'eval_circular_variables_id' => $newVariable->id,
//                        'eval_evaluation_id' => $newEval->id,
//                    ];
//                }
//
//                if (!empty($insertData)) {
//                    EvalEvaluationAnswer::insert($insertData);
//                }
//            });

//        $this->command->info('Evaluation data seeded successfully.');
    }

}
