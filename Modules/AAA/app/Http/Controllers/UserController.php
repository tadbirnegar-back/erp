<?php

namespace Modules\AAA\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\AAA\app\Http\Services\UserService;
use Modules\AAA\app\Models\User;
use Modules\AddressMS\app\Repositories\AddressRepository;
use Modules\PersonMS\app\Http\Repositories\PersonRepository;

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
        $all = User::with('roles', 'person.avatar', 'statuses')->get();

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
        $user = $this->userService->show($id);

        if (is_null($user)) {
            return response()->json(['message' => 'کاربری با این مشخصات یافت نشد'], 404);

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

        $user = User::findOrFail($id);

        if (is_null($user)) {
            return response()->json(['message' => 'کاربری با این مشخصات یافت نشد'], 404);

        }
        $data = $request->all();

        $data['roles'] = json_decode($data['roles']);

        $result = $this->userService->update($data, $id);

        if ($result instanceof \Exception) {
            return response()->json(['message' => $result->getMessage()], 500);
            return response()->json(['message' => 'خطا در بروزرسانی کاربر'], 500);
        }

        return response()->json(['message' => 'با موفقیت بروز رسانی شد']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);

        if (is_null($user)) {
            return response()->json(['message' => 'کاربری با این مشخصات یافت نشد'], 404);
        }

        $status = $user->status;
        $disable = User::GetAllStatuses()->where('name', '=', 'غیرفعال')->first();

        if ($status[0]->id != $disable->id) {
            $user->statuses()->attach($disable->id);
        }
        return response()->json(['message' => 'کاربر با موفقیت حذف شد']);
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
            $user->username = $data['username'] ?? null;
            $user->mobile = $data['mobile'];
            $user->save();
//            }
            $person = $user->person;

            if ($request->isNewAddress) {
                $addressService = new AddressRepository();
                $data['personID'] = $person->id;
                $address = $addressService->store($data);

                if ($address instanceof \Exception) {
                    return response()->json(['message' => 'خطا در وارد کردن آدرس'], 500);
                }
                $data['homeAddressID'] = $address->id;

            }
            $personService = new PersonRepository();
            $personResult = $personService->naturalUpdate($data, $person->personable_id);

            if ($personResult instanceof \Exception) {
                return response()->json(['message' => $personResult->getMessage()], 500);
//            return response()->json(['message' => 'خطا در افزودن شخص'], 500);
            }
            \DB::commit();
            $user->load('person.avatar', 'person.personable');
            return response()->json(['message' => 'با موفقیت ویرایش شد', 'data' => $user]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
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

