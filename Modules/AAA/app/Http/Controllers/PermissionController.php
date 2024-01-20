<?php

namespace Modules\AAA\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AAA\app\Models\Permission;
use Modules\AAA\app\Models\Role;
use Modules\AAA\app\Models\User;

class PermissionController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = \Auth::user();
//        $role = Role::find(1);

        $permissions = Permission::with('moduleCategory')->get();
        foreach ($permissions as $permission) {
            $a[$permission->moduleCategory->name][] = ['label' => $permission->name, 'value' => $permission->id];
        }
        foreach ($user->permissions as $permission) {
            $b[] = $permission->id;
        }

        return response()->json([$a,$b]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        //

        return response()->json($this->data);
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
