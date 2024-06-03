<?php

namespace Modules\AddressMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\AddressMS\app\Models\Address;
use Modules\AddressMS\app\Models\City;
use Modules\AddressMS\app\Models\Country;
use Modules\AddressMS\app\Models\District;
use Modules\AddressMS\app\Models\State;
use Modules\AddressMS\app\Models\Town;
use Modules\AddressMS\app\Models\Village;
use Modules\AddressMS\app\Traits\AddressTrait;

class AddressMSController extends Controller
{
    use AddressTrait;

//    public array $data = [];

    /**
     * @Authenticated
     */
    public function index(): JsonResponse
    {
        $user = \Auth::user();
        $statusID = $this->activeAddressStatus()->id;
        $response = $user->person->addresses()->where('status_id', '=', $statusID)->orderBy('create_date', 'desc')->select(['id', 'title'])->get();

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            \DB::beginTransaction();
            $data = $request->all();
            $data['userID'] = \Auth::user()->id;

            $address = $this->addressStore($data);
            \DB::commit();
            return response()->json($address->load('village', 'town.district.city.state.country'));

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'خطا در ثبت آدرس جدید'], 500);
        }

    }

    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $address = $this->addressShow($id);

        if ($address === null) {
            return response()->json('فایل مورد نظر یافت نشد', 404);
        }
        return response()->json($address);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $address = Address::findOrFail($id);
        if ($address === null) {
            return response()->json('فایل مورد نظر یافت نشد', 404);
        }
        $data = $request->all();
        $addressResult = $this->addressUpdate($data,$address);

        return response()->json(['message'=>'باموفقیت ویرایش شد']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $address = Address::findOr($id, function () {
            return response()->json('فایل مورد نظر یافت نشد', 404);
        });

        $status = $this->inactiveAddressStatus()->id;

        if ($status->id !== $address->status_id) {
            $address->status_id = $status->id;
            $address->save();
        }

        return response()->json('با موفقیت حذف شد');
    }

    public function countries(Request $request)
    {
        $countries = Country::all();

        return response()->json($countries);
    }

    public function statesOfCountry(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'countryID' => [
                'required',
            ],

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $states = State::where('country_id', '=', $request->countryID)->get(['id', 'name']);
        if ($states->isEmpty()) {
            return response()->json(['message' => 'موردی یافت نشد'], 404);
        }


        return response()->json($states);

    }

    public function citiesOfState(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'stateID' => [
                'required',
            ],

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cities = City::where('state_id', '=', $request->stateID)->get(['id', 'name']);

        if ($cities->isEmpty()) {
            return response()->json(['message' => 'موردی یافت نشد', 404]);
        }

        return response()->json($cities);
    }

    public function districtsOfCity(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'cityID' => [
                'required',
            ],

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $districts = District::where('city_id', '=', $request->cityID)->get(['id', 'name']);

        if ($districts->isEmpty()) {
            return response()->json(['message' => 'موردی یافت نشد', 404]);
        }


        return response()->json($districts);
    }


    public function townsOfDistrict(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'districtID' => [
                'required',
            ],

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $districts = Town::where('district_id', '=', $request->districtID)->get(['id', 'name']);

        if ($districts->isEmpty()) {
            return response()->json(['message' => 'موردی یافت نشد', 404]);
        }

        return response()->json($districts);
    }


    public function villagesOfTown(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'townID' => [
                'required',
            ],

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $districts = Village::where('town_id', '=', $request->townID)->get(['id', 'name']);

        if (is_null($districts)) {
            return response()->json(['message' => 'موردی یافت نشد', 404]);
        }

        return response()->json($districts);
    }


}
