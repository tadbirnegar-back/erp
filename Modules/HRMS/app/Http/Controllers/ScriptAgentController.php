<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Traits\ScriptAgentTrait;

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
            return response()->json(['message' => 'خطا در ایجاد عامل حکمی','error'=>$e->getMessage()], 500);
        }
    }

}
