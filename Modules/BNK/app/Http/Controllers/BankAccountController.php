<?php

namespace Modules\BNK\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ACMS\app\Http\Enums\AccountantScriptTypeEnum;
use Modules\BNK\app\Models\Bank;
use Modules\BNK\app\Models\BankAccount;
use Modules\OUnitMS\app\Models\StateOfc;
use Validator;

class BankAccountController extends Controller
{

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'bankID' => 'required',
            'branchID' => 'required',
            'accountNumber' => 'required',
            'accountName' => 'required',
            'accountType' => 'required',
            'accountStatus' => 'required',
            'ounitID' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }
    }

    public function addBaseInfo()
    {
        $banks = Bank::with('bankBranches')->get();

        return response()->json(['data' => $banks]);
    }

    public function show($id)
    {

        $bankAccount = BankAccount::with(['bankBranch.bank.logo', 'latestStatus', 'chequeBooks.latestStatus', 'accountCards.latestStatus', 'ounit' => function ($query) {
            $query->with(['village', 'ancestors' => function ($query) {
                $query->where('unitable_type', '!=', StateOfc::class);
            }]);
        }])
            ->where('id', $id)
            ->first();

        $user = auth()->user();
        $user->load(['activeRecruitmentScripts' => function ($query) use ($bankAccount) {
            $query->where('organization_unit_id', $bankAccount->ounit_id)
                ->whereHas('scriptType', function ($query) {
                    $query->where('name', AccountantScriptTypeEnum::ACCOUNTANT_SCRIPT_TYPE->value);
                });
        }]);

        if ($user->activeRecruitmentScripts->isEmpty()) {
            return response()->json(['error' => 'شما دسترسی برای مشاهده این بخش را ندارید'], 403);

        }
    }

    public function storeChequeAndCheques(Request $request)
    {
        $data = $request->all();
        $data['userID'] = auth()->user()->id;

        $validate = Validator::make($request->all(), [
            'accountID' => 'required',
            'count' => 'required',
            'series' => 'required',

        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }


    }
}
