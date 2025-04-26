<?php

namespace Modules\PFM\app\Http\Traits;


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\HRMS\app\Http\Enums\ScriptTypesEnum;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptType;
use Modules\PFM\app\Http\Enums\ApplicationsForTablesEnum;
use Modules\PFM\app\Http\Enums\BookletStatusEnum;
use Modules\PFM\app\Http\Enums\LeviesListEnum;
use Modules\PFM\app\Http\Enums\LevyStatusEnum;
use Modules\PFM\app\Models\Booklet;
use Modules\PFM\app\Models\BookletStatus;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\LevyCircular;
use Modules\PFM\app\Models\LevyItem;
use Modules\PFM\app\Models\PfmCirculars;
use Modules\PFM\app\Models\PropApplication;
use Modules\PFM\app\Models\Tarrifs;

trait BillsTrait
{
    public function getDatasOfFilledData($levy, $data)
    {


        $filledData = [];
        if (in_array($levy->name, [
            LeviesListEnum::AMLAK_MOSTAGHELAT->value,
            LeviesListEnum::TAFKIK_ARAZI->value,
            LeviesListEnum::DIVAR_KESHI->value,
            LeviesListEnum::ZIRBANA_MASKONI->value,
            LeviesListEnum::BALKON_PISH_AMADEGI->value,
            LeviesListEnum::MOSTAHADESAT_MAHOVATEH->value,
        ])) {
            $appID = $data['appID'];
            $bookletID = $data['bookletID'];
            $itemID = $data['itemID'];

            $booklet = Booklet::find($bookletID);
            $application = PropApplication::find($appID);
            switch ($application->main_prop_type) {
                case "p_residential":
                    $p = $booklet->p_residential;
                    $areaPrice = $p * $application->adjustment_coefficient;

                    $tarrifs = Tarrifs::where('item_id', $itemID)->where('app_id', $appID)->where('booklet_id', $bookletID)->select('value')->first();
                    $filledData['coefficient'] = $tarrifs->value;
                    $filledData['areaPrice'] = $areaPrice;
                    break;
            }
        }else if()
        return $filledData;
    }
}
