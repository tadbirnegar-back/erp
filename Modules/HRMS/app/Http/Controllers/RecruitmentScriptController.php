<?php

namespace Modules\HRMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HRMS\app\Models\HireType;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\ScriptType;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\OUnitMS\app\Models\VillageOfc;

class RecruitmentScriptController extends Controller
{

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
        $states = StateOfc::with('organizationUnit')
            //exclude EastAzerbaijan state from loading
            ->whereIntegerNotInRaw('id',[3])
            ->get();

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

    public function townOfcs(Request $request)
    {
        $states = TownOfc::with('organizationUnit')->where('district_ofc_id',$request->districtOfcID)->get();

        return response()->json($states);

    }


    public function villageOfcs(Request $request)
    {
        $districtOfc = DistrictOfc::with(['villageOfcs'=>function ($query) {
            $query->where('hasLicense',true)->with('organizationUnit');
        }])->find($request->districtOfcID);

        return response()->json($districtOfc->villageOfcs);

    }

    public function addRecruitmentScriptBaseInfo(Request $request)
    {
        $data = $request->all();

        $result['hireTypes']=HireType::all();
        $result['jobs']=Job::all();
        $ounit = OrganizationUnit::with('positions.levels')->find($data['ounitID']);
        $result['OunitDetails']=$ounit->positions;

        return response()->json($result);
    }

    public function getScriptAgentCombos()
    {
        
    }
}
