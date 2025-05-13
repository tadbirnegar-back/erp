<?php

namespace Modules\BDM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\BDM\app\Http\Enums\BdmOwnershipTypesEnum;
use Modules\BDM\app\Http\Enums\BdmTypesEnum;
use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\Estate;
use Modules\BDM\app\Models\EstateAppSuggest;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\PFM\app\Models\Application;

class BDMController extends Controller
{
    public function updateEstate(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $data = $request->all();
            $estate = Estate::where('dossier_id', $id)->first();
            $estate->ounit_id = $data['ounitID'];
            $estate->ownership_type_id = $data['ownershipTypeID'];
            $estate->part = $data['part'];
            $estate->postal_code = $data['postalCode'];
            $estate->address = $data['address'];
            $estate->ounit_number = $data['ounitNumber'];
            $estate->main = $data['main'];
            $estate->minor = $data['minor'];
            $estate->building_number = $data['buildingNumber'];
            $estate->area = $data['area'];
            $estate->save();

            $app = EstateAppSuggest::where('estate_id', $estate->id)->first();
            $app->app_id = $data['appID'];
            $app->save();
            \DB::commit();
            return response()->json(['message' => 'با موفقیت به روز شد']);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateEstatePreData($id)
    {
        $query = BuildingDossier::join('bdm_estates', 'bdm_building_dossiers.id', '=', 'bdm_estates.dossier_id')
            ->join('organization_units as village', 'bdm_estates.ounit_id', '=', 'village.id')
            ->join('organization_units as town', 'village.parent_id', '=', 'town.id')
            ->join('organization_units as district', 'town.parent_id', '=', 'district.id')
            ->join('organization_units as city', 'district.parent_id', '=', 'city.id')
            ->join('bdm_estate_app_suggests', 'bdm_estates.id', '=', 'bdm_estate_app_suggests.estate_id')
            ->join('pfm_prop_applications', 'bdm_estate_app_suggests.app_id', '=', 'pfm_prop_applications.id')
            ->select([
                'bdm_estates.id as estate_id',
                'bdm_estates.ownership_type_id as ownership_type_id',
                'bdm_estates.area as area',
                'bdm_estates.postal_code as postal_code',
                'bdm_estates.building_number as building_number',
                'bdm_estates.ounit_number as ounit_number',
                'bdm_estates.main as main',
                'bdm_estates.minor as minor',
                'bdm_estates.part as part',
                'pfm_prop_applications.name as app_name',
                'pfm_prop_applications.id as app_id',
                'city.name as city_name',
                'city.id as city_id',
                'district.name as district_name',
                'district.id as district_id',
                'village.name as village_name',
                'village.id as village_id',
                'bdm_estates.address as address',
                'bdm_building_dossiers.tracking_code as tracking_code',
            ])
            ->find($id);


        $query->ownership_type_name = BdmOwnershipTypesEnum::getNameById($query->ownership_type_id);


        $apps = Application::select('id', 'name')->get();

        $ownershipTypes = BdmOwnershipTypesEnum::listWithIds();

        $cities = OrganizationUnit::where('unitable_type', CityOfc::class)->select(['id', 'name'])->get();

        return response()->json(['estate' => $query, 'apps' => $apps, 'ownershipTypes' => $ownershipTypes, 'citiesList' => $cities]);
    }
}
