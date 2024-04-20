<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\VillageOfc;

class RecruitmentScriptController extends Controller
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


    public function stateOfcs(Request $request)
    {
        $states = StateOfc::with('organizationUnit')->get();

        return response()->json($states);

    }

    public function cityOfcs(Request $request)
    {
        $states = CityOfc::with('organizationUnit')->where('state_ofc_id',$request->stateOfcID)->get();

        return response()->json($states);

    }

    public function districtOfcs(Request $request)
    {
        $states = DistrictOfc::with('organizationUnit')->where('city_ofc_id',$request->cityOfcID)->get();

        return response()->json($states);

    }


    public function villageOfcs(Request $request)
    {
        $districtOfc = DistrictOfc::with(['villageOfcs.organizationUnit'])->find($request->districtOfcID);

        return response()->json($districtOfc->villageOfcs);

    }
}
