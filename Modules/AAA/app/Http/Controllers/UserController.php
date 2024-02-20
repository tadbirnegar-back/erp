<?php

namespace Modules\AAA\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AAA\app\Http\Services\UserService;
use Modules\AAA\app\Models\User;

class UserController extends Controller
{
    public array $data = [];

    protected UserService $userService;




    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        return response()->json($this->data);
    }


    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $user = $this->userService->show($id);

        if (is_null($user)) {
            return response()->json(['message'=>'کاربری با این مشخصات یافت نشد'],404);

        }

        if (\request()->route()->named('user.edit')) {
            $statuses = User::GetAllStatuses();

            return response()->json(['user' => $user, 'statuses' => $statuses]);

        }


        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $result = $this->userService->update($request->all(),$id);

        if ($result instanceof \Exception) {
            return response()->json(['message' => 'خطا در بروزرسانی کاربر'], 500);
        }

        return response()->json(['message' => 'با موفقیت بروز رسانی شد']);
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
