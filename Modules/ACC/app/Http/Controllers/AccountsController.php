<?php

namespace Modules\ACC\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ACC\app\Http\Enums\AccountLayerTypesEnum;
use Modules\ACC\app\Http\Enums\DocumentStatusEnum;
use Modules\ACC\app\Http\Enums\DocumentTypeEnum;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Models\Account;
use Modules\ACC\app\Models\AccountCategory;
use Modules\ACC\app\Models\Article;
use Modules\ACC\app\Resources\AccountUsageResource;
use Modules\BNK\app\Http\Enums\ChequeStatusEnum;
use Modules\BNK\app\Models\BankAccount;
use Modules\BNK\app\Models\BnkChequeStatus;
use Modules\BNK\app\Models\Cheque;
use Modules\BNK\app\Models\ChequeBook;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Legal;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Validator;

class AccountsController extends Controller
{
    use AccountTrait, PersonTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ounitID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        $accs = AccountCategory::leftJoinRelationship('accounts', function ($join) use ($data) {
            $join
                ->where(function ($query) use ($data) {
                    $query->where('acc_accounts.ounit_id', $data['ounitID'])
                        ->orWhereNull('acc_accounts.ounit_id');
                })
                ->withGlobalScopes();
        })
            ->select([
                'acc_accounts.id as id',
                'acc_accounts.name as title',
                'acc_accounts.segment_code as code',
                'acc_accounts.chain_code as chainedCode',
                'acc_accounts.parent_id as parent_id',
                'acc_account_categories.id as categoryID',
                'acc_account_categories.name as accountCategory',
            ])
            ->orderBy('acc_accounts.chain_code')
            ->get();
        $accs = $accs->groupBy('accountCategory')->map(function ($item) {
            return $item->toHierarchy();
        });

        return response()->json(['data' => $accs]);
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }
        $account = Account::find($id);
        if (!$account) {
            return response()->json(['message' => 'رکورد مورد نظر یافت نشد'], 404);
        }
        try {

            DB::beginTransaction();
            $account->name = $request->name;
            $account->save();
            DB::commit();
            return response()->json(['data' => $account]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();

        $validate = Validator::make($data, [
            'ounitID' => 'required',
            'name' => 'required',
            'parentChainCode' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $parentAccount = Account::where('chain_code', $data['parentChainCode'])->first();
//            $largest = Account::where('chain_code', 'LIKE', $data['parentChainCode'] . '%')
////                ->where('ounit_id', $data['ounitID'])
//                ->where(function ($query) use ($data) {
//                    $query->where('ounit_id', $data['ounitID'])
//                        ->orWhereNull('ounit_id');
//                })
//                ->orderByRaw('CAST(chain_code AS UNSIGNED) DESC')
//                ->withoutGlobalScopes()
//                ->activeInactive()
//                ->first();
            $largest = $this->latestAccountByChainCode($parentAccount->chain_code, $data['ounitID']);
            $data['segmentCode'] = addWithLeadingZeros($largest?->segment_code ?? '000', 1);


            $account = $this->storeAccount($data, $parentAccount);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error', 'error'], 500);
        }

        return response()->json($account);
    }

    public function getAddAccountBaseInfo()
    {
        $result['categories'] = AccountCategory::all();

        return response()->json(['data' => $result]);
    }

    public function deleteAccount($id)
    {


        try {
            DB::beginTransaction();
            $status = $this->inactiveAccountStatus();
            $acc = Account::with('descendantsAndSelf')->find($id);
            if (!$acc) {
                return response()->json(['message' => 'رکورد مورد نظر یافت نشد'], 404);
            }
            $descendants = $acc->descendantsAndSelf;

            $descendants->each(function ($item) use ($status) {
                $item->status_id = $status->id;
                $item->save();
            });

            DB::commit();
            return response()->json(['message' => 'با موفقیت حذف شد'], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'error'], 500);
        }
    }

    public function getFirstEmptyCheck(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'accountID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        $cheques = Cheque::
        joinRelationship('chequeBook.account', [
            'account' => function ($join) use ($data) {
                $join
                    ->where(Account::getTableName() . '.id', $data['accountID'])
                    ->where(Account::getTableName() . '.entity_type', BankAccount::class);
            }
        ])
            ->joinRelationship('statuses', [
                'statuses' => function ($join) {
                    $join
                        ->whereRaw(BnkChequeStatus::getTableName() . '.create_date = (SELECT MAX(create_date) FROM ' . BnkChequeStatus::getTableName() . ' WHERE cheque_id = bnk_cheques.id)')
                        ->where('statuses.name', '=', ChequeStatusEnum::BLANK->value);
                }
            ])
            ->addSelect([
                ChequeBook::getTableName() . '.cheque_series as series',
            ])
            ->with('bankAccount.bank')
            ->orderBy('segment_number', 'asc')
            ->get();

        $cheques = $cheques->map(function ($cheque) {
            return [
                'chequeID' => $cheque->id,
                'segmentNumber' => $cheque->segment_number,
                'series' => $cheque->series,
                'bank' => [
                    'name' => $cheque->bankAccount->bank->name,
                ],
            ];
        });


        return response()->json(['data' => $cheques], 200);

    }

    public function storeCreditAccount(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'personType' => 'required',
            'nationalCode' => 'required',
            'ounitID' => 'required',
            'name' => 'sometimes',
            'firstName' => 'sometimes',
            'lastName' => 'sometimes',
            'parentChainCode' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }


        $prefix = match ($data['parentChainCode']) {
            '31001' => 'حساب پرداختنی',
            '11901' => 'علی الحساب',
            '11003' => 'تنخواه گردان',
            '11201' => 'حساب دریافتنی',
            '31201' => '10 درصد حسن انجام کار',
            '31204' => '5 درصد حسن انجام کار',
            '31203' => 'سپرده مناقصه و مزایده',
            '31141' => 'کسورات قانونی (حق بیمه / مالیات پرداختنی)',
            '31140' => '7% + 7،1/9% حق بیمه',
            '31139' => '15 % + 15،1/9% حق بیمه',
            '31138' => '6/6% حق بیمه',
            default => '',
        };

        try {
            DB::beginTransaction();

            if ($data['personType'] == 1) {
                $person = Person::where('national_code', $data['nationalCode'])
                    ->where('personable_type', Natural::class)
                    ->first();
                if (is_null($person)) {
                    $naturalPerson = $this->naturalStore($data);
                    $person = $naturalPerson->person;
                }
            } else {
                $person = Person::where('national_code', $data['nationalCode'])
                    ->where('personable_type', Legal::class)
                    ->first();
                if (is_null($person)) {
                    $legalPerson = $this->legalStore($data);
                    $person = $legalPerson->person;
                }
            }

            $pType = $data['personType'] == 1 ? '(حقیقی)' : '(حقوقی)';

            $parentAccount = Account::where('chain_code', $data['parentChainCode'])->first();

//            $largest = Account::where('chain_code', 'LIKE', $data['parentChainCode'] . '%')
////                ->where('ounit_id', $data['ounitID'])
//                ->where(function ($query) use ($data) {
//                    $query->where('ounit_id', $data['ounitID'])
//                        ->orWhereNull('ounit_id');
//                })
//                ->orderByRaw('CAST(chain_code AS UNSIGNED) DESC')
//                ->withoutGlobalScopes()
//                ->activeInactive()
//                ->first();
            $largest = $this->latestAccountByChainCode($parentAccount->chain_code, $data['ounitID']);


            $accData = [
                'entityID' => $person->id,
                'entityType' => $person->personable_type,
                'name' => $prefix . ' ' . $person->display_name . ' ' . $pType . ' - ' . $person->national_code,
                'ounitID' => $data['ounitID'],
                'segmentCode' => addWithLeadingZeros($largest?->segment_code ?? '000', 1),
                'chainCode' => $parentAccount->chain_code . addWithLeadingZeros($largest?->segment_code ?? '000', 1),
                'categoryID' => $parentAccount->category_id,
            ];


            $this->firstOrStoreAccount($accData, $parentAccount, 1);

            DB::commit();
            return response()->json(['success' => 'با موفقیت اضافه شد'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error'], 500);
        }
    }

    public function personExistenceAndHasAccount(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'nationalCode' => 'required',
            'ounitID' => 'required',
            'parentChainCode' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        $person = Person::where('national_code', $data['nationalCode'])
            ->first();
        $personExists = !is_null($person);
        $personAccount = !is_null($person) ? $person->account()->where('entity_type', $person->personable_type)
            ->where('ounit_id', $data['ounitID'])
            ->where('chain_code', 'LIKE', $data['parentChainCode'] . '%')
            ->with(['ancestors', 'accountCategory'])->first() : null;
        $personName = $person?->display_name;


        $result = [
            'personExists' => $personExists,
            'account' => !is_null($personAccount) ? [
                'chain_code' => $personAccount->chain_code,
                'ancestors' => $personAccount->ancestors->pluck('name'),
                'category' => $personAccount->accountCategory->name,
            ] : null,
            'personName' => $personName,
        ];

        return response()->json($result);
    }

    public function accountUsageReport(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'ounitID' => 'required',
            'accountID' => 'required',
            'fiscalYearID' => 'required',
            'startDate' => 'sometimes',
            'endDate' => 'sometimes',
            'startDocNum' => 'sometimes',
            'endDocNum' => 'sometimes',
            'opening' => 'sometimes',
            'closing' => 'sometimes',
            'temporary' => 'sometimes',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }
        try {
            if (isset($data['startDate'])) {
                $startDate = convertJalaliPersianCharactersToGregorian($data['startDate']);
                $endDate = convertJalaliPersianCharactersToGregorian($data['endDate']);
                $searchByDate = true;
                $searchByDocNum = false;
                $startDocNum = null;
                $endDocNum = null;
            } else {
                $startDocNum = $data['startDocNum'];
                $endDocNum = $data['endDocNum'];
                $searchByDate = false;
                $searchByDocNum = true;
                $startDate = null;
                $endDate = null;

            }

            $periodTypes = [
                DocumentTypeEnum::NORMAL->value,
                ...($data['temporary'] ?? false ? [DocumentTypeEnum::TEMPORARY->value] : []),
                ...($data['opening'] ?? false ? [DocumentTypeEnum::OPENING->value] : []),
                ...($data['closing'] ?? false ? [DocumentTypeEnum::CLOSING->value] : []),
            ];

            $results = DB::table(DB::raw('(
                WITH RECURSIVE descendants AS (
                    SELECT id, id as root_id
                    FROM acc_accounts
                    WHERE id ="' . $data['accountID'] . '"
                    UNION ALL
                    SELECT a.id, d.root_id
                    FROM acc_accounts a
                    INNER JOIN descendants d ON a.parent_id = d.id
                    WHERE (a.ounit_id = ' . $data['ounitID'] . ' OR a.ounit_id IS NULL)
                        )
                SELECT * FROM descendants
            ) as descendants'))
                ->join('acc_articles', 'acc_articles.account_id', '=', 'descendants.id')
                ->leftJoin('bnk_transactions', 'acc_articles.transaction_id', '=', 'bnk_transactions.id')
                ->join('acc_documents', 'acc_documents.id', '=', 'acc_articles.document_id')
                // Join the pivot table for statuses. This table contains the create_date and status_name.
                ->join('accDocument_status', 'accDocument_status.document_id', '=', 'acc_documents.id')
                ->join('statuses', 'accDocument_status.status_id', '=', 'statuses.id')
                ->join('acc_accounts as root_account', 'root_account.id', '=', 'descendants.root_id')
                // Ensure we only get the latest status per document
                ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                // And only if that latest status has the name "active"
                ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value)
                ->where('acc_documents.ounit_id', $data['ounitID'])
                ->where('acc_documents.fiscal_year_id', $data['fiscalYearID'])
                ->whereIntegerInRaw('acc_documents.document_type_id', $periodTypes)
                ->when($searchByDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('acc_documents.document_date', [$startDate, $endDate]);
                })
                ->when($searchByDocNum, function ($query) use ($startDocNum, $endDocNum) {
                    $query->whereBetween('acc_documents.document_number', [$startDocNum, $endDocNum]);
                })
                ->orderBy('acc_documents.document_date', 'asc')
                ->select(
                    [
                        'descendants.root_id',
                        'root_account.name',
                        'root_account.chain_code',
                        'acc_articles.credit_amount',
                        'acc_articles.debt_amount',
                        'acc_documents.description as doc_description',
                        'acc_documents.document_number as document_number',
                        'acc_documents.document_date as document_date',
                        'bnk_transactions.tracking_code as tracking_code',

                    ]
                )
                ->get();


            $credit = 0;
            $debt = 0;

            $results->each(function ($row) use (&$credit, &$debt) {
                $credit += $row->credit_amount;
                $debt += $row->debt_amount;
                $row->remaining = (int)abs($credit - $debt);

            });


            return AccountUsageResource::collection($results);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function accountIndexByType(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'balanceType' => 'required',
            'ounitID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $accountableType = AccountLayerTypesEnum::getLayerByID($data['balanceType']);

        $accounts = Account::where('accountable_type', $accountableType)
            ->withoutGlobalScopes()
//            ->activeInactive()
            ->where(function ($query) use ($request) {
                $query->where('ounit_id', $request->ounitID)
                    ->orWhereNull('ounit_id');
            })
            ->get([
                'id',
                'chain_code',
                'name',
            ]);

        return response()->json([
            'data' => $accounts,
        ]);
    }

    public function accountRemainingValue(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'accountID' => 'required',
            'ounitID' => 'required',
            'fiscalYearID' => 'required',
            'docNum' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }
        try {
            $remaining = Article::joinRelationship('document', function ($join) use ($data) {
                $join->where('ounit_id', '=', $data['ounitID'])
                    ->where('fiscal_year_id', '=', $data['fiscalYearID'])
                    ->where('document_number', '<=', $data['docNum'])
                    ->join('accDocument_status', 'accDocument_status.document_id', '=', 'acc_documents.id')
                    ->join('statuses', 'accDocument_status.status_id', '=', 'statuses.id')
                    ->whereRaw('accDocument_status.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                    ->where('statuses.name', '!=', DocumentStatusEnum::DELETED->value);

            })
                ->where('account_id', '=', $data['accountID'])
                ->select([
                    DB::raw('SUM(credit_amount) - SUM(debt_amount) as remaining'),
                ])
                ->first();

            if ($remaining->remaining > 0) {
                $status = 'بس';
                $value = $remaining->remaining;

            } else if ($remaining->remaining < 0) {
                $status = 'بد';
                $value = abs($remaining->remaining);
            } else {
                $status = '-';
                $value = 0;
            }

            return response()->json(['value' => $value, 'status' => $status]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
