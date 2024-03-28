<?php

namespace Modules\AddressMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AddressMS\app\Models\Address;
use Modules\AddressMS\app\Models\City;
use Modules\AddressMS\app\Models\Country;
use Modules\AddressMS\app\Models\District;
use Modules\AddressMS\app\Models\State;
use Modules\AddressMS\app\Models\Town;
use Modules\AddressMS\app\Models\Village;
use Modules\FileMS\app\Models\File;

class AddressMSController extends Controller
{
    public array $data = [];

    /**
     * @Authenticated
     */
    public function index(): JsonResponse
    {
        $user = \Auth::user();
        $statusID = Address::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
        $response = $user->addresses()->where('status_id', '=', $statusID)->orderBy('create_date', 'desc')->select(['id', 'title'])->get();

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $address = new Address();
            $address->title = $request->title;
            $address->detail = $request->address;
            $address->postal_code = $request->postalCode ?? null;
            $address->longitude = $request->longitude ?? null;
            $address->latitude = $request->latitude ?? null;
            $address->map_link = $request->mapLink ?? null;
            $address->city_id = $request->cityID;
            $address->status_id = Address::GetAllStatuses()->where('name', '=', 'فعال')->first()->id;
            $address->creator_id = \Auth::user()->id;
            $address->save();
            return response()->json($address->load('city', 'state', 'country'));

        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }

    }
    /**
     * Show the specified resource.
     */
    public function show($id): JsonResponse
    {
        $address = Address::with('city', 'state', 'country','status')->findOrFail($id);

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
        $address->title = $request->title;
        $address->detail = $request->address;
        $address->postal_code = $request->postalCode ?? null;
        $address->longitude = $request->longitude ?? null;
        $address->latitude = $request->latitude ?? null;
        $address->map_link = $request->mapLink ?? null;
        $address->city_id = $request->cityID;

        $address->save();

        return response()->json($this->data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $address = Address::findOrFail($id);
        if ($address === null) {
            return response()->json('فایل مورد نظر یافت نشد', 404);
        }

        $address->status_id = $request->statusID;

        return response()->json('با موفقیت حذف شد');
    }

    public function countries(Request $request)
    {
        $countries = Country::all();

        return response()->json($countries);
    }

    public function statesOfCountry(Request $request)
    {
        $states = State::where('country_id','=',$request->countryID)->get(['id','name']);
        if ($states == null) {
            return response()->json(['message' => 'موردی یافت نشد', 404]);
        }


        return response()->json($states);

    }

    public function citiesOfState(Request $request)
    {
        $cities = City::where('state_id','=',$request->stateID)->get(['id','name']);

        if ($cities == null) {
            return response()->json(['message' => 'موردی یافت نشد', 404]);
        }

        return response()->json($cities);
    }

    public function districtsOfCity(Request $request)
    {
        $districts = District::where('city_id','=',$request->cityID)->get(['id','name']);

        if (is_null($districts) ) {
            return response()->json(['message' => 'موردی یافت نشد', 404]);
        }


        return response()->json($districts);
    }


    public function townsOfDistrict(Request $request)
    {
        $districts = Town::where('district_id','=',$request->districtID)->get(['id','name']);

        if (is_null($districts) ) {
            return response()->json(['message' => 'موردی یافت نشد', 404]);
        }

        return response()->json($districts);
    }


    public function villagesOfTown(Request $request)
    {
        $districts = Village::where('town_id','=',$request->townID)->get(['id','name']);

        if (is_null($districts) ) {
            return response()->json(['message' => 'موردی یافت نشد', 404]);
        }

        return response()->json($districts);
    }


}
