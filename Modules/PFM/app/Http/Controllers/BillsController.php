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
use Modules\PFM\app\Http\Enums\ApplicationsForTablesEnum;
use Modules\PFM\app\Http\Traits\BillsTrait;
use Modules\PFM\app\Models\Booklet;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\LevyCircular;
use Modules\PFM\app\Models\LevyItem;
use Modules\PFM\app\Models\PfmCirculars;
use Modules\PFM\app\Models\PropApplication;
use Modules\PFM\app\Models\Tarrifs;

class BillsController extends Controller
{
    use BillsTrait;
    public function billsVillageData()
    {
        $scriptType = ScriptType::where('title', ScriptTypesEnum::MASOULE_FAANI->value)->first();
        $user = User::find(2174);
        $user->load(['activeRecruitmentScripts' => function ($query) use ($user, $scriptType) {
            $query->where('script_type_id', $scriptType->id);
        }]);

        $ounits = $user->activeRecruitmentScripts->pluck('organization_unit_id')->toArray();

        $villages = [];
        foreach ($ounits as $ounit) {
            $villages[] = OrganizationUnit::with(['ancestorsAndSelf' => function ($query) {
                $query->whereNotIn('unitable_type', [StateOfc::class, TownOfc::class]);
            }])->find($ounit);
        }

        return response()->json($villages);
    }
    public function bankAccounts($id)
    {
        return BankAccount::where('ounit_id', $id)->get();
    }

    public function BookletData(Request $request,$id)
    {
        $data = $request->all();
        $fiscalYearID = $data['fiscal_year_id'];

        $circular = PfmCirculars::where('fiscal_year_id', $fiscalYearID)->first();

        $booklet = Booklet::where('pfm_circular_id', $circular->id)->where('ounit_id' , $id)->get();
        return response() -> json($booklet);

    }
    public function leviesList($id)
    {
        $fiscalYearID = $id;

        $circular = PfmCirculars::where('fiscal_year_id', $fiscalYearID)->first();

        $circularLevies = LevyCircular::where('circular_id', $circular->id)->get();


        $levies = $circularLevies->pluck('levy_id')->toArray();


        $hasAppLevies = Levy::select(['id', 'name'])->where('has_app', true)->whereIn('id', $levies)->get();


        $data = [];
        foreach ($hasAppLevies as $hasAppLevy) {
            switch ($hasAppLevy->name) {
                case ApplicationsForTablesEnum::AMLAK_MOSTAGHELAT_SINGLES->value:
                    $subData = [];
                    $subData['id'] = $hasAppLevy->id;
                    $subData['name'] = $hasAppLevy->name;
                    $applications = ApplicationsForTablesEnum::AMLAK_MOSTAGHELAT_SINGLES->values();
                    foreach ($applications as $application) {
                        $appData = PropApplication::select('id', 'name')->where('id', $application)->first();
                        $subData['applications'][] = $appData;
                    }
                    $multipleAppsIDs = ApplicationsForTablesEnum::AMLAK_MOSTAGHELAT_MULTIPLES->values();
                    foreach ($multipleAppsIDs as $multipleAppId) {
                        if (is_array($multipleAppId)) {
                            foreach ($multipleAppId as $appId) {
                                $appData = PropApplication::select('id', 'name')->where('id', $appId)->first();
                                $subData['applications'][] = $appData;
                            }
                        } else {
                            $appData = PropApplication::select('id', 'name')->where('id', $application)->first();
                            $subData['applications'][] = $appData;
                        }

                    }
                    $data[] = $subData;
                    break;
                case ApplicationsForTablesEnum::TAFKIK_ARAZI_SINGLES->value:
                    $subData = [];
                    $subData['id'] = $hasAppLevy->id;
                    $subData['name'] = $hasAppLevy->name;
                    $applications = ApplicationsForTablesEnum::TAFKIK_ARAZI_SINGLES->values();
                    foreach ($applications as $application) {
                        $appData = PropApplication::select('id', 'name')->where('id', $application)->first();
                        $subData['applications'][] = $appData;
                    }
                    $multipleAppsIDs = ApplicationsForTablesEnum::TAFKIK_ARAZI_MULTIPLES->values();
                    foreach ($multipleAppsIDs as $multipleAppId) {
                        if (is_array($multipleAppId)) {
                            foreach ($multipleAppId as $appId) {
                                $appData = PropApplication::select('id', 'name')->where('id', $appId)->first();
                                $subData['applications'][] = $appData;
                            }
                        } else {
                            $appData = PropApplication::select('id', 'name')->where('id', $application)->first();
                            $subData['applications'][] = $appData;
                        }

                    }
                    $data[] = $subData;
                    break;
                case ApplicationsForTablesEnum::PARVANEH_HESAR_SINGLES->value:
                    $subData = [];
                    $subData['id'] = $hasAppLevy->id;
                    $subData['name'] = $hasAppLevy->name;
                    $applications = ApplicationsForTablesEnum::PARVANEH_HESAR_SINGLES->values();
                    foreach ($applications as $application) {
                        $appData = PropApplication::select('id', 'name')->where('id', $application)->first();
                        $subData['applications'][] = $appData;
                    }
                    $multipleAppsIDs = ApplicationsForTablesEnum::PARVANEH_HESAR_MULTIPLES->values();
                    foreach ($multipleAppsIDs as $multipleAppId) {
                        if (is_array($multipleAppId)) {
                            foreach ($multipleAppId as $appId) {
                                $appData = PropApplication::select('id', 'name')->where('id', $appId)->first();
                                $subData['applications'][] = $appData;
                            }
                        } else {
                            $appData = PropApplication::select('id', 'name')->where('id', $application)->first();
                            $subData['applications'][] = $appData;
                        }

                    }
                    $data[] = $subData;
                    break;
                case ApplicationsForTablesEnum::PARVANE_ZIRBANA_SINGLES->value:
                    $subData = [];
                    $subData['id'] = $hasAppLevy->id;
                    $subData['name'] = $hasAppLevy->name;
                    $applications = ApplicationsForTablesEnum::PARVANE_ZIRBANA_SINGLES->values();
                    foreach ($applications as $application) {
                        $appData = PropApplication::select('id', 'name')->where('id', $application)->first();
                        $subData['applications'][] = $appData;
                    }
                    $multipleAppsIDs = ApplicationsForTablesEnum::PARVANE_ZIRBANA_MULTIPLES->values();
                    foreach ($multipleAppsIDs as $multipleAppId) {
                        if (is_array($multipleAppId)) {
                            foreach ($multipleAppId as $appId) {
                                $appData = PropApplication::select('id', 'name')->where('id', $application)->first();
                                $subData['applications'][] = $appData;
                            }
                        } else {
                            $appData = PropApplication::select('id', 'name')->where('id', $appId)->first();
                            $subData['applications'][] = $appData;
                        }

                    }
                    $data[] = $subData;
                    break;
                case ApplicationsForTablesEnum::PARVANE_BALKON_SINGLES->value:
                    $subData = [];
                    $subData['id'] = $hasAppLevy->id;
                    $subData['name'] = $hasAppLevy->name;
                    $applications = ApplicationsForTablesEnum::PARVANE_BALKON_SINGLES->values();
                    foreach ($applications as $application) {
                        $appData = PropApplication::select('id', 'name')->where('id', $application)->first();
                        $subData['applications'][] = $appData;
                    }
                    $multipleAppsIDs = ApplicationsForTablesEnum::PARVANE_BALKON_MULTIPLES->values();
                    foreach ($multipleAppsIDs as $multipleAppId) {
                        if (is_array($multipleAppId)) {
                            foreach ($multipleAppId as $appId) {
                                $appData = PropApplication::select('id', 'name')->where('id', $application)->first();
                                $subData['applications'][] = $appData;
                            }
                        } else {
                            $appData = PropApplication::select('id', 'name')->where('id', $appId)->first();
                            $subData['applications'][] = $appData;
                        }

                    }
                    $data[] = $subData;
                    break;
                case ApplicationsForTablesEnum::PARVANEH_MOSTAHADESAT_SINGLES->value:
                    $subData = [];
                    $subData['id'] = $hasAppLevy->id;
                    $subData['name'] = $hasAppLevy->name;
                    $applications = ApplicationsForTablesEnum::PARVANEH_MOSTAHADESAT_SINGLES->values();
                    foreach ($applications as $application) {
                        $appData = PropApplication::select('id', 'name')->where('id', $application)->first();
                        $subData['applications'][] = $appData;
                    }
                    $multipleAppsIDs = ApplicationsForTablesEnum::PARVANEH_MOSTAHADESAT_MULTIPLES->values();
                    foreach ($multipleAppsIDs as $multipleAppId) {
                        if (is_array($multipleAppId)) {
                            foreach ($multipleAppId as $appId) {
                                $appData = PropApplication::select('id', 'name')->where('id', $application)->first();
                                $subData['applications'][] = $appData;
                            }
                        } else {
                            $appData = PropApplication::select('id', 'name')->where('id', $appId)->first();
                            $subData['applications'][] = $appData;
                        }

                    }
                    $data[] = $subData;
                    break;
            }
        }

        $hasNotAppLevies = Levy::select(['id', 'name'])->where('has_app', false)->whereIn('id', $levies)->get();

        //ounayi ke fagat sharhan bayad item ha ham biyan

        return response()->json(['hasApp' => $data, 'hasNotApp' => $hasNotAppLevies]);
    }
    public function levyItemsList(Request $request, $id)
    {
        $data = $request->all();
        $bookletID = $data['booklet_id'];
        $circular = PfmCirculars::where('fiscal_year_id', $data['fiscal_year_id'])->first();

        $levyCirculars = LevyCircular::where('circular_id', $circular->id)->where('levy_id', $id)->first();

        $levyItems = LevyItem::where('circular_levy_id', $levyCirculars->id)->select('name' , 'id')->get();

        $tariffs = Tarrifs::where('booklet_id' , $bookletID)->whereIn('item_id' , $levyItems->pluck('id')->toArray())->get();

        $appIDs = $tariffs->pluck('app_id')->toArray();

        $applications = PropApplication::whereIn('id' , $appIDs)->select('id' , 'name')->get();

        return response()->json(["items" => $levyItems , 'applications' => $applications]);
    }

    public function getFilledData(Request $request ,$id)
    {
        $data = $request->all();
        $levy = Levy::find($id);

        $filledData = $this->getDatasOfFilledData($levy , $data);

        return response()->json($filledData);

    }
}
