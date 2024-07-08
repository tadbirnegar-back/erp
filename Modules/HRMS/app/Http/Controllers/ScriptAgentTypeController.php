<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\HRMS\app\Http\Traits\ScriptAgentTypesTrait;
use Modules\HRMS\app\Models\ScriptAgentType;

class ScriptAgentTypeController extends Controller
{
    use ScriptAgentTypesTrait;

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $scriptAgentType = $this->createScriptAgentType($data);
            DB::commit();
            return response()->json($scriptAgentType);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ایجاد دسته بندی عوامل حکمی جدید', 'error' => $e->getMessage()], 500);
        }
    }

    public function getScriptAgentType($id)
    {
        $scriptAgentType = ScriptAgentType::findOr($id, function () {
            return response()->json(['message' => 'موردی یافت نشد'], 404);
        });
        return response()->json($scriptAgentType);
    }

    public function listScriptAgentTypes()
    {
        $scriptAgentTypes = $this->getListOfScriptAgentTypes();
        return response()->json($scriptAgentTypes);
    }

    public function update(Request $request, $id)
    {
        $scriptAgentType = ScriptAgentType::findOr($id, function () {
            return response()->json(['message' => 'موردی یافت نشد'], 404);
        });

        DB::beginTransaction();
        try {
            $data = $request->all();
            $updatedScriptAgentType = $this->updateScriptAgentType($scriptAgentType, $data);
            DB::commit();
            return response()->json($updatedScriptAgentType);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در بروزرسانی دسته بندی عوامل حکمی', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $scriptAgentType = ScriptAgentType::findOr($id, function () {
            return response()->json(['message' => 'موردی یافت نشد'], 404);
        });
        DB::beginTransaction();
        try {
            $deleted = $this->deleteScriptAgentType($scriptAgentType);

            DB::commit();
            return response()->json(['message' => 'با موفقیت حذف شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در حذف دسته بندی عوامل حکمی', 'error' => $e->getMessage()], 500);
        }

    }
}
