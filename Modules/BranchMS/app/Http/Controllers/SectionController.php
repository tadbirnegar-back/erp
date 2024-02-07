<?php

namespace Modules\BranchMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\BranchMS\app\Models\Branch;
use Modules\BranchMS\app\Models\Department;
use Modules\BranchMS\app\Models\Section;

class SectionController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $department = Department::with('sections')->findOrFail($request->departmentID);
        if (is_null($department)) {
            return response()->json([
                'error' => 'دپارتمانی با این مشخصات یافت نشد'
            ], 404);
        }

        return response()->json($department->sections);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {

        $department = Department::findOrFail($request->departmentID);
        if (is_null($department)) {
            return response()->json([
                'error' => 'دپارتمانی با این مشخصات یافت نشد'
            ], 404);
        }
        try {
            \DB::beginTransaction();
            $section = new Section();
            $status = Branch::GetAllStatuses()->where('name', '=', 'فعال')->first();

            $section->name = $request->sectionName;
            $section->department_id = $department->id;
            $section->status_id = $status->id;

            $section->save();

            \DB::commit();
            return response()->json([
                'message' => 'بخش با موفقیت ایجاد شد',
                'departmentID' => $department->id,
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($e->getMessage(),500);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $section = Section::with('status', 'department.status', 'branch.status')->findOrFail($id);
        if (is_null($section)) {
            return response()->json([
                'error' => 'بخشی با این مشخصات یافت نشد'
            ], 404);
        }

        if (\Str::contains(\request()->route()->uri(), 'sections/edit/{id}')) {
            $statuses = Branch::GetAllStatuses();
//            $departments = Department::where('branch_id','=',$section->department->branch_id);
//            $departments = Department::with('status')->where('statuses.name','=','فعال')->get();
            return response()->json(['sections'=>$section,'statuses'=> $statuses]);

        }


        return response()->json($section);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $section = Section::findOrFail($id);
        if (is_null($section)) {
            return response()->json([
                'error' => 'بخشی با این مشخصات یافت نشد'
            ], 404);
        }
        $section->name = $request->sectionName;
        $section->department_id = $request->departmentID;
        $section->status_id = $request->statusID;

        $section->save();
        return response()->json([
            'message' => 'یخش با موفقیت ویرایش شد',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $section = Section::findOrFail($id);
        if (is_null($section)) {
            return response()->json([
                'error' => 'بخشی با این مشخصات یافت نشد'
            ], 404);
        }

        $status = Branch::GetAllStatuses()->where('name', '=', 'غیرفعال')->first();
        $section->status_id = $status->id;
        $section->save();

        return response()->json([
            'message' => 'بخش با موفقیت حذف شد',
        ]);
    }
}
