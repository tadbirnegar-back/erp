<?php

namespace Modules\AddressMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AddressMS\app\Models\Address;
use Modules\AddressMS\app\Models\Country;
use Modules\AddressMS\app\Models\State;
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
        $response = $countries->map(function ($country) {
            return [
                'label' => $country->name,
                'value' => $country->id
            ];
        });
        return response()->json($response);
    }

    public function statesOfCountry(Request $request)
    {
        $country = Country::with('states')->findOrFail($request->countryID);

        if ($country == null) {
            return response()->json(['message' => 'موردی یافت نشد', 404]);
        }
        $states = $country->states->map(function ($state) {
            return [
                'label' => $state->name,
                'value' => $state->id
            ];
        });

        return response()->json($states);

    }

    public function citiesOfState(Request $request)
    {
        $state = State::with('cities')->findOrFail($request->stateID);

        if ($state == null) {
            return response()->json(['message' => 'موردی یافت نشد', 404]);
        }
        $cities = $state->cities->map(function ($city) {
            return [
                'label' => $city->name,
                'value' => $city->id
            ];
        });

        return response()->json($cities);
    }
}
