<?php

namespace Modules\EVAL\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\User;
use Modules\EVAL\app\Http\Traits\EvaluationTrait;
use Modules\EVAL\app\Models\EvalEvaluation;
use Modules\EVAL\app\Resources\SendVariablesResource;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\OUnitMS\app\Models\OrganizationUnit;

class EvaluationController extends Controller
{
    use EvaluationTrait;

    public function preViewEvaluation($id)
    {
        $waitToDoneStatus = $this->evaluationWaitToDoneStatus();
        $eval = $this->indexForOnlyOneStatus($id, $waitToDoneStatus->id);
        if ($eval) {
            return response()->json($eval);
        } else {
            return response()->json(['message' => 'شما دسترسی به این قسمت را ندارید'], 403);
        }
    }


    public function evaluationStart($id)
    {
        $waitToDoneStatus = $this->evaluationWaitToDoneStatus();
        $eval = $this->indexForOnlyOneStatus($id, $waitToDoneStatus->id);
        if ($eval) {
            $user = Auth::user();
            $user->load('activeDehyarRcs');
            //check if user has same ounit as evaluation
            $ounitsOfDehyari = $user->activeDehyarRcs->pluck('organization_unit_id')->toArray();
            $evaluationOunit = $eval->target_ounit_id;
            if (in_array($evaluationOunit, $ounitsOfDehyari)) {
                $village = OrganizationUnit::find($evaluationOunit);
                $village->load('unitable');
                $variables = $this->showVariables($village,$id);
                $variableResource =  SendVariablesResource::collection($variables);
                return ['variables' => $variableResource , 'message' => 'سوالات ارزیابی شما با موفقیت ساخته شد' , 'count' => $variables->count()];
            }else{
                return response()->json(['message' => "شما دهیار مورد نظر برای ارزیابی نیستید"], 403);
            }
        } else {
            return response()->json(['message' => 'شما دسترسی به این قسمت را ندارید'], 403);
        }


    }

    public function evaluationDone($id , Request $request)
    {
        try {
            DB::beginTransaction();
            $answers = json_decode($request->answers);
            $this->setAnswers($id, $answers);
            $this -> calculateEvaluation($id);
            DB::commit();
            return response()->json(['message' => 'ارزیابی شما با موفقیت ثبت شد.']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['message' => "متاسفانه ارزیابی شما ثبت نشد."], 403);
        }
    }

//    public function evaluationRevising($id)
//    {
//
//    }
}
