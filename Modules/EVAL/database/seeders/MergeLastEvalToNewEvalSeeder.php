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
        // Fetch evaluators for evaluation_id = 1 and update EvalEvaluation records
        DB::table('evaluators')
            ->where('evaluation_id', 1)
            ->orderBy('id')
            ->chunk(500, function ($evals) {
                foreach ($evals as $eval) {
                    $targetOunitId = $eval->organization_unit_id;
                    $newEval = EvalEvaluation::where('target_ounit_id', $targetOunitId)
                        ->where('eval_circular_id', 22)
                        ->first();

                    if ($newEval) {
                        $newEval->update([
                            'sum' => $eval->sum,
                            'average' => $eval->average
                        ]);
                    }
                }
            });

        // Fetch all previous answers in chunks
        DB::table('eval_parameter_answers')
            ->orderBy('id') // Required for chunk()
            ->chunk(500, function ($oldAnswers) {
                $insertData = [];

                foreach ($oldAnswers as $oldAnswer) {
                    $oldEval = DB::table('evaluators')->where('id', $oldAnswer->evaluator_id)->first();
                    if (!$oldEval) continue;

                    $newEval = EvalEvaluation::where('target_ounit_id', $oldEval->organization_unit_id)->first();
                    if (!$newEval) continue;

                    $oldParameters = DB::table('eval_parameters')->where('id', $oldAnswer->eval_parameter_id)->first();
                    if (!$oldParameters) continue;

                    $newVariable = EvalCircularVariable::where('title', $oldParameters->title)->first();
                    if (!$newVariable) continue;

                    $insertData[] = [
                        'value' => $oldAnswer->value,
                        'eval_circular_variables_id' => $newVariable->id,
                        'eval_evaluation_id' => $newEval->id,
                    ];
                }

                // Bulk insert each chunk
                if (!empty($insertData)) {
                    EvalEvaluationAnswer::insert($insertData);
                }
            });

        $this->command->info('Evaluation data seeded successfully.');
    }

}
