<?php

namespace Modules\BDM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\ACMS\app\Models\FiscalYear;
use Modules\BDM\app\Http\Traits\DossierTrait;
use Modules\BDM\app\Http\Traits\StructuresTrait;
use Modules\PFM\app\Http\Enums\LeviesListEnum;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\LevyCircular;
use Modules\PFM\app\Models\LevyItem;
use Modules\PFM\app\Models\PfmCirculars;

class StructuresController extends Controller
{
    use StructuresTrait , DossierTrait;
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
        $levy = Levy::where('name' , LeviesListEnum::DIVAR_KESHI->value)->first();
        $fiscalYear = FiscalYear::orderBy('name', 'desc')->first();
        $circular = PfmCirculars::where('fiscal_year_id' , $fiscalYear->id)->first();
        $circularLevy = LevyCircular::where('levy_id' , $levy->id)->where('circular_id' , $circular->id)->first();
        $levyItems = LevyItem::where('circular_levy_id' , $circularLevy->id)->select(['id' , 'name'])->get();
        return response()->json(["participationTypes" => $levyItems , "data" => $this->getStructures($id)]);
    }
}
