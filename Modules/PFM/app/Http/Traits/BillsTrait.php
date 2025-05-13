<?php

namespace Modules\PFM\app\Http\Traits;


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Http\Enums\ScriptTypesEnum;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\ScriptType;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Person;
use Modules\PFM\app\Http\Enums\ApplicationsForTablesEnum;
use Modules\PFM\app\Http\Enums\BookletStatusEnum;
use Modules\PFM\app\Http\Enums\LeviesListEnum;
use Modules\PFM\app\Http\Enums\LevyStatusEnum;
use Modules\PFM\app\Models\Bill;
use Modules\PFM\app\Models\BillItemProperty;
use Modules\PFM\app\Models\BillTariff;
use Modules\PFM\app\Models\Booklet;
use Modules\PFM\app\Models\BookletStatus;
use Modules\PFM\app\Models\Levy;
use Modules\PFM\app\Models\LevyBill;
use Modules\PFM\app\Models\LevyCircular;
use Modules\PFM\app\Models\LevyItem;
use Modules\PFM\app\Models\PfmCirculars;
use Modules\PFM\app\Models\PropApplication;
use Modules\PFM\app\Models\Tarrifs;
use Modules\PFM\Services\CardToCardPayment;
use Modules\PFM\Services\PaymentService;

trait BillsTrait
{
    use PersonTrait, UserTrait;

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
            LeviesListEnum::TABLIGHAT->value,
            LeviesListEnum::SUDURE_MOJAVEZE_EHDAS->value,
        ])) {
            $appID = $data['appID'];
            $fiscalYearID = $data['fiscal_year_id'];
            $ounitID = $data['ounit_id'];

            $circular = PfmCirculars::where('fiscal_year_id', $fiscalYearID)->first();
            $booklet = Booklet::where('pfm_circular_id', $circular->id)->where('ounit_id', $ounitID)->first();


            $bookletID = $booklet->id;

            $itemID = $data['itemID'];

            $application = PropApplication::find($appID);
            switch ($application->main_prop_type) {
                case "p_residential":
                    $p = $booklet->p_residential;
                    $areaPrice = $p * $application->adjustment_coefficient;

                    $tarrifs = Tarrifs::where('item_id', $itemID)->where('app_id', $appID)->where('booklet_id', $bookletID)->select('value')->first();
                    $filledData['coefficient'] = $tarrifs->value;
                    $filledData['areaPrice'] = $areaPrice;
                    break;
                case "p_commercial":
                    $p = $booklet->p_commercial;
                    $areaPrice = $p * $application->adjustment_coefficient;

                    $tarrifs = Tarrifs::where('item_id', $itemID)->where('app_id', $appID)->where('booklet_id', $bookletID)->select('value')->first();
                    $filledData['coefficient'] = $tarrifs->value;
                    $filledData['areaPrice'] = $areaPrice;
                    break;
                case "p_administrative":
                    $p = $booklet->p_administrative;
                    $areaPrice = $p * $application->adjustment_coefficient;

                    $tarrifs = Tarrifs::where('item_id', $itemID)->where('app_id', $appID)->where('booklet_id', $bookletID)->select('value')->first();
                    $filledData['coefficient'] = $tarrifs->value;
                    $filledData['areaPrice'] = $areaPrice;
                    break;
            }
        } else if (in_array($levy->name, [
            LeviesListEnum::ARZESHE_AFZODEH_OMRAN->value,
            LeviesListEnum::BAHAYE_KHEDMAT->value,
            LeviesListEnum::GHAT_DERAKHTAN->value,
            LeviesListEnum::MASHAGHEL_DAEM->value,
            LeviesListEnum::ARZESHE_AFZODEH_HADI->value,
        ])) {
            $itemID = $data['itemID'];
            $fiscalYearID = $data['fiscal_year_id'];
            $ounitID = $data['ounit_id'];

            $circular = PfmCirculars::where('fiscal_year_id', $fiscalYearID)->first();
            $booklet = Booklet::where('pfm_circular_id', $circular->id)->where('ounit_id', $ounitID)->first();

            $bookletID = $booklet->id;
            $tarrifs = Tarrifs::where('item_id', $itemID)->where('booklet_id', $bookletID)->select('value')->first();
            $filledData['areaPrice'] = 0;
            $filledData['coefficient'] = 0;
            $filledData['approvedTariff'] = $tarrifs->value;
        }
        return $filledData;
    }

    public function sendBillWithData($data)
    {
        $nationalCode = $data['nationalCode'];

        $person = Person::where('national_code', $nationalCode)->first();

        if (!$person) {
            $personType = $data['personType'];
            if ($personType == 1) {
                $naturalAndPerson = $this->naturalStore($data);
                $person = $naturalAndPerson->person;
                $personID = $naturalAndPerson->person->id;
                $data['personID'] = $personID;
            } else {
                $legalAndPerson = $this->legalStore($data);
                $person = $legalAndPerson->person;
                $personID = $legalAndPerson->person->id;
                $data['personID'] = $personID;
            }
        } else {
            $personType = $data['personType'];
            if ($personType == 1) {
                $this->optionalPersonAndNatualAndLegalUpdate($data, $person);
            } else {
                $this->optionalPersonAndNatualAndLegalUpdate($data, $person);
            }
        }

        $bill = $this->storeOneBill($data);


        $user = Auth::user();
        $payment = new PaymentService($bill->id, $user, $data['price'], $data['maxDays'], $data['discountAmount'], $person);
        $payment->makeUserCustomer();
        $payment->generateBill();

    }

    public function storeOneBill($data)
    {
        $tableDatas = json_decode($data['tableDatas']);


        $appID = $data['app_id'] ?? null;

        $circular = PfmCirculars::where('fiscal_year_id', $data['fiscal_year_id'])->first();
        $booklet = Booklet::where('pfm_circular_id', $circular->id)->where('ounit_id', $data['ounit_id'])->first();

        $data['booklet_id'] = $booklet->id;


        $tariff = Tarrifs::where('booklet_id', $data['booklet_id'])->where('item_id', $data['item_id'])->where('app_id', $appID)->first();


        $bill = Bill::create([
            'bank_account_id' => $data['bank_account_id'],
        ]);

//        foreach ($tableDatas as $tableData) {
//            LevyBill::create([
//                'levy_id' => $data['levy_id'],
//                'bill_id' => $bill->id,
//                'key' => $tableData->key,
//                'value' => $tableData->value,
//            ]);
//        }

        $billTarif = BillTariff::create([
            'bill_id' => $bill->id,
            'tariff_id' => $tariff->id,
        ]);


        foreach ($tableDatas as $tableData) {
            BillItemProperty::create([
                'bill_tariff_id' => $billTarif->id,
                'key' => $tableData->key,
                'value' => $tableData->value,
            ]);
        }


        return $bill;
    }

    public function generateBillsList($pageNum, $perPage, $ounits)
    {
        $query = Bill::query()
            ->join('orders', function ($join) {
                $join->on('orders.orderable_id', '=', 'pfm_bills.id')
                    ->where('orders.orderable_type', '=', Bill::class);
            })
            ->join('process_status', function ($join) {
                $join->on('process_status.order_id', '=', 'orders.id')
                    ->whereRaw('process_status.created_date = (SELECT MAX(created_date) FROM process_status WHERE order_id = orders.id)');
            })
            ->join('financial_status', function ($join) {
                $join->on('financial_status.order_id', '=', 'orders.id')
                    ->whereRaw('financial_status.created_date = (SELECT MAX(created_date) FROM financial_status WHERE order_id = orders.id)');
            })
            ->join('statuses as status_fin', 'financial_status.status_id', '=', 'status_fin.id')
            ->join('statuses as status_pro', 'process_status.status_id', '=', 'status_pro.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('persons', 'customers.person_id', '=', 'persons.id')
            ->join('invoices', 'orders.id', '=', 'invoices.order_id')
            ->join('pfm_bill_tariff', 'pfm_bills.id', '=', 'pfm_bill_tariff.bill_id')
            ->join('pfm_circular_tariffs', 'pfm_bill_tariff.tariff_id', '=', 'pfm_circular_tariffs.id')
            ->join('pfm_levy_items', 'pfm_circular_tariffs.item_id', '=', 'pfm_levy_items.id')
            ->join('pfm_levy_circular', 'pfm_levy_items.circular_levy_id', '=', 'pfm_levy_circular.id')
            ->join('pfm_levies', 'pfm_levy_circular.levy_id', '=', 'pfm_levies.id')
            ->leftJoin('pfm_circular_booklets', 'pfm_circular_tariffs.booklet_id', '=', 'pfm_circular_booklets.id')
            ->leftJoin('organization_units as booklet_ounit', 'pfm_circular_booklets.ounit_id', '=', 'booklet_ounit.id')
            ->leftJoin('discount_invoice', 'invoices.id', '=', 'discount_invoice.invoice_id')
            ->leftJoin('discounts', 'discount_invoice.discount_id', '=', 'discounts.id')
            ->select([
                'pfm_bills.id as bill_id',
                'status_fin.name as financial_status_name',
                'status_fin.class_name as financial_status_class_name',
                'status_pro.name as process_status_name',
                'status_pro.class_name as process_status_class_name',
                'persons.display_name as customer_name',
                'pfm_levies.name as levy_name',
                'orders.create_date as create_date',
                'persons.national_code as national_code',
                'orders.total_price as total_price',
                'discounts.value as discount_value',
                'booklet_ounit.id as ounit_id',
            ])
//            ->whereIn('booklet_ounit.id', $ounits)
            ->paginate($perPage, ['*'], 'page', $pageNum);

        return $query;
    }

    public function getBillData($id)
    {
        $query = Bill::query()
            ->join('orders', function ($join) {
                $join->on('orders.orderable_id', '=', 'pfm_bills.id')
                    ->where('orders.orderable_type', '=', Bill::class);
            })
            ->join('process_status', function ($join) {
                $join->on('process_status.order_id', '=', 'orders.id')
                    ->whereRaw('process_status.created_date = (SELECT MAX(created_date) FROM process_status WHERE order_id = orders.id)');
            })
            ->join('financial_status', function ($join) {
                $join->on('financial_status.order_id', '=', 'orders.id')
                    ->whereRaw('financial_status.created_date = (SELECT MAX(created_date) FROM financial_status WHERE order_id = orders.id)');
            })
            ->join('statuses as status_fin', 'financial_status.status_id', '=', 'status_fin.id')
            ->join('statuses as status_pro', 'process_status.status_id', '=', 'status_pro.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('persons', 'customers.person_id', '=', 'persons.id')
            ->join('invoices', 'orders.id', '=', 'invoices.order_id')
            ->leftJoin('ps_payment' , 'invoices.id' , '=' , 'ps_payment.invoice_id')
            ->leftJoin('card_to_cards' , 'ps_payment.ps_paymentable_id' , '=' , 'card_to_cards.id')
            ->leftJoin('discount_invoice', 'invoices.id', '=', 'discount_invoice.invoice_id')
            ->leftJoin('discounts', 'discount_invoice.discount_id', '=', 'discounts.id')
            ->join('pfm_bill_tariff', 'pfm_bills.id', '=', 'pfm_bill_tariff.bill_id')
            ->join('pfm_circular_tariffs', 'pfm_bill_tariff.tariff_id', '=', 'pfm_circular_tariffs.id')
            ->join('pfm_levy_items', 'pfm_circular_tariffs.item_id', '=', 'pfm_levy_items.id')
            ->join('pfm_levy_circular', 'pfm_levy_items.circular_levy_id', '=', 'pfm_levy_circular.id')
            ->join('pfm_levies', 'pfm_levy_circular.levy_id', '=', 'pfm_levies.id')
            ->join('pfm_circular_booklets', 'pfm_circular_tariffs.booklet_id', '=', 'pfm_circular_booklets.id')
            ->join('organization_units', 'pfm_circular_booklets.ounit_id', '=', 'organization_units.id')
            ->join('bnk_bank_accounts', 'pfm_bills.bank_account_id', '=', 'bnk_bank_accounts.id')
            ->join('bnk_bank_branches', 'bnk_bank_accounts.branch_id', '=', 'bnk_bank_branches.id')
            ->join('bnk_banks', 'bnk_bank_branches.bank_id', '=', 'bnk_banks.id')
            ->select([
                'pfm_bills.id as bill_id',
                'status_fin.name as financial_status_name',
                'status_fin.class_name as financial_status_class_name',
                'status_pro.name as process_status_name',
                'status_pro.class_name as process_status_class_name',
                'persons.display_name as customer_name',
                'pfm_levies.name as levy_name',
                'persons.national_code as national_code',
                'orders.total_price as total_price',
                'discounts.value as discount_value',
                'invoices.due_date as due_date',
                'organization_units.name as ounit_name',
                'bnk_banks.name as bank_name',
                'bnk_bank_accounts.account_number as account_number',
                'orders.create_date as create_date',
                'card_to_cards.reference_number as reference_number',
            ])
            ->where('pfm_bills.id', $id)
            ->first();

        return $query;


    }

    public function billConfirmation($data, $id, $user)
    {
        $query = Bill::query()
            ->join('orders', function ($join) {
                $join->on('orders.orderable_id', '=', 'pfm_bills.id')
                    ->where('orders.orderable_type', '=', Bill::class);
            })
            ->join('invoices', 'orders.id', '=', 'invoices.order_id')
            ->select([
                'invoices.id as invoice_id',
            ])
            ->where('pfm_bills.id', $id)
            ->first();

        $invoiceID = $query->invoice_id;
        $payment = new CardToCardPayment($invoiceID, $data['fileID'], $data['refNumber'], $user);
        $payment->makeCardToCard();

    }

}
