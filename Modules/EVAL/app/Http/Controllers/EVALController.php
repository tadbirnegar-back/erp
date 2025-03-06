<?php

namespace Modules\EVAL\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\EVAL\app\Models\EvalCircular;
use Modules\EVAL\app\Models\EvalCircularIndicator;
use Modules\EVAL\app\Models\EvalCircularSection;
use Modules\EVAL\app\Models\EvalCircularVariable;
use Modules\EVAL\app\Models\EvalEvaluation;
use Modules\EVAL\app\Models\EvalEvaluationAnswer;

class EVALController extends Controller
{
    public function mergeOldEvaluationToNew()
    {
        try {
            DB::beginTransaction();
            //Store Circular
            $circular = EvalCircular::create([
                'title' => 'ارزیابی عملکرد دهیاری ها سال 1402',
                'description' => 'ارزیابی عملکرد دهیاری ها سال 1402',
                'maximum_value' => 100,
                'file_id' => 1,
                'creator_id' => 1905,
                'create_date' => now(),
                'expired_date' => Carbon::now()->addYears(1),
            ]);


            //Store Section
            //----get section before
            $sections = DB::table('eval_parts')->get();



            //----insert new section
            $NewSections = [];
            foreach ($sections as $section) {
                $NewSections[] = EvalCircularSection::create([
                    'title' => $section->title,
                    'eval_circular_id' => $circular->id
                ]);
            }


            foreach ($NewSections as $NewSection) {
                $sectionBefore = DB::table('eval_parts')->where('title', $NewSection->title)->first();
                $indicators = DB::table('evaluator_indicators')->where('eval_part_id', $sectionBefore->id)->get();
                foreach ($indicators as $indicator) {
                    EvalCircularIndicator::create([
                        'title' => $indicator->title,
                        'coefficient' => $indicator->coefficient,
                        'eval_circular_section_id' => $NewSection->id,
                    ]);
                }
            }


            //store variables
            $variables = DB::table('eval_parameters')->get();
            foreach ($variables as $variable) {
                $oldIndicator = DB::table('evaluator_indicators')->where('id', $variable->eval_indicator_id)->first();
                $newIndicator = EvalCircularIndicator::where('title', $oldIndicator->title)->first();
                $dayereShumul = $variable->circle_of_inclusion;
                EvalCircularVariable::create([
                    'title' => $variable->title,
                    'weight' => $variable->weight,
                    'eval_circular_indicator_id' => $newIndicator->id,
                    'description' => $variable->description . '(' . $dayereShumul . ')',
                ]);
            }


            DB::commit();

            return response()->json($circular);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }


    }

    public function fillTheAnswers(Request $request, $lastEvaluationID)
    {

        //Fill the evaluations
        $evals = DB::table('evaluators')
            ->where('evaluation_id', 1)
            ->get();


        foreach ($evals as $eval) {
            $targetOunitId = $eval->organization_unit_id;
            $newEval = EvalEvaluation::
            where('target_ounit_id', $targetOunitId)
                ->where('eval_circular_id', 20)
                ->first();


            if ($newEval) {
                $newEval->sum = $eval->sum;
                $newEval->average = $eval->average;
                $newEval->save();
            }
        }

        $oldAnswers = DB::table('eval_parameter_answers')->get();

        $insertData = [];

        foreach ($oldAnswers as $oldAnswer) {
            // Fetch related evaluator
            $oldEval = DB::table('evaluators')->where('id', $oldAnswer->evaluator_id)->first();

            if (!$oldEval) continue;

            // Fetch new evaluation based on organization unit
            $newEval = EvalEvaluation::where('target_ounit_id', $oldEval->organization_unit_id)->first();

            if (!$newEval) continue;

            // Fetch the parameter title
            $oldParameters = DB::table('eval_parameters')->where('id', $oldAnswer->eval_parameter_id)->first();

            if (!$oldParameters) continue;

            // Find new variable based on title
            $newVariable = EvalCircularVariable::where('title', $oldParameters->title)->first();

            if (!$newVariable) continue;

            // Prepare the data for bulk insert
            $insertData[] = [
                'value' => $oldAnswer->value,
                'eval_circular_variables_id' => $newVariable->id,
                'eval_evaluation_id' => $newEval->id,
            ];
        }

// Perform bulk insert
        if (!empty($insertData)) {
            EvalEvaluationAnswer::insert($insertData);
        }

        return response()->json(['message' => 'باز ارزیابی شما با موفقیت ثبت شد.']);

    }
}
