<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Http\Traits\PositionTrait;
use Modules\HRMS\app\Models\Position;
use Modules\OUnitMS\app\Models\OrganizationUnit;

class PositionController extends Controller
{
    use PositionTrait;

    public array $data = [];
//    protected PositionService $positionService;

//    /**
//     * @param PositionService $positionService
//     */
//    public function __construct(PositionService $positionService)
//    {
//        $this->positionService = $positionService;
//    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $result = $this->positionIndex();

        return response()->json($result);
    }

    public function getByOrganizationUnit(Request $request): JsonResponse
    {
        $ounit = OrganizationUnit::with('positions.levels')->findOr($request->ounitID, function () {
            return response()->json(['message' => 'واحد سازمانی یافت نشد'], 404);
        });

        return response()->json($ounit->positions);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {

        try {
            \DB::beginTransaction();


            $data = $request->all();

            $pos = $this->positionStore($data);

            \DB::commit();

            return response()->json($pos);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'خطا در ایجاد سمت', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $result = $this->positionShow($id);
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
        $result = Position::findOr($id, function () {
            return response()->json(['message' => 'موزدی یافت نشد'], 404);
        });

        try {
            \DB::beginTransaction();


            $data = $request->all();

            $pos = $this->positionUpdate($data, $result);

            \DB::commit();

            return response()->json($pos);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'خطا در بروزرسانی سمت', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $result = Position::findOr($id, function () {
            return response()->json(['message' => 'موزدی یافت نشد'], 404);
        });

        $status = $this->positionDelete($result);

        return response()->json(['message' => 'سمت با موفقیت حذف شد']);
    }
}
