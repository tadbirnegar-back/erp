<?php

namespace Modules\BDM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\BDM\app\Http\Enums\EstateConditionsEnum;
use Modules\BDM\app\Http\Enums\FieldConditionsEnum;
use Modules\BDM\app\Http\Traits\EstateTrait;
use Modules\BDM\app\Models\BuildingDossier;

class EstateController extends Controller
{
    use EstateTrait;

    public function getEstatesPreData($id)
    {
        $listOfEstateConditions = EstateConditionsEnum::listWithIds();
        $listOfFieldConditions = FieldConditionsEnum::listWithIds();
        $getGeoLocations = $this->getGeoLocations($id);
        $getArea = $this->getArea($id);
        $getBdmType = $this->getBdmType($id);
        $getGeoLocationsList = $this->getGeoLocationList();
        return response()->json(['estateConditions' => $listOfEstateConditions, 'geoLocations' => $getGeoLocations, 'fileds' => $listOfFieldConditions, 'bdmType' => $getBdmType, 'geoLocationList' => $getGeoLocationsList, 'area' => $getArea]);
    }

    public function FullFillEstate(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $data = $request->all();

            $this->insertEstateDatas($id, $data);
            \DB::commit();
            return response()->json(['message' => "دوره با موفقیت به روز رسانی شد"]);
        }catch (\Exception $e){
            \DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }


}
