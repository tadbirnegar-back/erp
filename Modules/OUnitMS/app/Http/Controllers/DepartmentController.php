<?php

namespace Modules\OUnitMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Modules\OUnitMS\app\Http\Traits\OrganizationUnitTrait;
use Modules\OUnitMS\app\Models\Department;
use Modules\OUnitMS\app\Models\OrganizationUnit;

class DepartmentController extends Controller
{
    use OrganizationUnitTrait;

    public function index(Request $request)
    {
        $searchTerm = $request->name ?? null;
        $perPage = $request->perPage ?? 10;
        $page = $request->pageNum ?? 1;
        $ounitID = $request->orginizationUnit ?? null;


        $departemans = $this->departmentIndex($searchTerm, $ounitID, $perPage, $page);

        return response()->json($departemans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();
            $state = $this->departmentStore($data);
            DB::commit();

            return response()->json($state);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ایجاد استانداری جدید'], 500);
        }
    }

    public function show($id)
    {

        $departemans = OrganizationUnit::where('unitable_type', Department::class)
            ->findOrFail($id);

        return $departemans;
    }

    public function update(Request $request, $id)
    {
        // Validate the input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255', // Ensure 'name' is provided and valid
        ]);

        try {
            DB::beginTransaction();
            $dep = OrganizationUnit::find($id);
            if (is_null($dep)) {
                return response()->json([
                    'message' => 'دپارتمان یافت نشد',
                ], 404);
            }
            $dep->update($validatedData);

            DB::commit();
            return response()->json([
                'message' => 'دپارتمان با موفقیت ویرایش شد',
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'خطا در بروزرسانی دپارتمان',
            ], 500);

        }

    }

    public function destroy($id)
    {
        //
    }
}
