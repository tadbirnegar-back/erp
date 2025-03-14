<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Traits\HireTypeTrait;
use Modules\HRMS\app\Models\HireType;

class HireTypeController extends Controller
{
    use HireTypeTrait;

    public array $data = [];

    public function index()
    {
        $results = $this->getAllHireTypes();

        return response()->json($results);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            \DB::beginTransaction();

            $this->data = $request->all();
            $hireType = $this->createHireType($this->data);

            \DB::commit();
            return response()->json($hireType);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'خطا در ایجاد نوع استخدام', 'error' => 'error'], 500);
        }


    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            \DB::beginTransaction();
            $hireType = HireType::findOr($id, function () {
                return response()->json(['message' => 'یافت نشد']);
            });
            $this->data = $request->all();
            $hireType = $this->updateHireType($hireType, $this->data);

            \DB::commit();
            return response()->json($hireType);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'خطا در بروزرسانی نوع استخدام', 'error' => 'error'], 500);
        }

    }

    public function destroy($id)
    {
        $job = HireType::findOr($id, function () {
            return response()->json(['message' => 'موزدی یافت نشد'], 404);
        });
        $job = $this->deleteHireType($job);
        return response()->json(['message' => ' با موفقیت حذف شد']);
    }
}
