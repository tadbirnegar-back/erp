<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Services\SkillService;
use Modules\HRMS\app\Http\Traits\SkillTrait;
use Modules\HRMS\app\Models\Skill;

class SkillController extends Controller
{
    use SkillTrait;
//    public array $data = [];
//    protected SkillService $skillService;
//
//    /**
//     * @param SkillService $skillService
//     */
//    public function __construct(SkillService $skillService)
//    {
//        $this->skillService = $skillService;
//    }


    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $result = $this->skillIndex();

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
     try {
    \DB::beginTransaction();
    $data = $request->all();

    $skill = $this->skillStore($data);
    if ($skill instanceof \Exception) {
        return response()->json(['message' => 'خطا در ایجاد مهارت جدید'], 500);
    }

    \DB::commit();
    return response()->json($skill);
} catch (\Exception $e) {
    \DB::rollBack();
    return response()->json(['message' => 'خطا در پردازش درخواست', 'error' => $e->getMessage()], 500);
}
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $skill = $this->skillShow($id);

        if (is_null($skill)) {
            return response()->json(['message'=>'موزدی یافت نشد'],404);

        }

        return response()->json($skill);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $result = Skill::findOr($id,function (){
            return response()->json(['message'=>'موزدی یافت نشد'],404);
        });
        $data = $request->all();
        try {
            \DB::beginTransaction();
            $level = $this->skillUpdate($data,$result);

            \DB::commit();
            return response()->json($level);
        }catch (\Exception $e){
            \DB::rollBack();
            return response()->json(['message'=>'خطا در بروزرسانی مهارت ','error'=>$e->getMessage()],500);
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $result = Skill::findOr($id,function (){
            return response()->json(['message'=>'موزدی یافت نشد'],404);
        });

        $status = $this->inactiveSkillStatus();

        $result->status_id = $status->id;
        $result->save();

        return response()->json(['message'=>'مهارت با موفقیت حذف شد']);
    }
}
