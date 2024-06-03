<?php

namespace Modules\AAA\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\AAA\app\Models\User;
use Modules\AddressMS\app\Traits\AddressTrait;
use Modules\PersonMS\app\Http\Traits\PersonTrait;

class UserController extends Controller
{


    use UserTrait, AddressTrait, PersonTrait;


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->perPage ?? 10;
        $pageNum = $request->pageNum ?? 1;
        $all = User::with('roles', 'person.avatar', 'statuses')->paginate($perPage, page: $pageNum);

        $all->each(function ($user) {
            if ($user->person->avatar) {
                $user->person->avatar = url(url('/') . '/' . $user->person->avatar);
            }
        });

        return response()->json($all);
    }


    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $user = User::findOr($id, fn() => response()->json('کاربری با این مشخصات یافت نشد', 404));

        $user = $this->showUser($user);


        if (\request()->route()->named('user.edit')) {
            $statuses = $this->allUserStats();

            return response()->json(['user' => $user, 'statuses' => $statuses]);

        }


        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {

        $user = User::findOr($id, fn() => response()->json(['message' => 'کاربری با این مشخصات یافت نشد'], 404));

        try {
            \DB::beginTransaction();
            $data = $request->all();


            $result = $this->updateUser($data, $user);
            \DB::commit();
            return response()->json(['message' => 'با موفقیت بروز رسانی شد']);


        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'خطا در بروزرسانی کاربر'], 500);

        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $user = User::findOr($id, fn() => response()->json(['message' => 'کاربری با این مشخصات یافت نشد'], 404));

        try {
            \DB::beginTransaction();
            $status = $user->status;
            $disable = $this->inactiveUserStatus();

            if ($status[0]->id != $disable->id) {
                $user->statuses()->attach($disable->id);
            }
            \DB::commit();
            return response()->json(['message' => 'کاربر با موفقیت حذف شد']);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'خطا در حذف کاربر'], 500);

        }

    }

    public function updateUserInfo(Request $request)
    {
        $data = $request->all();
        $user = \Auth::user();

        $validator = Validator::make($data, [
            'mobile' => [
                'required',
                'unique:users,mobile,' . $user->id,
            ],
            'username' => [
                'sometimes',
                'unique:users,username,' . $user->id,
            ],
            'nationalCode' => [
                'required',
                'unique:persons,national_code,' . $user->person->id,
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            \DB::beginTransaction();
            $data['userID'] = $user->id;
//            if (isset($data['username'])) {
            $this->updateUser($data, $user);
//            }
            $person = $user->person;

            if ($request->isNewAddress) {
                $data['personID'] = $person->id;
                $address = $this->addressStore($data);

                $data['homeAddressID'] = $address->id;

            }
            $personResult = $this->naturalUpdate($data, $person->personable);


            \DB::commit();
            $user->load('person.avatar', 'person.personable');
            return response()->json(['message' => 'با موفقیت ویرایش شد', 'data' => $user]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'خطا در بروزرسانی کاربر'], 500);

        }

    }

    public function profile()
    {
        $user = \Auth::user();
        $user->load('person.avatar', 'person.personable.homeAddress.village', 'person.personable.homeAddress.town.district.city.state.country');

        return response()->json($user);
    }

    public function updatePassword(Request $request)
    {


        $user = \Auth::user();

        if (\Hash::check($request->currentPassword, $user->password)) {
            $user->password = \Hash::make($request->newPassword);
            $user->save();
            $message = 'با موفقیت بروزرسانی شد';
            $statusCode = 200;
        } else {
            $message = 'رمز فعلی نادرست است';
            $statusCode = 401;

        }


        return response()->json(['message' => $message], $statusCode);
    }
}

