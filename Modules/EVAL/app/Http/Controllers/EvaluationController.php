<?php

namespace Modules\EVAL\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\EVAL\app\Http\Traits\EvaluationTrait;
use Modules\EVAL\app\Models\EvalEvaluation;
use Modules\EvalMS\app\Models\Evaluation;

class EvaluationController extends Controller
{
    use EvaluationTrait;
    public function preViewEvaluation($id)
    {
        $waitToDoneStatus = $this->evaluationWaitToDoneStatus();
        $eval = $this->indexForOnlyOneStatus($id , $waitToDoneStatus->id);
        if($eval)
        {
            return response() -> json($eval);
        }else{
            return response() -> json(['message' => 'شما دسترسی به این قسمت را ندارید'] , 403);
        }
    }



    public function showEvaluationVariables($id)
    {

    }
}
