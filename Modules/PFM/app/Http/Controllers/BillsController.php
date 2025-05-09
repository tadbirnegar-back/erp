<?php

namespace Modules\PFM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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
use Modules\PayStream\app\Http\Traits\OrderTrait;
use Modules\PayStream\app\Models\Order;
use Modules\PayStream\app\Models\ProcessStatus;
use Modules\PersonMS\app\Models\Legal;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Modules\PFM\app\Http\Enums\ApplicationsForTablesEnum;
use Modules\PFM\app\Http\Enums\LeviesListEnum;
use Modules\PFM\app\Http\Traits\BillsTrait;
use Modules\PFM\app\Models\Bill;
use Modules\PFM\app\Models\Booklet;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\LevyCircular;
use Modules\PFM\app\Models\LevyItem;
use Modules\PFM\app\Models\PfmCirculars;
use Modules\PFM\app\Models\PropApplication;
use Modules\PFM\app\Models\Tarrifs;
use Modules\PFM\app\Resources\BillsListResource;
use Modules\PFM\app\Resources\ShowBillResource;

class BillsController extends Controller
{
    use BillsTrait, OrderTrait;

    public function billsVillageData()
    {
        $scriptType = ScriptType::where('title', ScriptTypesEnum::MASOULE_FAANI->value)->first();
        $user = \Auth::user();
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
        return BankAccount::join('bnk_bank_branches', 'bnk_bank_branches.id', '=', 'bnk_bank_accounts.branch_id')
            ->join('bnk_banks', 'bnk_banks.id', '=', 'bnk_bank_branches.bank_id')
            ->select('bnk_banks.name as bank_name', 'bnk_bank_accounts.*')
            ->where('ounit_id', $id)->get();
    }

    public function BookletData(Request $request, $id)
    {
        $data = $request->all();
        $fiscalYearID = $data['fiscal_year_id'];

        $circular = PfmCirculars::where('fiscal_year_id', $fiscalYearID)->first();

        if ($circular) {
            $booklet = Booklet::where('pfm_circular_id', $circular->id)->where('ounit_id', $id)->first();
        } else {
            $booklet = null;
        }

        return response()->json($booklet);

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
                            $appData = PropApplication::select('id', 'name')->where('id', $multipleAppId)->first();
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
                            $appData = PropApplication::select('id', 'name')->where('id', $multipleAppId)->first();
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
                            $appData = PropApplication::select('id', 'name')->where('id', $multipleAppId)->first();
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
                                $appData = PropApplication::select('id', 'name')->where('id', $appId)->first();
                                $subData['applications'][] = $appData;
                            }
                        } else {
                            $appData = PropApplication::select('id', 'name')->where('id', $multipleAppId)->first();
                            $subData['applications'][] = $appData;
                        }

                    }
                    $data[] = $subData;
                    break;
                case ApplicationsForTablesEnum::SUDURE_MOJAVEZE_EHDAS_SINGLES->value:
                    $subData = [];
                    $subData['id'] = $hasAppLevy->id;
                    $subData['name'] = $hasAppLevy->name;
                    $applications = ApplicationsForTablesEnum::SUDURE_MOJAVEZE_EHDAS_SINGLES->values();
                    foreach ($applications as $application) {
                        $appData = PropApplication::select('id', 'name')->where('id', $application)->first();
                        $subData['applications'][] = $appData;
                    }
                    $multipleAppsIDs = ApplicationsForTablesEnum::SUDURE_MOJAVEZE_EHDAS_MULTIPLES->values();
                    foreach ($multipleAppsIDs as $multipleAppId) {
                        if (is_array($multipleAppId)) {
                            foreach ($multipleAppId as $appId) {
                                $appData = PropApplication::select('id', 'name')->where('id', $appId)->first();
                                $subData['applications'][] = $appData;
                            }
                        } else {
                            $appData = PropApplication::select('id', 'name')->where('id', $multipleAppId)->first();
                            $subData['applications'][] = $appData;
                        }

                    }
                    $data[] = $subData;
                    break;
                case ApplicationsForTablesEnum::TABLIGHAT_MOHITY_SINGLES->value:
                    $subData = [];
                    $subData['id'] = $hasAppLevy->id;
                    $subData['name'] = $hasAppLevy->name;
                    $applications = ApplicationsForTablesEnum::TABLIGHAT_MOHITY_SINGLES->values();
                    foreach ($applications as $application) {
                        $appData = PropApplication::select('id', 'name')->where('id', $application)->first();
                        $subData['applications'][] = $appData;
                    }
                    $multipleAppsIDs = ApplicationsForTablesEnum::TABLIGHAT_MOHITY_MULTIPLES->values();
                    foreach ($multipleAppsIDs as $multipleAppId) {
                        if (is_array($multipleAppId)) {
                            foreach ($multipleAppId as $appId) {
                                $appData = PropApplication::select('id', 'name')->where('id', $appId)->first();
                                $subData['applications'][] = $appData;
                            }
                        } else {
                            $appData = PropApplication::select('id', 'name')->where('id', $multipleAppId)->first();
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
                                $appData = PropApplication::select('id', 'name')->where('id', $appId)->first();
                                $subData['applications'][] = $appData;
                            }
                        } else {
                            $appData = PropApplication::select('id', 'name')->where('id', $multipleAppId)->first();
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
                                $appData = PropApplication::select('id', 'name')->where('id', $appId)->first();
                                $subData['applications'][] = $appData;
                            }
                        } else {
                            $appData = PropApplication::select('id', 'name')->where('id', $multipleAppId)->first();
                            $subData['applications'][] = $appData;
                        }

                    }
                    $data[] = $subData;
                    break;
            }
        }

        $hasNotAppLevies = Levy::select(['id', 'name'])->where('has_app', false)->whereIn('id', $levies)->get();

        $hasNotAppLevies->each(function ($levy) use (&$data) {
            $data[] = [
                'id' => $levy->id,
                'name' => $levy->name,
                'applications' => null,
            ];
        });
        //ounayi ke fagat sharhan bayad item ha ham biyan


        $dataWithItems = [];
        foreach ($data as $levy) {
            $levyCirculars = LevyCircular::where('circular_id', $circular->id)->where('levy_id', $levy['id'])->first();

            $levyItems = LevyItem::where('circular_levy_id', $levyCirculars->id)->select('name', 'id')->get();
            $levy['items'] = $levyItems;
            switch ($levy['name']) {
                case LeviesListEnum::AMLAK_MOSTAGHELAT->value:
                    $levy['key'] = 'amlak';
                    break;
                case LeviesListEnum::ZIRBANA_MASKONI->value:
                    $levy['key'] = 'zirbana_maskuni';
                    break;
                case LeviesListEnum::DIVAR_KESHI->value:
                    $levy['key'] = 'divar';
                    break;
                case LeviesListEnum::TABLIGHAT->value:
                    $levy['key'] = 'tablighat';
                    break;
                case LeviesListEnum::SUDURE_MOJAVEZE_EHDAS->value:
                    $levy['key'] = 'sudure_mojavez';
                    break;
                case LeviesListEnum::TAFKIK_ARAZI->value:
                    $levy['key'] = 'tafkik_arazi';
                    break;
                case LeviesListEnum::TAMDID_PARVANEH_SAKHTEMAN->value:
                    $levy['key'] = 'tamdid_parvaneh_sakhteman';
                    break;
                case LeviesListEnum::ARZESHE_AFZODEH_HADI->value:
                    $levy['key'] = 'arzheshe_afzodeh_hadi';
                    break;
                case LeviesListEnum::ARZESHE_AFZODEH_OMRAN->value:
                    $levy['key'] = 'arzheshe_afzodeh_omran';
                    break;
                case LeviesListEnum::BALKON_PISH_AMADEGI->value:
                    $levy['key'] = 'balkon_pish_amadegi';
                    break;
                case LeviesListEnum::MOSTAHADESAT_MAHOVATEH->value:
                    $levy['key'] = 'mosthadesat_mahovateh';
                    break;
                case LeviesListEnum::TAJDID_PARVANEH_SAKHTEMAN->value:
                    $levy['key'] = 'tajdid_parvaneh_sakhteman';
                    break;
                case LeviesListEnum::MASHAGHEL_DAEM->value:
                    $levy['key'] = 'mashaghel_daem';
                    break;
                case LeviesListEnum::BAHAYE_KHEDMAT->value:
                    $levy['key'] = 'bahaye_khemdat';
                    break;
                case LeviesListEnum::GHAT_DERAKHTAN->value:
                    $levy['key'] = 'ghat_derakhtan';
                    break;
                case LeviesListEnum::CHESHME_MADANI->value:
                    $levy['key'] = 'cheshme_madani';
                    break;
            }
            $dataWithItems[] = $levy;
        }


        return response()->json($dataWithItems);
    }

    public function levyItemsList(Request $request, $id)
    {
        $data = $request->all();
        $bookletID = $data['booklet_id'];
        $circular = PfmCirculars::where('fiscal_year_id', $data['fiscal_year_id'])->first();

        $levyCirculars = LevyCircular::where('circular_id', $circular->id)->where('levy_id', $id)->first();

        $levyItems = LevyItem::where('circular_levy_id', $levyCirculars->id)->select('name', 'id')->get();

//        $tariffs = Tarrifs::where('booklet_id', $bookletID)->whereIn('item_id', $levyItems->pluck('id')->toArray())->get();
//
//        $appIDs = $tariffs->pluck('app_id')->toArray();
//
//        $applications = PropApplication::whereIn('id', $appIDs)->select('id', 'name')->get();
//        'applications' => $applications
        return response()->json($levyItems);
    }

    public function getFilledData(Request $request, $id)
    {
        $data = $request->all();
        $levy = Levy::find($id);

        $filledData = $this->getDatasOfFilledData($levy, $data);

        return response()->json($filledData);

    }

    public function checkNationalCode(Request $request)
    {
        $data = $request->all();
        $nationalCode = $data['national_code'];

        $person = Person::where('national_code', $nationalCode)->first();

        if ($person) {
            $person->load('user');
            $person->load('personable');
            return response()->json(['situation' => 'found', 'person' => $person]);
        } else {
            return response()->json(['situation' => 'notFound']);
        }
    }

    public function sendBill(Request $request)
    {
        try {
            \DB::beginTransaction();
            $data = $request->all();
            $this->sendBillWithData($data);
            \DB::commit();
            return response()->json(['message' => 'قبض با موفقیت صادر شد']);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function billsList(Request $request)
    {
        $data = $request->all();
        $pageNum = $data['pageNum'] ?? 1;
        $perPage = $data['perPage'] ?? 10;

        $data = $this->generateBillsList($pageNum, $perPage);

        return BillsListResource::collection($data);
    }

    public function showBill($id)
    {
        $data = $this->getBillData($id);
        return new ShowBillResource($data);
    }

    public function confirmBill(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = \Auth::user();
            $this->billConfirmation($data, $id, $user);
            DB::commit();
            return response()->json(['message' => 'پرداخت قبض تایید شد']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'متاسفانه صدور قبض با مشکل مواجه شد'] , 404);
        }
    }

    public function cancelBill($id)
    {
        try {
            DB::beginTransaction();
            $order = Order::where('orderable_id', $id)->where('orderable_type', Bill::class)->first();

            $user = \Auth::user();

            $status = $this->orderProcCanceled();
            ProcessStatus::create([
                'order_id' => $order->id,
                'creator_id' => $user->id,
                'created_date' => now(),
                'status_id' => $status->id,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'لغو قبض به درستی انجام نشد']);
        }

    }
}
