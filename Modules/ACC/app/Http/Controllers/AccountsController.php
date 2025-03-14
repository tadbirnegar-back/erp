<?php

namespace Modules\ACC\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Models\Account;
use Modules\ACC\app\Models\AccountCategory;
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
            'segmentCode' => 'required',
            'categoryID' => 'sometimes',
            'parentID' => 'sometimes',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        try {
            DB::beginTransaction();
            if (isset($data['parentID'])) {
                $parent = Account::find($data['parentID']);
            } else {
                $parent = null;
            }
            $account = $this->storeAccount($data, $parent);
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
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

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

            $largest = Account::where('chain_code', 'LIKE', '310001%')
//                ->where('entity_type', $person->personable_type)
                ->where('ounit_id', $data['ounitID'])
                ->orderByRaw('CAST(chain_code AS UNSIGNED) DESC')
                ->first();

            $accData = [
                'entityID' => $person->id,
                'entityType' => $person->personable_type,
                'name' => $person->display_name . ' - ' . $person->national_code,
                'ounitID' => $data['ounitID'],
                'segmentCode' => addWithLeadingZeros($largest?->segment_code ?? '000', 1)
            ];

            $parentAccount = Account::where('name', 'حسابهای پرداختنی تجاری')->where('chain_code', 310001)->first();

            $this->storeAccount($accData, $parentAccount);

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
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        $person = Person::where('national_code', $data['nationalCode'])
            ->first();
        $personExists = !is_null($person);
        $personAccount = !is_null($person) ? $person->account()->where('entity_type', $person->personable_type)->where('ounit_id', $data['ounitID'])->with(['ancestors', 'accountCategory'])->first() : null;
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
}
