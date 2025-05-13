<?php

namespace Modules\BDM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\BDM\app\Http\Enums\FloorNumbersEnum;
use Modules\BDM\app\Http\Traits\DossierTrait;
use Modules\BDM\app\Http\Traits\StructuresTrait;
use Modules\BDM\app\Models\Estate;
use Modules\BDM\app\Models\EstateAppSet;
use Modules\PFM\app\Http\Enums\LeviesListEnum;
use Modules\PFM\app\Models\Application;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\LevyCircular;
use Modules\PFM\app\Models\LevyItem;
use Modules\PFM\app\Models\PfmCirculars;

class StructuresController extends Controller
{
    use StructuresTrait, DossierTrait;

    public function storeStructures(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $data = $request->all();
            $this->insertStructures($id, $data);
            \DB::commit();
            return response()->json(['message' => "اطلاعات سازه ثبت شد"]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function preDataStructures($id)
    {
        $fiscalYear = FiscalYear::orderBy('name', 'desc')->first();
        $circular = PfmCirculars::where('fiscal_year_id', $fiscalYear->id)->first();


        $levyDivar = Levy::where('name', LeviesListEnum::DIVAR_KESHI->value)->first();
        $circularLevy = LevyCircular::where('levy_id', $levyDivar->id)->where('circular_id', $circular->id)->first();
        $levyItemsDivar = LevyItem::where('circular_levy_id', $circularLevy->id)->select(['id', 'name'])->get();

        //Levy zir bana
        $levyZirBana = Levy::where('name', LeviesListEnum::ZIRBANA_MASKONI->value)->first();
        $circularLevyZirbana = LevyCircular::where('levy_id', $levyZirBana->id)->where('circular_id', $circular->id)->first();
        $levyItemsZirBana = LevyItem::where('circular_levy_id', $circularLevyZirbana->id)->select(['id', 'name'])->get();

        $floorNumbers = FloorNumbersEnum::listWithIds();

        $estate = Estate::where('dossier_id', $id)->first();
        $apps = EstateAppSet::where('estate_id', $estate->id)->get();
        $apps->map(function ($item) {
            $item->app_name = Application::find($item->app_id)->name;
        });

        return response()->json(["apps" => $apps,"participationTypes" => $levyItemsDivar, "levyItemsZirbana" => $levyItemsZirBana, "floorNumbers" => $floorNumbers, "data" => $this->getStructures($id)]);
    }
}
