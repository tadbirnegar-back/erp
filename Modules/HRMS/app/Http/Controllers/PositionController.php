<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Services\PositionService;
use Modules\HRMS\app\Models\Position;

class PositionController extends Controller
{
    public array $data = [];
    protected PositionService $positionService;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $result = $this->positionService->index();

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();

        $position = $this->positionService->store($data);
        if ($position instanceof \Exception) {
            return response()->json(['message'=>'خطا در ایجاد سمت جدید'],500);

        }
        return response()->json($position);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $result = $this->positionService->show($id);
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

        $level = $this->positionService->update($data,$id);

        if ($level instanceof \Exception) {
            return response()->json(['message'=>'خطا در بروزرسانی سمت '],500);

        }

        return response()->json(['message'=>'بروزرسانی سمت با موفقیت انجام شد']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $result = Position::findOrFail($id);

        if (is_null($result)) {
            return response()->json(['message'=>'موزدی یافت نشد'],404);

        }

        $status = Position::GetAllStatuses()->where('name', '=', 'غیرفعال')->first();

        $result->status_id = $status->id;
        $result->save();

        return response()->json(['message'=>'سمت با موفقیت حذف شد']);
    }
}
