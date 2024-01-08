<?php

namespace Modules\BranchMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\BranchMS\app\Models\Branch;
use Modules\BranchMS\app\Models\Department;

class DepartmentController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
//    public function index(Request $request): JsonResponse
//    {
//        $branch = Branch::findOrFail($request->branchID);
//
//        if (is_null($branch)) {
//            return response()->json([
//                'error' => 'دپارتمانی با این مشخصات برای'
//            ], 404);
//
//        return response()->json($this->data);
//    }

    /**
     * @authenticated
     * @bodyparams branchID int required the id of branch to insert department for.
     * @bodyparams departmentName string required the title of the department to insert.
     * @bodyparams statusID int the status of the address, default is active
     *
     *
     */
    public function store(Request $request): JsonResponse
    {
        $branch = Branch::with('departments')->findOrFail($request->branchID);
        if (is_null($branch)) {
            return response()->json([
                'error' => 'شعبه ای با این مشخصات یافت نشد'
            ], 404);
        }

        $department = new Department();

        $department->name=$request->departmentName;
        $department->branch_id = $branch->id;
        $department->status_id = $request->status_id;

        $department->save();

        return response()->json([
            'message' => 'دپارتمان با موفقیت ایجاد شد',
            'departmentID' => $department->id,
        ], 200);
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        //

        return response()->json($this->data);
    }
}
