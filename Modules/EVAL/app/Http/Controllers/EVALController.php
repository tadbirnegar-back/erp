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
                $oldIndicator = DB::table('evaluator_indicators')->where('id' , $variable->eval_indicator_id)->first();
                $newIndicator = EvalCircularIndicator::where('title', $oldIndicator->title)->first();
                $dayereShumul = $variable->circle_of_inclusion;
                EvalCircularVariable::create([
                    'title' => $variable->title,
                    'weight' => $variable->weight,
                    'eval_circular_indicator_id' => $newIndicator->id,
                    'description' => $variable->description.'('.$dayereShumul.')',
                ]);
            }



            DB::commit();

            return response()->json($circular);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }


    }
    public function fillTheAnswers($lastEvaluationID)
    {

        //Fill the evaluations
        $evals = DB::table('evaluators')->where('evaluation_id',$lastEvaluationID)->get();

        foreach ($evals as $eval) {
            $targetOunitId = $eval->organization_unit_id;
            $newEval = EvalEvaluation::where('target_ounit_id' , $targetOunitId)->first();
            if($newEval){
                $newEval->sum = $eval->sum;
                $newEval->average = $eval->average;
                $newEval->save();

                $parametersOld = DB::table('eval_parameter_answers')->where('evaluator_id')->get();
            }


        }
        return response()->json($evals);
    }
}
