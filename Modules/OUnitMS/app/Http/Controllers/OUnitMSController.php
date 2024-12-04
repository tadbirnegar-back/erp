<?php

namespace Modules\OUnitMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Modules\HRMS\app\Http\Traits\EmployeeTrait;
use Modules\OUnitMS\app\Http\Enums\statusEnum;
use Modules\OUnitMS\app\Http\Traits\OrganizationUnitTrait;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\OUnitMS\app\Models\VillageOfc;

class OUnitMSController extends Controller
{
    use OrganizationUnitTrait, EmployeeTrait;

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

    public function districtsAllList()
    {
        $districts = OrganizationUnit::with('ancestors')->where('unitable_type', DistrictOfc::class)->get();

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
        return OrganizationUnit::with(['unitable', 'ancestorsAndSelf', 'person.user'])->findOr($id, function () {
            return response()->json(['message' => 'موردی یافت نشد'], 404);
        });
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $ounit = OrganizationUnit::with('unitable')->findOr($id, function () {
            return response()->json(['message' => 'موردی یافت نشد'], 404);
        });


        $type = $ounit->unitable_type;

        try {
            DB::beginTransaction();
            switch ($type) {

                case CityOfc::class:
                    $this->updateCity($data, $ounit);
                    break;

                case DistrictOfc::class:
                    $this->updateDistrict($data, $ounit);
                    break;

                case TownOfc::class:
                    $this->updateTown($data, $ounit);
                    break;

                case VillageOfC::class:
                    $this->updateVillage($data, $ounit);
                    break;
                default:
                    return response()->json(['message' => 'نوع وارد شده با معتبر'], 422);
            }

            DB::commit();
            return response()->json(['message' => 'باموفقیت بروزرسانی شد']);

        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در بروز رسانی'], 404);
        }

    }

    public function search(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $result = $this->searchOunitByname($data['name']);


        return response()->json($result);
    }

//    public function destroy($id)
//    {
//        $ounit = OrganizationUnit::findOr($id, function () {
//            return response()->json(['message' => 'موردی یافت نشد'], 404);
//        });
//
//        try {
//            DB::beginTransaction();
//            $status = $this ->GetInactiveStatuses();
//            $ounit->status_id= $status ->id;
//            $ounit->save();
//            DB::commit();
//            return response()->json(['message' => 'باموفقیت حذف شد']);
//        } catch (Exception $exception) {
//            DB::rollBack();
//            return response()->json(['message' => 'خطا در حذف'], 500);
//        }
//    }


    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $ounit = OrganizationUnit::find($id);
            $this->SoftDeletingOunits($ounit);
            DB::commit();
            return response()->json(['message' => 'باموفقیت حذف شد']);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'خطا در حذف'], 500);
        }
//       $result= $this->SoftDeletingOunits($id);
    }


}
