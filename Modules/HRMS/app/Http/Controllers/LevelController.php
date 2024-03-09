<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Services\LevelService;
use Modules\HRMS\app\Models\Level;

class LevelController extends Controller
{
    public array $data = [];
    protected LevelService $levelService;

    /**
     * @param LevelService $levelService
     */
    public function __construct(LevelService $levelService)
    {
        $this->levelService = $levelService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $result = $this->levelService->index();

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();

        $level = $this->levelService->store($data);

        if ($level instanceof \Exception) {
            return response()->json(['message'=>'خطا در ایجاد سطح جدید'],500);

        }

        return response()->json($level);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $result = $this->levelService->show($id);
        if (is_null($result)) {
            return response()->json(['message'=>'موزدی یافت نشد'],404);

        }
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $data = $request->all();

        $level = $this->levelService->update($data,$id);

        if ($level instanceof \Exception) {
            return response()->json(['message'=>'خطا در بروزرسانی سطح '],500);

        }

        return response()->json(['message'=>'بروزرسانی سطح با موفقیت انجام شد']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $result = Level::findOrFail($id);

        if (is_null($result)) {
            return response()->json(['message'=>'موزدی یافت نشد'],404);

        }

        $status = Level::GetAllStatuses()->where('name', '=', 'غیرفعال')->first();

        $result->status_id = $status->id;
        $result->save();

        return response()->json(['message'=>'سطح با موفقیت حذف شد']);
    }
}
