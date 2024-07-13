<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Traits\ScriptTypeTrait;
use Modules\HRMS\app\Models\ConfirmationTypeScriptType;
use Modules\HRMS\app\Models\ScriptType;

class ScriptTypeController extends Controller
{
    use ScriptTypeTrait;

    public function index()
    {
        $result = $this->getListOfScriptTypes();
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $scriptType = $this->createScriptType($data);
            DB::commit();
            return response()->json($scriptType);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خظا در ثبت نوع حکم', 'error' => $e->getMessage()], 500);
        }

    }

    public function update(Request $request, $id)
    {
        $scriptType = ScriptType::findOr($id, function () {
            return response()->json(['message' => 'نوع حکم یافت نشد'], 404);
        });
        try {
            DB::beginTransaction();

            $data = $request->all();
            $scriptType = $this->updateScriptType($scriptType, $data);

            $deletedSTCTids = json_decode($data['deletedSTCTids'], true);
            $scTypes = ConfirmationTypeScriptType::whereIntegerInRaw('id', $deletedSTCTids)->delete();
            DB::commit();
            return response()->json($scriptType);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خظا در ویرایش نوع حکم', 'error' => $e->getMessage()], 500);
        }

    }

    public function destroy($id)
    {
        $scriptType = ScriptType::findOr($id, function () {
            return response()->json(['message' => 'نوع حکم یافت نشد'], 404);
        });
        try {
            DB::beginTransaction();
            $this->deleteScriptType($scriptType);
            DB::commit();
            return response()->json(['message' => 'نوع حکم حذف شد']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خظا در حذف نوع حکم', 'error' => $e->getMessage()], 500);
        }
    }

}
