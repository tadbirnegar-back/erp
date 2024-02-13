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
        $role = Role::all();

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
            $role->name=$request->name;
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
