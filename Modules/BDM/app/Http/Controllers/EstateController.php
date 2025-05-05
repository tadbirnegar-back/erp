<?php

namespace Modules\BDM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\BDM\app\Http\Enums\EstateConditionsEnum;
use Modules\BDM\app\Http\Enums\FieldConditionsEnum;
use Modules\BDM\app\Http\Traits\EstateTrait;

class EstateController extends Controller
{
    use EstateTrait;
    public function getEstatesPreData($id)
    {
        $listOfEstateConditions = EstateConditionsEnum::listWithIds();
        $listOfFieldConditions = FieldConditionsEnum::listWithIds();
        $getGeoLocations = $this->getGeoLocations($id);
        $getArea = $this->getArea($id);
        return response()->json(['estateConditions' => $listOfEstateConditions , 'geoLocations' => $getGeoLocations , 'fileds' => $listOfFieldConditions,'area' => $getArea]);
    }
}
