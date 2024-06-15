<?php

namespace Modules\OUnitMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\WorkForce;
use Modules\OUnitMS\app\Http\Traits\OrganizationUnitTrait;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;

class OUnitMSController extends Controller
{
    use OrganizationUnitTrait;

    public function statesIndex(Request $request)
    {
        $searchTerm = $request->name ?? null;
        $perPage = $request->perPage ?? 10;
        $page = $request->pageNum ?? 1;
        $states = $this->ostandariIndex($searchTerm, $perPage, $page);

        return response()->json($states);

    }

    public function statesStore(Request $request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();
            $state = $this->ostandariStore($data);
            DB::commit();

            return response()->json($state);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ایجاد استانداری جدید'], 500);
        }
    }

    public function citiesIndex(Request $request)
    {
        $searchTerm = $request->name ?? null;
        $perPage = $request->perPage ?? 10;
        $page = $request->pageNum ?? 1;
        $cities = $this->farmandariIndex($searchTerm, $perPage, $page);

        return response()->json($cities);
    }

    public function cityStore(Request $request)
    {
        $data = $request->all();

        $ounit = OrganizationUnit::where('unitable_type', StateOfc::class)
            ->findOr($data['ounitID'], function () {
                return response()->json(['message' => 'استانداری نامعتبر'], 404);
            });
        try {
            DB::beginTransaction();
            $data['stateOfcID'] = $ounit->unitable_id;
            $city = $this->farmandariStore($data);
            DB::commit();

            return response()->json($city);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ایجاد فرمانداری جدید'], 500);
        }
    }

    public function districtsIndex(Request $request)
    {
        $searchTerm = $request->name ?? null;
        $ounitID = $request->cityOfcID ?? null;
        $perPage = $request->perPage ?? 10;
        $page = $request->pageNum ?? 1;
        $districts = $this->bakhshdariIndex($searchTerm, $ounitID, $perPage, $page);

        return response()->json($districts);
    }

    public function districtStore(Request $request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();
            $city = $this->bakhshdariStore($data);
            DB::commit();

            return response()->json($city);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ایجاد بخشداری جدید'], 500);
        }
    }

    public function townIndex(Request $request)
    {
        $searchTerm = $request->name ?? null;
        $perPage = $request->perPage ?? 10;
        $page = $request->pageNum ?? 1;
        $ounitID = $request->districtOfcID ?? null;


        $districts = $this->dehestanIndex($searchTerm, $ounitID, $perPage, $page);

        return response()->json($districts);
    }

    public function townStore(Request $request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();
            $city = $this->dehestanStore($data);
            DB::commit();

            return response()->json($city);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ایجاد دهستان جدید'], 500);
        }
    }

    public function villageIndex(Request $request)
    {
        $searchTerm = $request->name ?? null;
        $perPage = $request->perPage ?? 10;
        $page = $request->pageNum ?? 1;
        $ounitID = $request->townOfcID ?? null;

        $districts = $this->dehyariIndex($searchTerm, $ounitID, $perPage, $page);

        return response()->json($districts);
    }

    public function villageStore(Request $request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();
            $city = $this->dehyariStore($data);
            DB::commit();

            return response()->json($city);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در ایجاد دهیاری جدید'], 500);
        }
    }

    public function searchEmployees(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => [
                'required',
            ],

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);


        }
        $searchTerm = $data['name'];


        $employees = $this->getEmployeesByPersonName($searchTerm);

        return response()->json($employees);

    }

    public function show($id)
    {
        return OrganizationUnit::with(['unitable','ancestorsAndSelf','person'])->findOr($id,function (){
            return response()->json(['message' => 'موردی یافت نشد'], 404);
        });
    }

}
