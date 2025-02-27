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
use Modules\EVAL\app\Resources\EvaluationRevisedResource;
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
                $variables = $this->showVariables($village, $id);
                $variableResource = SendVariablesResource::collection($variables);
                return ['variables' => $variableResource, 'message' => 'سوالات ارزیابی شما با موفقیت ساخته شد', 'count' => $variables->count()];
            } else {
                return response()->json(['message' => "شما دهیار مورد نظر برای ارزیابی نیستید"], 403);
            }
        } else {
            return response()->json(['message' => 'شما دسترسی به این قسمت را ندارید'], 403);
        }


    }

    public function evaluationDone($id, Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $answers = json_decode($request->answers);
            $this->setAnswers($id, $answers);
            $this->calculateEvaluation($id, $user);
            DB::commit();
            return response()->json(['message' => 'ارزیابی شما با موفقیت ثبت شد.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => "متاسفانه ارزیابی شما ثبت نشد."], 403);
        }
    }

    public function revisingEvaluationPreData($id)
    {
        $user = Auth::user();
        $eval = EvalEvaluation::find($id);
        $preDatas = $this->showPreDatas($eval , $user);

        $resource = new EvaluationRevisedResource($preDatas);

        if(collect($resource) == collect([])){
            return response()->json(['message' => "شما در حال حاضر هیچ ارزیابی ندارید"], 403);
        }else{
            return response()->json(['revisersData' => $resource , 'ounits' => $preDatas['ounits']]);
        }
    }

    public function revising(Request $request , $id)
    {
        try {
            DB::beginTransaction();
            $user = User::find(2174);
            $eval = EvalEvaluation::find($id);

            $isPersonAllowToEvaluate = $this->isPersonAllowToEvaluate($user , $eval);

            if(!$isPersonAllowToEvaluate){
                return response()->json(['message' => "شما قبلا در ارزیابی ابن دهیار شرکت کرده اید"], 403);
            }

            $data = $request->all();

            $this->evaluate($eval , $data , $user);

            DB::commit();
            return response()->json(['message' => 'باز ارزیابی شما با موفقیت ثبت گردید'], 200);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }
}
