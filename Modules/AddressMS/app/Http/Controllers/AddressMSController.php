<?php

namespace Modules\AddressMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AddressMS\app\Models\Address;

class AddressMSController extends Controller
{
    public array $data = [];

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //

        return response()->json($this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $address = new Address();
        $address->title = $request->title;
        $address->detail = $request->detail;
        $address->postal_code = $request->postal_code ?? null;
        $address->longitude = $request->longitude ?? null;
        $address->latitude = $request->latitude ?? null;
        $address->city_id = $request->city_id;
        $address->status_id = Address::GetAllStatuses()->where('name','=','فعال')->first()->id;
        $address->creator_id = \Auth::user()->id;

        $address->save();


        return response()->json($address->id);
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
