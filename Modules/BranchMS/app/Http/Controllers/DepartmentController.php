<?php

namespace Modules\BranchMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;
use Modules\BranchMS\app\Models\Branch;
use Modules\BranchMS\app\Models\Department;

class DepartmentController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $branch = Branch::with('departments')->findOrFail($request->branchID);

        if (is_null($branch)) {
            return response()->json([
                'error' => 'دپارتمانی با این مشخصات برای'
            ], 404);

        }
        return response()->json($branch->departments);

    }

    public function indexActive(Request $request): JsonResponse
    {
        $activeDepartments = Branch::with(['departments' => function ($query) {
            $query->whereHas('status', function ($statusQuery) {
                $statusQuery->where('name', 'فعال');
            });
        }])->find($request->branchID)->departments;


        if (is_null($activeDepartments)) {
            return response()->json([
                'error' => 'دپارتمانی با این مشخصات برای'
            ], 404);

        }
        return response()->json($activeDepartments);

    }

    /**
     * @authenticated
     * @bodyparams branchID int required the id of branch to insert department for.
     * @bodyparams departmentName string required the title of the department to insert.
     * @bodyparams statusID int required the status of the address, default is active
     *
     *
     */
    public function store(Request $request): JsonResponse
    {
        $branch = Branch::findOrFail($request->branchID);
        if (is_null($branch)) {
            return response()->json([
                'error' => 'شعبه ای با این مشخصات یافت نشد'
            ], 404);
        }
        try {
            \DB::beginTransaction();
            $department = new Department();
            $status = Branch::GetAllStatuses()->where('name', '=', 'فعال')->first();
            $department->name = $request->departmentName;
            $department->branch_id = $branch->id;
            $department->status_id = $status->id;

            $department->save();
            \DB::commit();
            return response()->json([
                'message' => 'دپارتمان با موفقیت ایجاد شد',
                'departmentID' => $department->id,
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json('error', 500);
        }


    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $department = Department::with('status', 'sections.status', 'branch.status')->findOrFail($id);
        if (is_null($department)) {
            return response()->json([
                'error' => 'دپارتمانی با این مشخصات یافت نشد'
            ], 404);
        }

        if (\Str::contains(\request()->route()->uri(), 'departments/edit/{id}')) {
            $statuses = Branch::GetAllStatuses();
//            $branches = Branch::all();
//            $branches = Branch::whereHas('status', function ($query) {
//                $query->where('name', 'فعال')
//                    ->where('branch_status.create_date', function($subQuery) {
//                        $subQuery->selectRaw('MAX(create_date)')
//                            ->from('branch_status')
//                            ->whereColumn('branch_id', 'branches.id');
//                    });
//            })->get();
            return response()->json(['department' => $department, 'statuses' => $statuses]);

        }


        return response()->json($department);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $department = Department::findOrFail($id);
        if (is_null($department)) {
            return response()->json([
                'error' => 'دپارتمانی با این مشخصات یافت نشد'
            ], 404);
        }
        try {


            $department->name = $request->departmentName;
            $department->branch_id = $request->branchID;
            $department->status_id = $request->statusID;

            $department->save();

            return response()->json([
                'message' => 'دپارتمان با موفقیت ویرایش شد',
            ]);
        } catch (Exception $e) {
            return response()->json('error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $department = Department::findOrFail($id);
        if (is_null($department)) {
            return response()->json([
                'error' => 'دپارتمانی با این مشخصات یافت نشد'
            ], 404);
        }

        $status = Branch::GetAllStatuses()->where('name', '=', 'غیرفعال')->first();
        $department->status_id = $status->id;
        $department->save();

        return response()->json([
            'message' => 'بخش با موفقیت حذف شد',
        ]);
    }
}
