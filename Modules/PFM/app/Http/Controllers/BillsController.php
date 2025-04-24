<?php

namespace Modules\PFM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\AAA\app\Models\User;
use Modules\AddressMS\app\Models\State;
use Modules\AddressMS\app\Models\Town;
use Modules\BNK\app\Models\BankAccount;
use Modules\HRMS\app\Http\Enums\ScriptTypesEnum;
use Modules\HRMS\app\Models\Level;
use Modules\HRMS\app\Models\ScriptType;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\TownOfc;
use Modules\PFM\app\Models\Levy;

class BillsController extends Controller
{
    public function billsVillageData()
    {
        $scriptType = ScriptType::where('title', ScriptTypesEnum::MASOULE_FAANI->value)->first();
        $user = User::find(2174);
        $user->load(['activeRecruitmentScripts' => function ($query) use ($user , $scriptType) {
            $query->where('script_type_id', $scriptType->id);
        }]);

        $ounits = $user->activeRecruitmentScripts->pluck('organization_unit_id')->toArray();

        $villages = [];
        foreach ($ounits as $ounit) {
            $villages[] = OrganizationUnit::with(['ancestorsAndSelf' => function ($query) {
                $query->whereNotIn('unitable_type', [StateOfc::class, TownOfc::class]);
            }])->find($ounit);
        }

        return response() -> json($villages);
    }

    public function bankAccounts($id)
    {
        return BankAccount::where('ounit_id', $id)->get();
    }

    public function leviesList()
    {
        $levies = Levy::select(['id', 'name', 'has_app'])->get();
        return response() -> json($levies);
    }
}
