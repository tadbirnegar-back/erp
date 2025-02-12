<?php

namespace Modules\BNK\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Modules\ACC\app\Http\Traits\AccountTrait;
use Modules\ACC\app\Models\Account;
use Modules\ACMS\app\Http\Enums\AccountantScriptTypeEnum;
use Modules\BNK\app\Http\Enums\BankAccountTypeEnum;
use Modules\BNK\app\Http\Traits\BankTrait;
use Modules\BNK\app\Models\Bank;
use Modules\BNK\app\Models\BankAccount;
use Modules\BNK\app\Models\BankAccountCard;
use Modules\BNK\app\Models\ChequeBook;
use Modules\BNK\app\Resources\BankAccountListResource;
use Modules\BNK\app\Resources\BankAccountShowResource;
use Modules\OUnitMS\app\Models\StateOfc;
use Validator;

class BankAccountController extends Controller
{
    use BankTrait, AccountTrait;

    public function index(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($request->all(), [
            'ounitID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }
        $bankAccount = $this->bankAccountIndex($data);

        return BankAccountListResource::collection($bankAccount);

    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'branchID' => 'required',
            'accountNumber' => 'required',
            'accountTypeID' => 'required',
            'registerDate' => 'sometimes',
            'ibanNumber' => 'required',
            'ounitID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $data = $request->all();
            $bankAccount = $this->storeBankAccount($data);

            $bank = $bankAccount->bank;
            $className = get_class($bank);

            $acc = Account::where('entity_type', $className)
                ->where('entity_id', $bank->id)
                ->first();

            $data = [
                'name' => ' حساب ' . $bankAccount->account_type_id->getLabel() . ' ' . $bank->name . ' ' . $bankAccount->bankBranch->name . ' ' . $bankAccount->account_number,
                'ounitID' => $bankAccount->ounit_id,
                'segmentCode' => '001',
                'entityType' => get_class($bankAccount),
                'entityID' => $bankAccount->id,
            ];
            $accBankAccount = $this->storeAccount($data, $acc);

            DB::commit();
            return response()->json(['data' => $bankAccount]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage(), $e->getTrace()], 500);
        }
    }

    public function addBaseInfo()
    {
        $banks = Bank::with('bankBranches')->get();

        return response()->json(['data' => [
            'banks' => $banks,
            'accountTypes' => BankAccountTypeEnum::getAllLabelAndValues(),
        ]
        ]);
    }

    public function show($id)
    {

        $bankAccount = BankAccount::with(['bankBranch.bank.logo', 'latestStatus', 'chequeBooks.latestStatus', 'chequeBooks.cheques.latestStatus', 'accountCards.latestStatus', 'ounit' => function ($query) {
            $query->with(['village', 'ancestors' => function ($query) {
                $query->where('unitable_type', '!=', StateOfc::class);
            }]);
        }])
            ->find($id);

        $user = auth()->user();
        $user->load(['activeRecruitmentScripts' => function ($query) use ($bankAccount) {
            $query
                ->where('organization_unit_id', $bankAccount->ounit_id)
                ->whereHas('scriptType', function ($query) {
                    $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
                });
        }]);

        if ($user->activeRecruitmentScripts->isEmpty()) {
            return response()->json(['error' => 'شما دسترسی برای مشاهده این بخش را ندارید'], 403);

        }

        return BankAccountShowResource::make($bankAccount)->additional(['cardStatusList' => BankAccountCard::GetAllStatuses()->get(), 'chequeBookStatusList' => ChequeBook::GetAllStatuses()->get()]);
    }

    public function edit($id)
    {

        $bankAccount = BankAccount::with(['bankBranch.bank.logo', 'ounit'])->find($id);

        $user = auth()->user();
        $user->load(['activeRecruitmentScripts' => function ($query) use ($bankAccount) {
            $query
                ->where('organization_unit_id', $bankAccount->ounit_id)
                ->whereHas('scriptType', function ($query) {
                    $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
                });
        }]);

        if ($user->activeRecruitmentScripts->isEmpty()) {
            return response()->json(['error' => 'شما دسترسی برای مشاهده این بخش را ندارید'], 403);

        }

        return BankAccountShowResource::make($bankAccount)->additional([
                'banks' => Bank::with('bankBranches')->get(),
                'accountTypes' => BankAccountTypeEnum::getAllLabelAndValues(),
            ]
        );
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'branchID' => 'required',
            'accountNumber' => 'required',
            'accountTypeID' => 'required',
            'registerDate' => 'sometimes',
            'ibanNumber' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $data = $request->all();
            $bankAccount = BankAccount::find($id);
            $bankAccount = $this->updateBankAccount($data, $bankAccount);

            DB::commit();
            return response()->json(['data' => $bankAccount]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $bankAccount = BankAccount::
        where('id', $id)
            ->first();

        $user = auth()->user();
        $user->load(['activeRecruitmentScripts' => function ($query) use ($bankAccount) {
            $query->where('organization_unit_id', $bankAccount->ounit_id)
                ->whereHas('scriptType', function ($query) {
                    $query->where('title', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
                });
        }]);

        if ($user->activeRecruitmentScripts->isEmpty()) {
            return response()->json(['error' => 'شما دسترسی برای مشاهده این بخش را ندارید'], 403);

        }

        $deleteStatus = $this->bankAccountDeactivateStatus();

        $bankAccount->statuses()->attach($deleteStatus->id);

        return response()->json(['message' => 'حساب بانکی با موفقیت حذف شد'], 200);
    }
}
