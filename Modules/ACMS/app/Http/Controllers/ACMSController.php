<?php

namespace Modules\ACMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Modules\ACMS\app\Http\Enums\AccountantScriptTypeEnum;
use Modules\ACMS\app\Resources\AccounterOunitsListResource;
use Modules\OUnitMS\app\Models\StateOfc;

class ACMSController extends Controller
{

    public function dispatchedCircularsForMyVillage(Request $request)
    {
        $user = Auth::user();
        $recruitmentScripts = $user
            ->activeRecruitmentScripts()
            ->whereHas('scriptType', function ($query) {
                $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
            })
            ->with(['organizationUnit' => function ($query) {
                $query
                    ->with(['village', 'ancestors' => function ($query) {
                        $query->where('unitable_type', '!=', StateOfc::class);
                    },
                    ]);
            }])->get();

        if ($recruitmentScripts->isEmpty()) {
            return response()->json(['message' => 'دهیاری ای تحت نظر شما یافت نشد'], 404);
        }

        $ounits = $recruitmentScripts->pluck('organizationUnit');

        return AccounterOunitsListResource::collection($ounits);

    }

}
