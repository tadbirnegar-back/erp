<?php

namespace Modules\EVAL\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Bus\Batch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Models\User;
use Modules\EVAL\app\Http\Traits\CircularTrait;
use Modules\EVAL\app\Http\Traits\EvaluationTrait;
use Modules\EVAL\app\Jobs\MakeEvaluationFormJob;
use Modules\EVAL\app\Models\EvalCircular;
use Modules\EVAL\app\Models\EvalCircularStatus;
use Modules\EVAL\app\Models\EvalEvaluation;
use Modules\EVAL\app\Resources\EvaluationRevisedResource;
use Modules\EVAL\app\Resources\SendVariablesResource;
use Modules\EvalMS\app\Models\Evaluation;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\VillageOfc;

class EvaluationController extends Controller
{
    use EvaluationTrait , CircularTrait;

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
            return response()->json(['message' => 'ارزیابی شما با موفقیت ثبت شد.'] , 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => "متاسفانه ارزیابی شما ثبت نشد."], 403);
        }
    }

    public function revisingEvaluationPreData($id)
    {
        $user = Auth::user();
        $eval = EvalEvaluation::find($id);
        $preDatas = $this->showPreDatas($eval, $user);

        $resource = new EvaluationRevisedResource($preDatas);

        if (collect($resource) == collect([])) {
            return response()->json(['message' => "شما در حال حاضر هیچ ارزیابی ندارید"], 403);
        } else {
            return response()->json(['revisersData' => $resource, 'ounits' => $preDatas['ounits']]);
        }
    }

    public function revising(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $eval = EvalEvaluation::find($id);

            $isPersonAllowToEvaluate = $this->isPersonAllowToEvaluate($user, $eval);

            if (!$isPersonAllowToEvaluate) {
                return response()->json(['message' => "شما قبلا در ارزیابی ابن دهیار شرکت کرده اید"], 403);
            }

            $data = $request->all();

            $this->evaluate($eval, $data, $user);

            DB::commit();
            return response()->json(['message' => 'باز ارزیابی شما با موفقیت ثبت گردید'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function makeEvaluationForm($id)
    {
        try {
            DB::beginTransaction();
            $circular = EvalCircular::findOrFail($id);
            $user = Auth::user();
            $waitToDoneStatus = $this->evaluationWaitToDoneStatus()->id;

            $eliminatedVillagesQuery = $this->villagesNotInCirclesOfTarget($circular);

            $allJobs = [];

            OrganizationUnit::where('unitable_type', VillageOfc::class)
                ->join('village_ofcs as village_alias', 'village_alias.id', '=', 'organization_units.unitable_id')
                ->where('village_alias.hasLicense', true)
                ->whereIntegerNotInRaw('unitable_id', $eliminatedVillagesQuery)
                ->chunkById(100, function ($chunk) use ($circular, $user, $waitToDoneStatus, &$allJobs) {
                    $batch = [];
                    foreach ($chunk as $organizationUnit) {
                        $delayInSeconds = 10 + rand(1, 45);
                        $batch[] = (new MakeEvaluationFormJob(
                            $circular,
                            $organizationUnit->unitable_id,
                            $user->id,
                            $waitToDoneStatus
                        ))->delay(now()->addSeconds($delayInSeconds));
                    }
                    $allJobs[] = $batch;
                }, 'unitable_id');


            foreach ($allJobs as $jobBatch) {
                Bus::batch($jobBatch)
                    ->name("DispatchEvaluationFormJob")
                    ->onQueue('default')
                    ->dispatchAfterResponse();
            }

            $circularStatus = $this->notifiedCircularStatus();
            EvalCircularStatus::create([
                'status_id' => $circularStatus->id,
                'eval_circular_id' => $circular->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
            return response()->json(['message' => 'بخشنامه ابلاغ گردید'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'متاسفانه بخشنامه ابلاغ نگردید'], 404);
        }

    }

    public function remakeEvaluationForm($id)
    {
        try {
            DB::beginTransaction();
            $circular = EvalCircular::findOrFail($id);
            $user = Auth::user();
            $waitToDoneStatus = $this->evaluationWaitToDoneStatus()->id;

            $eliminatedVillagesQuery = $this->villagesNotInCirclesOfTargetForRemake($circular);
            $allJobs = [];

            OrganizationUnit::where('unitable_type', VillageOfc::class)
                ->join('village_ofcs as village_alias', 'village_alias.id', '=', 'organization_units.unitable_id')
                ->where('village_alias.hasLicense', true)
                ->whereIntegerNotInRaw('unitable_id', $eliminatedVillagesQuery)
                ->chunkById(100, function ($chunk) use ($circular, $user, $waitToDoneStatus, &$allJobs) {
                    $batch = [];
                    foreach ($chunk as $organizationUnit) {
                        $delayInSeconds = 10 + rand(1, 45);
                        $batch[] = (new MakeEvaluationFormJob(
                            $circular,
                            $organizationUnit->unitable_id,
                            $user->id,
                            $waitToDoneStatus
                        ))->delay(now()->addSeconds($delayInSeconds));
                    }
                    $allJobs[] = $batch;
                }, 'unitable_id');


            foreach ($allJobs as $jobBatch) {
                Bus::batch($jobBatch)
                    ->name("DispatchEvaluationFormJob")
                    ->onQueue('default')
                    ->dispatchAfterResponse();
            }
            DB::commit();
            return response()->json(['message' => 'بخشنامه ابلاغ گردید'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

    }
}
