<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Traits\ScriptTypeTrait;

class ScriptTypeController extends Controller
{
   use ScriptTypeTrait;

    public function index()
    {
     $result=$this->getListOfScriptTypes();
   }

    public function store(Request $request)
    {
        try {
\DB::beginTransaction();

            $data = $request->all();
            $scriptType = $this->createScriptType($data);
            \DB::commit();
            return response()->json($scriptType);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message'=>'خظا در ثبت نوع حکم','error' => $e->getMessage()], 500);
        }

   }

}
