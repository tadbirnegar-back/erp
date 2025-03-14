<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Services\LevelService;
use Modules\HRMS\app\Http\Traits\LevelTrait;
use Modules\HRMS\app\Models\Level;

class LevelController extends Controller
{
    use LevelTrait;


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
        try {
            \DB::beginTransaction();

            $data = $request->all();
            $level = $this->storeLevel($data);

            if ($level instanceof \Exception) {
                \DB::rollBack();
                return response()->json(['message' => $level->getMessage()], 500);
            }

            \DB::commit();
            return response()->json($level);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'خطا در پردازش درخواست', 'error' => 'error'], 500);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $result = $this->showLevel($id);
        if (is_null($result)) {
            return response()->json(['message' => 'موزدی یافت نشد'], 404);

        }
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            \DB::beginTransaction();
            $level = Level::findOr($id, function () {
                return response()->json(['message' => 'موزدی یافت نشد'], 404);
            });
            $data = $request->all();
            $level = $this->updateLevel($level, $data);

            if ($level instanceof \Exception) {
                \DB::rollBack();
                return response()->json(['message' => 'خطا در بروزرسانی سطح '], 500);
            }

            \DB::commit();
            return response()->json($level);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'خطا در پردازش درخواست', 'error' => 'error'], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $result = Level::findOrFail($id);

        if (is_null($result)) {
            return response()->json(['message' => 'موزدی یافت نشد'], 404);

        }

        $status = $this->inactiveLevelStatus();

        $result->status_id = $status->id;
        $result->save();

        return response()->json(['message' => 'سطح با موفقیت حذف شد']);
    }
}
