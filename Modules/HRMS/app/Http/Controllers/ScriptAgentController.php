<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;
use Modules\HRMS\app\Http\Traits\ScriptAgentTrait;
use Modules\HRMS\app\Models\ScriptAgent;
use Modules\HRMS\app\Models\ScriptAgentCombo;

class ScriptAgentController extends Controller
{
    use ScriptAgentTrait;

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $scriptAgent = $this->createScriptAgent($data);
            DB::commit();
            return response()->json($scriptAgent);
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the exception, e.g., log it or return an error response
            return response()->json(['message' => 'خطا در ایجاد عامل حکمی', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $scriptAgent = ScriptAgent::findOr($id, function () {
            return response()->json(['message' => 'موردی یافت نشد'], 404);
        });

        try {
            DB::beginTransaction();
            $data = $request->all();
            $deletedCombos = json_decode($data['deletedCombos'], true);
            if (!empty($deletedCombos)) {
                $combos = ScriptAgentCombo::whereIntegerInRaw('id', $deletedCombos)->delete();
            }
            $scriptAgent = $this->updateScriptAgent($scriptAgent, $data);


            DB::commit();
            return response()->json($scriptAgent);
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the exception, e.g., log it or return an error response
            return response()->json(['message' => 'خطا در ویرایش عامل حکمی', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $scriptAgent = ScriptAgent::findOr($id, function () {
            return response()->json(['message' => 'موردی یافت نشد'], 404);
        });
        try {
            DB::beginTransaction();
            $result = $this->deleteScriptAgent($scriptAgent);
            DB::commit();
            return response()->json(['message' => 'عامل حکمی با موفقیت حذف شد']);
        } catch (Exception $e) {
            return response()->json(['message' => 'خطا در حذف عامل حکمی', 'error' => $e->getMessage()], 500);
        }
    }

}
