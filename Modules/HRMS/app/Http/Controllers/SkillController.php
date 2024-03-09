<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Services\SkillService;
use Modules\HRMS\app\Models\Skill;

class SkillController extends Controller
{
    public array $data = [];
    protected SkillService $skillService;

    /**
     * @param SkillService $skillService
     */
    public function __construct(SkillService $skillService)
    {
        $this->skillService = $skillService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $result = $this->skillService->index();

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();

        $skill = $this->skillService->store($data);
        if ($skill instanceof \Exception) {
            return response()->json(['message'=>'خطا در ایجاد مهارت جدید'],500);

        }
        return response()->json($skill);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $skill = $this->skillService->show($id);

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
        $data = $request->all();

        $level = $this->skillService->update($data,$id);

        if ($level instanceof \Exception) {
            return response()->json(['message'=>'خطا در بروزرسانی مهارت '],500);

        }

        return response()->json(['message'=>'بروزرسانی مهارت با موفقیت انجام شد']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $result = Skill::findOrFail($id);

        if (is_null($result)) {
            return response()->json(['message'=>'موزدی یافت نشد'],404);

        }

        $status = Skill::GetAllStatuses()->where('name', '=', 'غیرفعال')->first();

        $result->status_id = $status->id;
        $result->save();

        return response()->json(['message'=>'مهارت با موفقیت حذف شد']);
    }
}
