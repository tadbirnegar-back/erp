<?php

namespace Modules\AAA\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;
use Modules\AAA\app\Models\Role;

class RoleController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $role = Role::with('status', 'section.department.branch')->get();

        return response()->json($role);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
//        return response()->json($request->permissions);

        try {
            $role = new Role();
            $role->name = $request->name;
            $role->status_id = Role::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
            $role->section_id = $request->sectionID;

            $role->save();

            $permissions = json_decode($request->permissions);

            $role->permissions()->sync($permissions);

            return response()->json($role);
        } catch (Exception $exception) {
            return response()->json($exception->getMessage());

        }

    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $role = Role::with(['permissions.moduleCategory', 'status', 'section.department.branch'])->findOrFail($id);


//        $role = Role::with('permissionsWithModuleCategory','status','section.department.branch')->findOrFail($id);

        if (is_null($role)) {
            return response()->json('نقشی با این مشخصات یافت نشد', 404);

        }
        $permissionsGroupedByCategory = $role->permissions
            ->groupBy('moduleCategory.name');

        if (\request()->route()->named('role.edit')) {
            $statuses = Role::GetAllStatuses();

            return response()->json(['role' => ['permissions' => $permissionsGroupedByCategory, 'status' => $role->status, 'detail' => $role], 'statuses' => $statuses]);

        }

        return response()->json(['permissions' => $permissionsGroupedByCategory, 'status' => $role->status, 'detail' => $role]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $role = Role::with('permissions')->findOrFail($id);

        if (is_null($role)) {
            return response()->json('نقشی با این مشخصات یافت نشد', 404);

        }
        try {
            $role->name = $request->name;
            $role->status_id = $request->statusID;
            $role->section_id = $request->sectionID;

            $role->save();

            $permissions = json_decode($request->permissions);

            $role->permissions()->sync($permissions);

            return response()->json('با موفقیت بروزرسانی شد');
        } catch (Exception $exception) {
//            return response()->json($exception->getMessage());
            return response()->json('خطا در بروز رسانی نقش', 500);

        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $role = Role::findOrFail($id);

        if (is_null($role)) {
            return response()->json('نقشی با این مشخصات یافت نشد', 404);

        }
        try {
            $role->status_id = Role::GetAllStatuses()->where('name', '=', 'غیرفعال')->first()->id;

            $role->save();


            return response()->json('با موفقیت بروزرسانی شد');
        } catch (Exception $exception) {
//            return response()->json($exception->getMessage());
            return response()->json('خطا در بروز رسانی نقش', 500);

        }

    }
}
